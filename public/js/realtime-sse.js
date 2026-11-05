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
     * Memastikan kontainer toast ada di DOM dan selalu berposisi fixed mengambang.
     */
    ensureToastContainer() {
        let container = document.getElementById('toastFloatingContainer') ||
            document.querySelector('.toast-floating-container') ||
            document.getElementById('magkom-toast-container') ||
            document.querySelector('.alerts-container') ||
            document.getElementById('app-toast-container');

        if (!container) {
            container = document.createElement('div');
            container.id = 'magkom-toast-container';
            container.className = 'magkom-toast-container toast-floating-container alerts-container';
            document.body.appendChild(container);
        }

        // Garansi posisi fixed agar tidak pernah ikut dalam flow flex body / merusak layout grid
        if (container) {
            container.style.setProperty('position', 'fixed', 'important');
            container.style.setProperty('top', '24px', 'important');
            container.style.setProperty('right', '24px', 'important');
            container.style.setProperty('z-index', '99999', 'important');
            container.style.setProperty('pointer-events', 'none', 'important');
            container.style.setProperty('display', 'flex', 'important');
            container.style.setProperty('flex-direction', 'column', 'important');
            container.style.setProperty('max-width', '420px', 'important');
            container.style.setProperty('width', 'calc(100vw - 48px)', 'important');
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
    showToast(title, message, eventType, actionUrl, data = {}) {
        if (!this.toastContainer) this.ensureToastContainer();
        if (!this.toastContainer) return;

        const config = this.getEventVisualConfig(eventType, data);
        const toast = document.createElement('div');
        toast.className = `app-toast app-alert ${config.alertClass || 'alert-info'}`;
        toast.setAttribute('role', 'alert');

        const safeActionUrl = actionUrl && (actionUrl.startsWith('/') || actionUrl.startsWith('http://') || actionUrl.startsWith('https://'))
            ? this.escapeHtml(actionUrl)
            : null;

        toast.innerHTML = `
            <div class="toast-icon-badge alert-icon-badge ${config.badgeType || 'info'}">
                ${config.icon}
            </div>
            <div class="toast-content alert-body">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 8px;">
                    <div class="toast-title alert-title">${this.escapeHtml(title)}</div>
                    <span style="font-size: 0.6875rem; color: #94A3B8; font-weight: 500; white-space: nowrap;">Baru saja</span>
                </div>
                <div class="toast-desc alert-desc">${this.escapeHtml(message)}</div>
                ${safeActionUrl ? `
                    <div style="margin-top: 8px;">
                        <a href="${safeActionUrl}" rel="noopener noreferrer" class="btn-toast-action">
                            Lihat Rincian &rarr;
                        </a>
                    </div>
                ` : ''}
            </div>
            <button type="button" class="toast-close-btn alert-close-btn" aria-label="Tutup notifikasi">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        `;

        this.toastContainer.appendChild(toast);

        let isDismissed = false;
        const dismiss = () => {
            if (isDismissed) return;
            isDismissed = true;
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        };

        const closeBtn = toast.querySelector('.alert-close-btn');
        if (closeBtn) {
            closeBtn.addEventListener('click', dismiss);
        }

        let timer = setTimeout(dismiss, 7000);
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', () => {
            clearTimeout(timer);
            timer = setTimeout(dismiss, 4000);
        });
    }

    /**
     * Konfigurasi visual ikon dan varian untuk event.
     */
    getEventVisualConfig(eventType, data = {}) {
        const isRevisi = data.status === 'perlu_revisi' || data.status_review === 'perlu_revisi';

        if (isRevisi) {
            return {
                alertClass: 'alert-warning',
                badgeType: 'warning',
                icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>'
            };
        }

        switch (eventType) {
            case 'pendaftaran_baru':
            case 'kuota_berubah':
            case 'tugas_baru':
            case 'tugas_dikumpulkan':
                return {
                    alertClass: 'alert-info',
                    badgeType: 'info',
                    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
                };
            case 'status_pendaftaran':
            case 'logbook_direviu':
            case 'progres_direviu':
                return {
                    alertClass: 'alert-success',
                    badgeType: 'success',
                    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>'
                };
            case 'ancaman_keamanan':
                return {
                    alertClass: 'alert-error alert-danger',
                    badgeType: 'error',
                    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'
                };
            default:
                return {
                    alertClass: 'alert-info',
                    badgeType: 'info',
                    icon: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>'
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
