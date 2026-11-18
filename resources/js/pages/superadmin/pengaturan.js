/**
 * MAGKOM SuperAdmin - Pengaturan & Konten Publik Landing Page
 * Penanganan Client-Side Live Image Preview & Validasi File Interaktif
 */

import { initScrollCraft } from '../../services/scroll-craft.js';

document.addEventListener('DOMContentLoaded', () => {
    // Inisialisasi scroll-craft motion reveal engine
    try {
        initScrollCraft();
    } catch {
        // Fallback safe jika observer tidak didukung
    }
    // 1. Live Preview & Validasi Foto Hero Carousel
    const heroInput = document.getElementById('hero_image');
    const heroPreview = document.getElementById('heroImagePreview');
    const heroLabelText = document.getElementById('heroFileLabelText');

    if (heroInput && heroPreview) {
        heroInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const maxSize = parseInt(heroInput.dataset.maxSize || '5242880', 10); // 5MB default

            if (file.size > maxSize) {
                alert(`Ukuran berkas foto terlalu besar (${(file.size / 1024 / 1024).toFixed(2)} MB). Maksimal ukuran berkas yang diperbolehkan adalah 5 MB.`);
                heroInput.value = '';
                if (heroLabelText) heroLabelText.textContent = 'Pilih Foto Pengganti...';
                return;
            }

            if (!file.type.match(/^image\/(png|jpeg|jpg|webp)$/i)) {
                alert('Format berkas tidak didukung. Harap pilih gambar dengan format JPG, JPEG, PNG, atau WebP.');
                heroInput.value = '';
                if (heroLabelText) heroLabelText.textContent = 'Pilih Foto Pengganti...';
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                if (event.target && event.target.result) {
                    heroPreview.src = event.target.result;
                    if (heroLabelText) {
                        heroLabelText.textContent = `Terpilih: ${file.name}`;
                    }
                }
            };
            reader.readAsDataURL(file);
        });
    }

    // 2. Validasi & Feedback Pemilihan Berkas Panduan PDF
    const pdfInput = document.getElementById('panduan_pdf');
    const pdfNameDisplay = document.getElementById('pdfFileNameDisplay');
    const pdfLabelText = document.getElementById('pdfFileLabelText');

    if (pdfInput) {
        pdfInput.addEventListener('change', (e) => {
            const file = e.target.files && e.target.files[0];
            if (!file) return;

            const maxSize = parseInt(pdfInput.dataset.maxSize || '10485760', 10); // 10MB default

            if (file.size > maxSize) {
                alert(`Ukuran dokumen PDF terlalu besar (${(file.size / 1024 / 1024).toFixed(2)} MB). Maksimal ukuran dokumen yang diperbolehkan adalah 10 MB.`);
                pdfInput.value = '';
                if (pdfLabelText) pdfLabelText.textContent = 'Pilih Berkas PDF Baru...';
                return;
            }

            if (file.type !== 'application/pdf' && !file.name.toLowerCase().endsWith('.pdf')) {
                alert('Format dokumen tidak valid. Hanya berkas PDF (.pdf) yang diperbolehkan.');
                pdfInput.value = '';
                if (pdfLabelText) pdfLabelText.textContent = 'Pilih Berkas PDF Baru...';
                return;
            }

            if (pdfNameDisplay) {
                const fileSizeKb = Math.round(file.size / 1024);
                pdfNameDisplay.textContent = `${file.name} (${fileSizeKb} KB - Siap Diunggah)`;
            }

            if (pdfLabelText) {
                pdfLabelText.textContent = `Terpilih: ${file.name}`;
            }
        });
    }

    // 3. Konfirmasi Reset Konten via Modal Dialog (Identik Modal Staf)
    const modalReset = document.getElementById('modalKonfirmasiResetKonten');
    const formResetModal = document.getElementById('formResetKontenModal');
    const modalTitleEl = document.getElementById('modalResetKontenTitle');
    const resetNamaEl = document.getElementById('resetKontenNama');
    const resetDescEl = document.getElementById('resetKontenDesc');
    const resetConsequenceEl = document.getElementById('resetKontenConsequence');
    const submitBtnTextEl = document.getElementById('btnSubmitResetKontenText');

    // Handler Buka Modal Reset Konten
    document.querySelectorAll('.btn-trigger-reset-content').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();

            const actionUrl = btn.dataset.actionUrl;
            const title = btn.dataset.title || 'Konfirmasi Reset Konten';
            const itemName = btn.dataset.itemName || 'Konten';
            const itemDesc = btn.dataset.itemDesc || '';
            const consequence = btn.dataset.consequence || 'Aset kustom akan dihapus dan landing page akan kembali menggunakan tampilan resmi bawaan sistem.';
            const submitText = btn.dataset.submitText || 'Ya, Kembalikan ke Default';

            if (formResetModal && actionUrl) {
                formResetModal.action = actionUrl;
            }

            if (modalTitleEl) modalTitleEl.textContent = title;
            if (resetNamaEl) resetNamaEl.textContent = itemName;
            if (resetDescEl) resetDescEl.textContent = itemDesc;
            if (resetConsequenceEl) {
                resetConsequenceEl.textContent = consequence.startsWith('•') ? consequence : `• ${consequence}`;
            }
            if (submitBtnTextEl) submitBtnTextEl.textContent = submitText;

            if (modalReset) {
                modalReset.style.display = 'flex';
            }
        });
    });

    // Handler Tutup Modal via data-modal-dismiss
    document.querySelectorAll('[data-modal-dismiss="modalKonfirmasiResetKonten"]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (modalReset) modalReset.style.display = 'none';
        });
    });

    // Handler Tutup Modal jika klik di luar card (backdrop click)
    if (modalReset) {
        modalReset.addEventListener('click', (e) => {
            if (e.target === modalReset) {
                modalReset.style.display = 'none';
            }
        });
    }

    // Handler Tutup Modal via tombol Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modalReset && modalReset.style.display === 'flex') {
            modalReset.style.display = 'none';
        }
    });

    // 4. Konfirmasi Tombol Purge Audit / Aksi Berbahaya Tertentu via data-confirm
    document.querySelectorAll('button[data-confirm]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const message = btn.getAttribute('data-confirm');
            if (message && !window.confirm(message)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
});

