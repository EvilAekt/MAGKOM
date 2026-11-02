/**
 * MagKom Design System - Toast & Alert Component
 * Mitigasi DOM XSS (SEC-02, OWASP Client-Side Security)
 */

export function dismissToast(btn) {
    const toast = btn.closest('.app-alert') || btn.closest('.app-toast');
    if (!toast) return;
    toast.classList.add('toast-hiding');
    setTimeout(() => toast.remove(), 300);
}

export const MagKomToast = {
    show({ type = 'success', title = '', message = '', duration = 5000 }) {
        let container = document.querySelector('.alerts-container');
        if (!container) {
            container = document.createElement('div');
            container.className = 'alerts-container';
            const mainContent = document.getElementById('mainContent') || document.body;
            mainContent.insertBefore(container, mainContent.firstChild);
        }

        const toast = document.createElement('div');
        toast.className = `app-toast app-alert alert-${type === 'error' ? 'error alert-danger' : type}`;
        toast.setAttribute('role', 'alert');

        const icons = {
            success: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>',
            warning: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
            info: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>',
            error: '<svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>'
        };

        const iconBadge = document.createElement('div');
        iconBadge.className = `toast-icon-badge alert-icon-badge ${type}`;
        iconBadge.innerHTML = icons[type] || icons.info;

        const content = document.createElement('div');
        content.className = 'toast-content alert-body';

        if (title) {
            const titleEl = document.createElement('div');
            titleEl.className = 'toast-title alert-title';
            titleEl.textContent = title;
            content.appendChild(titleEl);
        }

        if (message) {
            const descEl = document.createElement('div');
            descEl.className = 'toast-desc alert-desc';
            descEl.textContent = message;
            content.appendChild(descEl);
        }

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'toast-close-btn alert-close-btn';
        closeBtn.setAttribute('aria-label', 'Tutup notifikasi');
        closeBtn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>';
        closeBtn.addEventListener('click', () => dismissToast(closeBtn));

        toast.appendChild(iconBadge);
        toast.appendChild(content);
        toast.appendChild(closeBtn);

        container.appendChild(toast);

        if (duration > 0) {
            let timer = setTimeout(() => {
                toast.classList.add('toast-hiding');
                setTimeout(() => toast.remove(), 300);
            }, duration);

            toast.addEventListener('mouseenter', () => clearTimeout(timer));
            toast.addEventListener('mouseleave', () => {
                timer = setTimeout(() => {
                    toast.classList.add('toast-hiding');
                    setTimeout(() => toast.remove(), 300);
                }, 2500);
            });
        }
    },
    success(title, message) { this.show({ type: 'success', title, message }); },
    error(title, message) { this.show({ type: 'error', title, message }); },
    warning(title, message) { this.show({ type: 'warning', title, message }); },
    info(title, message) { this.show({ type: 'info', title, message }); }
};

if (typeof window !== 'undefined') {
    window.MagKomToast = MagKomToast;
    window.dismissToast = dismissToast;

    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alerts-container .app-alert, .alerts-container .app-toast, .toast-floating-container .app-toast');
        alerts.forEach(alert => {
            let timer = setTimeout(() => {
                alert.classList.add('toast-hiding');
                setTimeout(() => alert.remove(), 300);
            }, 5000);

            alert.addEventListener('mouseenter', () => clearTimeout(timer));
            alert.addEventListener('mouseleave', () => {
                timer = setTimeout(() => {
                    alert.classList.add('toast-hiding');
                    setTimeout(() => alert.remove(), 300);
                }, 3000);
            });
        });

        // Event delegation for dismiss buttons
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.alert-close-btn, .toast-close-btn, [data-action="dismiss-toast"]');
            if (btn) {
                dismissToast(btn);
            }
        });
    });
}

