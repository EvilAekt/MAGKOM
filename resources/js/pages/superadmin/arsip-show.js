/**
 * Modul Detail Arsip Peserta SuperAdmin
 * - Penanganan tab navigasi segmented (Ringkasan, Tugas, Logbook, Administrasi)
 */
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

