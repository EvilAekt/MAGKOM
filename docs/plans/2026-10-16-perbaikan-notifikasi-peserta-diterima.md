# Plan: Penyesuaian Pop-Up Toast Notifikasi Penerimaan Peserta Magang (Status Diterima)

Dokumen rencana kerja teknis revisi untuk menyelaraskan notifikasi penerimaan peserta magang (*Status Diterima*) dengan **komponen Pop-Up Toast standar MagKom** yang sudah ada (sesuai dokumen `2026-10-13-redesain-popup-toast-notifikasi-perubahan.md`).

---

## 1. Tujuan

1. **Menyelaraskan Visual 100% dengan Pop-Up Toast Eksisting**:
   - Menggunakan bahasa visual identik dari komponen `.app-toast` yang sudah ada di MagKom:
     - Badge sirkular hijau mint lembut (`#ECFDF5`) dengan ikon *shield-check* emerald (`#10B981`).
     - Judul tebal pekat (`.toast-title` bold 700, `#0F172A` / dark `#F8FAFC`).
     - Subteks deskripsi abu-abu netral (`.toast-desc` regular 400, `#64748B` / dark `#94A3B8`).
     - Tombol tutup silang (&times;) `.toast-close-btn` di sudut kanan.
2. **Menghilangkan Masalah "Ukuran Mempengaruhi Tampilan" (*Zero Layout Push*)**:
   - Menghapus banner inline besar (`.detail-success-alert`) yang selama ini memakan ruang vertikal dan mendorong (*push down*) tabel berkas serta tugas magang ke bawah.
   - Mengalihkan notifikasi pengumuman diterima menjadi **Floating Pop-up Toast** di dalam `.toast-floating-container` (`position: fixed; top: 24px; right: 24px; z-index: 99999;`).
   - Konten halaman pendaftaran dan dashboard tetap stabil, bersih, dan tidak mengalami *layout shift*.
3. **Mengatasi Masalah "Muncul Terus" (*Smart Dismiss Persistence*)**:
   - Pop-up toast hanya akan muncul sekali saat status pendaftaran berubah menjadi Diterima.
   - Ketika peserta mengklik tombol tutup (&times;) atau waktu durasi selesai, status penutupan disimpan di `localStorage` per ID pendaftaran: `localStorage.setItem('magkom_accepted_toast_dismissed_' + pendaftaranId, 'true')`.
   - Pada reload halaman atau navigasi ke menu lain, pop-up **tidak akan muncul lagi mengganggu aktivitas peserta**.
4. **Aksesibilitas Dokumen Tetap Terjamin**:
   - Tautan unduh dan pratinjau *Surat Keterangan Diterima* tetap tersedia secara permanen di tombol aksi halaman detail pendaftaran dan kartu penempatan di dashboard peserta.

---

## 2. Asumsi & Keterkaitan Komponen

### Keterkaitan dengan Komponen yang Sudah Ada
- Layout peserta (`resources/views/layouts/peserta.blade.php`) telah memiliki wadah pop-up mengambang:
  ```html
  <div class="toast-floating-container" id="toastFloatingContainer" aria-live="polite" aria-atomic="true">
      <x-flash-alert />
  </div>
  ```
  serta fungsi helper JavaScript `window.MagKomToast` dan CSS rule `.app-toast`.
- Halaman detail pendaftaran (`resources/views/peserta/pendaftaran/detail.blade.php`) saat ini masih memiliki blok HTML statis lama `.detail-success-alert` (baris 1538–1570) yang berukuran besar dan statis inline. Blok ini yang akan digantikan oleh pop-up toast standar dan baris status dokumen yang rapi.

---

## 3. Pendekatan Desain

### Perbandingan Pendekatan

| Aspek | Desain Lama (Banner Inline Statis) | Desain Baru (Pop-Up Toast Standar MagKom) |
|---|---|---|
| **Jenis Komponen** | Blok banner vertikal besar di tengah halaman (`.detail-success-alert`) | Floating Pop-Up Card (`.app-toast`) di pojok kanan atas layar |
| **Dampak ke Layout** | ❌ Mendorong tabel berkas & grid dashboard ke bawah | ✅ **Zero layout shift**: Melayang di atas layar tanpa menggeser elemen apa pun |
| **Penyelesaian "Muncul Terus"** | ❌ Selalu dirender setiap kali halaman dibuka | ✅ **Tuntas**: Menggunakan `localStorage` pengecekan *dismissed* |
| **Konsistensi Visual** | Menggunakan CSS terpisah | ✅ **100% Selaras** dengan desain notifikasi perubahan / pengaturan yang sudah ada |
| **Akses Unduh Surat** | Hanya ada di banner tersebut | Tersedia di dalam pop-up berupa quick-link, serta tetap ada di tabel berkas & dashboard |

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
|---|---|---|
| `resources/views/peserta/pendaftaran/detail.blade.php` | Ubah | 1. Hapus banner besar `.detail-success-alert` yang mendorong tata letak halaman.<br>2. Tambahkan pemicu pop-up toast diterima yang memanggil struktur `.app-toast` standar MagKom dengan tautan pratinjau surat.<br>3. Tambahkan script pengecekan `localStorage` agar pop-up tidak muncul lagi setelah ditutup peserta.<br>4. Letakkan tombol unduh Surat Keterangan Diterima secara permanen di baris ringkasan dokumen. |
| `resources/views/peserta/dashboard.blade.php` | Ubah | Pastikan integrasi pop-up toast diterima terpanggil rapi saat peserta pertama kali membuka dashboard dengan status diterima, tanpa menduplikasi banner hero. |
| `resources/views/layouts/peserta.blade.php` | Verifikasi | Pastikan styling `.app-toast` dan `.toast-floating-container` siap menerima action link / tombol kecil di dalam toast. |

