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
        let container = document.getElementById('magkom-toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'magkom-toast-container';
            container.className = 'fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4';
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
                'ancaman_keamanan'
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
     * Menampilkan notifikasi kartu melayang (Toast) sesuai Figma Design System.
     */
    showToast(title, message, eventType, actionUrl) {
        if (!this.toastContainer) this.ensureToastContainer();

        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-white border border-slate-200/80 rounded-2xl shadow-xl p-4 flex gap-3.5 transform transition-all duration-300 ease-out translate-y-4 opacity-0';

        // Konfigurasi ikon dan warna berdasarkan tipe event
        const config = this.getEventVisualConfig(eventType);

        toast.innerHTML = `
            <div class="flex-shrink-0 w-10 h-10 rounded-xl flex items-center justify-center ${config.bgClass} ${config.textClass}">
                ${config.icon}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <h4 class="text-xs font-bold text-slate-900 tracking-tight leading-snug">${this.escapeHtml(title)}</h4>
                    <span class="text-[10px] text-slate-400 font-medium">Baru saja</span>
                </div>
                <p class="text-xs text-slate-600 mt-1 leading-relaxed">${this.escapeHtml(message)}</p>
                ${actionUrl ? `
                    <div class="mt-2.5 flex items-center gap-2">
                        <a href="${actionUrl}" class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-900 hover:bg-slate-800 text-white text-[11px] font-semibold rounded-lg shadow-sm transition">
                            Lihat Rincian &rarr;
                        </a>
                    </div>
                ` : ''}
            </div>
            <button type="button" class="close-btn text-slate-400 hover:text-slate-600 self-start p-1 -mr-1 -mt-1 rounded-lg transition" aria-label="Tutup">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        `;

        this.toastContainer.appendChild(toast);

        // Animasi kemunculan
        requestAnimationFrame(() => {
            toast.classList.remove('translate-y-4', 'opacity-0');
        });

        // Tombol tutup
        const closeBtn = toast.querySelector('.close-btn');
        const dismiss = () => {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        };
        closeBtn.addEventListener('click', dismiss);

        // Auto dismiss setelah 7 detik
        setTimeout(dismiss, 7000);
    }

    /**
     * Konfigurasi visual ikon dan warna untuk event.
     */
    getEventVisualConfig(eventType) {
        switch (eventType) {
            case 'pendaftaran_baru':
            case 'kuota_berubah':
                return {
                    bgClass: 'bg-blue-50 border border-blue-200/60',
                    textClass: 'text-blue-600',
                    icon: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>'
                };
            case 'status_pendaftaran':
            case 'logbook_direviu':
                return {
                    bgClass: 'bg-emerald-50 border border-emerald-200/60',
                    textClass: 'text-emerald-600',
                    icon: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>'
                };
            case 'logbook_baru':
            case 'tugas_dikumpulkan':
                return {
                    bgClass: 'bg-indigo-50 border border-indigo-200/60',
                    textClass: 'text-indigo-600',
                    icon: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>'
                };
            case 'tugas_baru':
                return {
                    bgClass: 'bg-amber-50 border border-amber-200/60',
                    textClass: 'text-amber-600',
                    icon: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>'
                };
            case 'ancaman_keamanan':
            default:
                return {
                    bgClass: 'bg-rose-50 border border-rose-200/60',
                    textClass: 'text-rose-600',
                    icon: '<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>'
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
