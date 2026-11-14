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
    initEditPermissionModal();
    initSyncConfirmation();
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
 * Inisialisasi Modal Dialog Edit Hak Akses
 */
function initEditPermissionModal() {
    const modal = document.getElementById('modalEditPermission');
    const form = document.getElementById('formEditPermission');
    const closeBtn = document.getElementById('btnTutupEditModal');
    const cancelBtn = document.getElementById('btnBatalEditModal');
    const editBtns = document.querySelectorAll('.btn-edit-permission');

    if (!modal || !form) return;

    const inputNama = document.getElementById('editNamaModul');
    const inputSlug = document.getElementById('editSlug');
    const selectKategori = document.getElementById('editSelectKategori');
    const textareaDeskripsi = document.getElementById('editTextareaDeskripsi');
    const badgeSystem = document.getElementById('badgeEditSystemSlug');
    const helpSystem = document.getElementById('slugSystemHelp');

    function openEditModal(btn) {
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama') || '';
        const slug = btn.getAttribute('data-slug') || '';
        const kategori = btn.getAttribute('data-kategori') || 'Umum';
        const deskripsi = btn.getAttribute('data-deskripsi') || '';
        const isSystem = btn.getAttribute('data-system') === '1';

        // Set form action ke URL update
        form.action = `/superadmin/role/permission/${id}`;

        if (inputNama) inputNama.value = nama;
        if (inputSlug) {
            inputSlug.value = slug;
            if (isSystem) {
                inputSlug.setAttribute('disabled', 'disabled');
                inputSlug.style.backgroundColor = '#F3F4F6';
                inputSlug.style.color = '#6B7280';
                inputSlug.style.cursor = 'not-allowed';
            } else {
                inputSlug.removeAttribute('disabled');
                inputSlug.style.backgroundColor = '';
                inputSlug.style.color = '';
                inputSlug.style.cursor = '';
            }
        }

        if (badgeSystem) badgeSystem.style.display = isSystem ? 'inline-block' : 'none';
        if (helpSystem) helpSystem.style.display = isSystem ? 'block' : 'none';

        if (selectKategori) {
            selectKategori.value = kategori;
            if (selectKategori.value !== kategori) {
                const opt = document.createElement('option');
                opt.value = kategori;
                opt.textContent = kategori;
                opt.selected = true;
                selectKategori.appendChild(opt);
            }
        }

        if (textareaDeskripsi) textareaDeskripsi.value = deskripsi;

        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        if (inputNama) {
            setTimeout(() => inputNama.focus(), 150);
        }
    }

    function closeEditModal() {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    }

    editBtns.forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            openEditModal(btn);
        });
    });

    if (closeBtn) {
        closeBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeEditModal();
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            closeEditModal();
        });
    }

    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeEditModal();
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            closeEditModal();
        }
    });
}

/**
 * Inisialisasi konfirmasi sinkronisasi modul standar
 */
function initSyncConfirmation() {
    const formSync = document.getElementById('formSyncPermissions');
    if (!formSync) return;

    formSync.addEventListener('submit', (e) => {
        const confirmed = window.confirm(
            'Apakah Anda yakin ingin menyinkronkan modul sistem standar?\n\nSistem akan memastikan ke-13 modul bawaan MagKom terdaftar di basis data tanpa menghapus atau mengubah wewenang yang sudah Anda atur.'
        );
        if (!confirmed) {
            e.preventDefault();
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
