/**
 * MagKom Realtime Server-Sent Events (SSE) Client Engine
 * Mendukung RFC 8895, tab visibility optimization, toast alerts, dan badge counter updates.
 */
class MagKomRealtimeEngine {
    constructor() {
        this.streamUrl = null;
        this.eventSource = null;
        this.isConnected = false;
        this.retryTimeout = null;
        this.toastContainer = null;
    }

    /**
     * Inisialisasi engine dengan endpoint SSE stream URL.
     */
    init(streamUrl) {
        if (!window.EventSource) {
            console.warn('[MagKom SSE] Browser tidak mendukung HTML5 EventSource.');
            return;
        }

        this.streamUrl = streamUrl;
        this.ensureToastContainer();
        this.connect();
        this.registerVisibilityHandler();
    }

    /**
     * Memastikan kontainer toast ada di DOM.
     */
    ensureToastContainer() {
        let container = document.getElementById('app-toast-container') || document.getElementById('magkom-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'app-toast-container';
            container.className = 'fixed top-5 right-5 z-[9999] flex flex-col gap-3 pointer-events-none max-w-md w-full px-4';
            document.body.appendChild(container);
        }
        this.toastContainer = container;
    }

    /**
     * Membuka koneksi EventSource ke server.
     */
    connect() {
        if (this.isConnected || !this.streamUrl) return;

        try {
            this.eventSource = new EventSource(this.streamUrl);

            this.eventSource.onopen = () => {
                this.isConnected = true;
            };

            this.eventSource.onerror = (err) => {
                this.isConnected = false;
                // Browser akan otomatis reconnect sesuai petunjuk retry: 2500 dari server
            };

            // Daftarkan listener untuk tipe event spesifik
            const events = [
                'pendaftaran_baru',
                'status_pendaftaran',
                'kuota_berubah',
                'logbook_baru',
                'logbook_direviu',
                'tugas_baru',
                'tugas_dikumpulkan',
                'ancaman_keamanan',
                'telemetri_route'
            ];

            events.forEach((eventType) => {
                this.eventSource.addEventListener(eventType, (e) => {
                    this.handleEvent(eventType, e);
                });
            });

        } catch (error) {
            console.error('[MagKom SSE] Gagal menginisialisasi EventSource:', error);
        }
    }

    /**
     * Menutup koneksi secara graceful.
     */
    disconnect() {
        if (this.eventSource) {
            this.eventSource.close();
            this.eventSource = null;
            this.isConnected = false;
        }
    }

