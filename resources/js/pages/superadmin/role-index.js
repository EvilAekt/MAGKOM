/**
 * ========================================================
 * MAGKOM GENTELELLA DESIGN SYSTEM: SUPERADMIN ROLE JS
 * Halaman: Hak Akses dan Role Pengguna
 * Sistem Animasi Universal (Scroll-Craft + GSAP)
 * ========================================================
 */

import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();
    initPermissionModal();
    initDeleteConfirmations();
});

/**
 * Inisialisasi Modal Dialog Tambah Hak Akses
 */
function initPermissionModal() {
    const modal = document.getElementById('modalTambahPermission');
    const openBtn = document.getElementById('btnBukaModalTambah');
    const closeBtn = document.getElementById('btnTutupModal');
    const cancelBtn = document.getElementById('btnBatalModal');

    if (!modal) return;

    function openModal() {
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        const inputNama = modal.querySelector('input[name="nama_modul"]');
        if (inputNama) {
            setTimeout(() => inputNama.focus(), 150);
        }
    }

    function closeModal() {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    if (openBtn) {
        openBtn.addEventListener('click', (e) => {
            e.preventDefault();
            openModal();
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeModal();
        });
    }

    // Tutup saat klik area luar dialog
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Tutup dengan tombol Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
}

/**
 * Inisialisasi konfirmasi penghapusan modul hak akses kustom
 */
function initDeleteConfirmations() {
    const deleteForms = document.querySelectorAll('.form-delete-permission');

    deleteForms.forEach((form) => {
        form.addEventListener('submit', (e) => {
            const moduleName = form.getAttribute('data-module-name') || 'modul ini';
            const confirmed = window.confirm(
                `Apakah Anda yakin ingin menghapus hak akses "${moduleName}"?\n\nTindakan ini akan mencabut hak akses ini dari seluruh role yang terhubung.`
            );

            if (!confirmed) {
                e.preventDefault();
            }
        });
    });
}