---

## 5. Rincian Langkah Implementasi

### Fase 1: Pembersihan Banner Besar & Penataan Akses Dokumen Permanen
- [x] Buka `resources/views/peserta/pendaftaran/detail.blade.php`:
  - Ganti blok banner inline raksasa `.detail-success-alert` (baris 1538–1570).
  - Sebagai gantinya, jika status `diterima` dan penempatan ada, sediakan tombol unduh SK di area header/action bar atau di tabel dokumen persyaratan, sehingga peserta selalu bisa mengunduh SK tanpa membutuhkan banner besar.

### Fase 2: Implementasi Pop-Up Toast Menyesuaikan Komponen Eksisting
- [x] Di `resources/views/peserta/pendaftaran/detail.blade.php` (dan dashboard bila relevan):
  - Tambahkan blok script cerdas yang memeriksa status kelulusan peserta:
    ```javascript
    document.addEventListener('DOMContentLoaded', function () {
        @if($val === 'diterima' && $pendaftaran->penempatan)
            const pendaftaranId = {{ $pendaftaran->id }};
            const dismissKey = 'magkom_accepted_toast_dismissed_' + pendaftaranId;

            if (!localStorage.getItem(dismissKey)) {
                const container = document.getElementById('toastFloatingContainer');
                if (container) {
                    const toast = document.createElement('div');
                    toast.className = 'app-toast alert-success';
                    toast.id = 'toastAcceptedAnnouncement';
                    toast.setAttribute('role', 'alert');
                    toast.innerHTML = `
                        <div class="toast-icon-badge success">
                            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div class="toast-content">
                            <div class="toast-title">{{ __('Selamat! Permohonan Magang Diterima') }}</div>
                            <div class="toast-desc">
                                Penempatan di {{ $pendaftaran->penempatan->bidang->nama_bidang ?? 'Dinas Kominfo Sleman' }} telah disetujui.
                                <div style="margin-top: 8px; display: flex; gap: 8px;">
                                    <a href="{{ route('peserta.penempatan.surat-diterima', $pendaftaran->penempatan) }}" target="_blank" style="font-weight: 600; color: #0751FF; text-decoration: underline; font-size: 0.8125rem;">
                                        Unduh Surat SK (PDF) &rarr;
                                    </a>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="toast-close-btn" onclick="dismissAcceptedToast(${pendaftaranId})" aria-label="{{ __('Tutup notifikasi') }}">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    `;
                    container.appendChild(toast);
                }
            }
        @endif
    });

    function dismissAcceptedToast(id) {
        const toast = document.getElementById('toastAcceptedAnnouncement');
        if (toast) {
            toast.classList.add('toast-hiding');
            setTimeout(() => toast.remove(), 300);
        }
        localStorage.setItem('magkom_accepted_toast_dismissed_' + id, 'true');
    }
    ```

### Fase 3: Pengujian Responsivitas & Dark Mode
- [x] Pastikan tampilan pop-up toast:
  - Mengikuti palet Light Mode (`#FFFFFF`, border `#E2E8F0`, badge `#ECFDF5` / `#10B981`).
  - Mengikuti palet Dark Mode (`#182234`, border `#26354D`, badge `rgba(16, 185, 129, 0.18)` / `#34D399`).
  - Tidak memotong teks di layar perangkat smartphone atau tablet.

---

## 6. Strategi Testing

1. **Automated Tests**:
   - Jalankan `php artisan test tests/Feature/PesertaPortalWorkflowTest.php` untuk memastikan routing, view pendaftaran, dan fungsi download PDF surat diterima tetap 100% lulus.
2. **Manual QA**:
   - Login sebagai `peserta@magkom.go.id` (`Password123!`).
   - Buka halaman pendaftaran dan dashboard:
     - Pop-up toast muncul di pojok kanan atas dengan badge mint dan perisai centang yang identik dengan notifikasi perubahan/pengaturan yang sudah ada.
     - Konten utama halaman (tabel dokumen dan tugas) **tidak terdorong ke bawah** sama sekali.
     - Klik tombol silang (&times;): Pop-up menutup dengan transisi halus.
     - Refresh halaman berkali-kali: Pop-up **tidak muncul lagi**.
     - Cek apakah tautan unduh Surat SK tetap ada dan berfungsi.

---

## 7. Kriteria Selesai (Definition of Done)

- [x] Tampilan pop-up 100% menggunakan format `.app-toast` yang sudah ada (badge mint perisai, judul bold, subteks, tombol close).
- [x] Tidak ada lagi banner inline besar yang mendorong layout atau membuat tampilan sesak.
- [x] Pop-up hanya muncul saat pertama kali dan tidak muncul terus-menerus berkat penyimpanan status di `localStorage`.
- [x] Surat Keputusan (SK) tetap mudah diunduh kapan saja.
- [x] Kompatibel dengan Light Mode dan Dark Mode.
- [x] Seluruh automated test suite lulus tanpa error.

