/**
 * Modul Interaksi Master Jurusan SuperAdmin
 * - Penanganan modal tambah & edit jurusan via event delegation
 * - Pengisian form aman berbasis atribut HTML5 data-* (kebal newline & kutip)
 */
document.addEventListener('DOMContentLoaded', () => {
    const modalTambah = document.getElementById('modalTambahJurusan');
    const modalEdit = document.getElementById('modalEditJurusan');
    const editNama = document.getElementById('editNamaJurusan');
    const editKategori = document.getElementById('editKategoriJurusan');
    const formEdit = document.getElementById('formEditJurusan');

    document.addEventListener('click', (event) => {
        // Trigger Buka Modal Tambah
        const triggerTambah = event.target.closest('.btn-trigger-tambah-jurusan') || event.target.closest('#btnOpenTambahJurusan');
        if (triggerTambah) {
            event.preventDefault();
            if (modalTambah) modalTambah.style.display = 'flex';
            return;
        }

        // Trigger Buka Modal Edit
        const triggerEdit = event.target.closest('.btn-trigger-edit-jurusan');
        if (triggerEdit) {
            event.preventDefault();
            const id = triggerEdit.dataset.id;
            const nama = triggerEdit.dataset.nama || '';
            const kategori = triggerEdit.dataset.kategori || 'smk';
            const actionUrl = triggerEdit.dataset.actionUrl || `/superadmin/jurusan/${id}`;

            if (editNama) editNama.value = nama;
            if (editKategori) editKategori.value = kategori;
            if (formEdit) formEdit.action = actionUrl;
            if (modalEdit) modalEdit.style.display = 'flex';
            return;
        }

        // Trigger Tutup Modal
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Tutup jika klik backdrop
        if (event.target === modalTambah) {
            modalTambah.style.display = 'none';
        }
        if (event.target === modalEdit) {
            modalEdit.style.display = 'none';
        }
    });

    // Keyboard ESC untuk menutup modal
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (modalTambah) modalTambah.style.display = 'none';
            if (modalEdit) modalEdit.style.display = 'none';
        }
    });
});