    /**
     * Mengatur koneksi berdasarkan visibilitas tab browser (menghemat energi & bandwidth).
     */
    registerVisibilityHandler() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                this.disconnect();
            } else {
                this.connect();
            }
        });
    }

    /**
     * Menangani event yang diterima dari server.
     */
    handleEvent(eventType, eventObj) {
        try {
            const data = JSON.parse(eventObj.data || '{}');

            // Dispatch global event untuk subscriber frontend (seperti Canvas Telemetri)
            window.dispatchEvent(new CustomEvent('magkom:realtime-event', { detail: { eventType, data } }));

            if (eventType === 'telemetri_route') {
                window.dispatchEvent(new CustomEvent('magkom:telemetry-route', { detail: data }));
                return; // Jangan tampilkan toast/chime untuk telemetri route reguler
            }

            this.playNotificationChime();
            this.showToast(data.title || 'Pemberitahuan Baru', data.message || '', eventType, data.action_url);

            // Perbarui badge counter jika disediakan di payload
            if (data.badge_target) {
                this.incrementBadge(data.badge_target);
            }
        } catch (err) {
            console.error('[MagKom SSE] Error parsing payload event:', err);
        }
    }

    /**
     * Menampilkan notifikasi kartu melayang (Toast) sesuai MagKom Design System.
     */
    showToast(title, message, eventType, actionUrl) {
        if (!this.toastContainer) this.ensureToastContainer();

        const config = this.getEventVisualConfig(eventType);
        const toast = document.createElement('div');
        toast.className = `app-toast ${config.variantClass} flex items-start gap-3.5 p-4 rounded-2xl shadow-xl transition-all duration-300 pointer-events-auto`;
        toast.setAttribute('role', 'alert');

        toast.innerHTML = `
            <div class="toast-icon-badge flex-shrink-0">
                ${config.icon}
            </div>
            <div class="toast-body flex-1 min-w-0 pt-0.5">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="toast-title text-sm font-bold tracking-tight">${this.escapeHtml(title)}</h4>
                    <span class="text-[10px] text-slate-400 dark:text-slate-500 font-medium whitespace-nowrap">Baru saja</span>
                </div>
                <div class="toast-desc text-xs mt-1 leading-relaxed">${this.escapeHtml(message)}</div>
                ${actionUrl ? `
                    <div class="mt-2.5">
                        <a href="${actionUrl}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white dark:bg-white dark:text-slate-900 dark:hover:bg-slate-100 text-[11px] font-semibold rounded-lg shadow-sm transition">
                            Lihat Rincian &rarr;
                        </a>
                    </div>
                ` : ''}
            </div>
            <button type="button" class="toast-close flex-shrink-0 p-1 -mr-1 -mt-1 rounded-lg text-slate-400 hover:text-slate-600 dark:text-slate-500 dark:hover:text-slate-300 transition" aria-label="Tutup notifikasi">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        this.toastContainer.appendChild(toast);

        const dismiss = () => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-10px) scale(0.98)';
            setTimeout(() => toast.remove(), 250);
        };

        const closeBtn = toast.querySelector('.toast-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', dismiss);
        }

        setTimeout(dismiss, 7000);
    }

    /**
     * Konfigurasi visual ikon dan varian untuk event.
     */
    getEventVisualConfig(eventType) {
        switch (eventType) {
            case 'pendaftaran_baru':
            case 'kuota_berubah':
                return {
                    variantClass: 'toast-info',
                    icon: '<svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                };
            case 'status_pendaftaran':
            case 'logbook_direviu':
                return {
                    variantClass: 'toast-success',
                    icon: '<svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>'
                };
            case 'logbook_baru':
            case 'tugas_dikumpulkan':
                return {
                    variantClass: 'toast-info',
                    icon: '<svg class="w-5 h-5 text-sky-600 dark:text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                };
            case 'tugas_baru':
                return {
                    variantClass: 'toast-warning',
                    icon: '<svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
                };
            case 'ancaman_keamanan':
            default:
                return {
                    variantClass: 'toast-error',
                    icon: '<svg class="w-5 h-5 text-rose-600 dark:text-rose-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>'
                };
        }
    }

    /**
     * Memperbarui badge counter pada halaman.
     */
    incrementBadge(selector) {
        const el = document.querySelector(selector);
        if (!el) return;

        const currentVal = parseInt(el.textContent.replace(/[^0-9]/g, ''), 10) || 0;
        el.textContent = currentVal + 1;
        el.classList.add('animate-pulse');
        setTimeout(() => el.classList.remove('animate-pulse'), 1500);
    }

    /**
     * Audio Chime lembut menggunakan Web Audio API murni (tanpa file audio eksternal).
     */
    playNotificationChime() {
        try {
            const AudioCtx = window.AudioContext || window.webkitAudioContext;
            if (!AudioCtx) return;
            const ctx = new AudioCtx();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
            osc.frequency.exponentialRampToValueAtTime(880.00, ctx.currentTime + 0.15); // A5

            gain.gain.setValueAtTime(0.04, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.3);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start();
            osc.stop(ctx.currentTime + 0.3);
        } catch (e) {
            // Abaikan jika browser memblokir autoplay audio sebelum interaksi pengguna
        }
    }

    escapeHtml(str) {
        return String(str || '').replace(/[&<>"']/g, (m) => {
            return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m];
        });
    }
}

// Inisialisasi global
window.MagKomRealtime = new MagKomRealtimeEngine();
