/**
 * Modul Detail Arsip Peserta SuperAdmin
 * - Penanganan tab navigasi segmented (Ringkasan, Tugas, Logbook, Administrasi)
 * - Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

export function switchArsipTab(tabId, btn) {
    document.querySelectorAll('.arsip-tab-pill').forEach(b => b.classList.remove('active'));
    document.querySelectorAll('.arsip-tab-pane').forEach(p => p.classList.remove('active'));
    if (btn) btn.classList.add('active');
    const target = document.getElementById(tabId);
    if (target) {
        target.classList.add('active');
    }
}

// Global backward compatibility
window.switchArsipTab = switchArsipTab;

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    document.addEventListener('click', (event) => {
        const tabBtn = event.target.closest('.arsip-tab-pill');
        if (tabBtn) {
            const tabId = tabBtn.dataset.tabTarget;
            if (tabId) {
                event.preventDefault();
                switchArsipTab(tabId, tabBtn);
            }
        }
    });
});

