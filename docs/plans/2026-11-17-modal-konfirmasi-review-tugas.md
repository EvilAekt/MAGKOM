# Rencana Kerja: Standarisasi Modal Konfirmasi Review Tugas Pembimbing
**Dokumen Referensi:** Design System MagKom v1.0, PRD MagKom v1.2 (F-07 / BR-08), WCAG 2.1 AA  
**Tanggal:** 17 November 2026  
**Target Halaman:** Review / Evaluasi Tugas Pembimbing (`resources/views/pembimbing/tugas/show.blade.php`)  
**Target Skrip:** `resources/js/pages/pembimbing/tugas-review.js`  
**Status:** Menunggu Persetujuan Pengguna (*Pending Approval*)

---

## 1. Latar Belakang & Analisis Masalah

### Kondisi Saat Ini (Tangkapan Layar 1):
Saat pembimbing magang menekan opsi **"Kembalikan Tugas"** pada halaman review tugas, sistem memicu:
```javascript
if (confirm('Apakah Anda yakin ingin mengembalikan tugas ini kepada peserta untuk diperbaiki?')) { ... }
```
dan jika catatan umpan balik kurang dari 5 karakter:
```javascript
alert('Catatan umpan balik wajib diisi minimal 5 karakter ketika mengembalikan tugas kepada peserta untuk diperbaiki.');
```
Hal ini menyebabkan browser memunculkan dialog native (kotak hitam di bagian atas layar pada browser bertema dark mode: `magkom.test:8080 says: Apakah Anda yakin...`) yang:
1. Merusak estetika dan kredibilitas portal resmi MagKom Dinas Kominfo Sleman.
2. Tidak memberikan konteks data yang informatif (tidak menampilkan nama peserta, judul tugas, maupun isi catatan revisi).
3. Mengabaikan aksesibilitas dan standar modal dialog WCAG 2.1 AA.

### Kondisi yang Diharapkan (Tangkapan Layar 2 - Acuan SuperAdmin):
Mengganti seluruh dialog native browser tersebut menjadi **Custom Modal Dialog UI** terpadu yang identik dengan modal konfirmasi di SuperAdmin dan Verifikasi Admin (`confirm-modal.css`):
- Memiliki backdrop overlay blur semi-transparan (`rgba(15, 23, 42, 0.6)`).
- Dialog card modern berpusat di tengah layar (*centered pop-up*) dengan sudut melengkung `16px`.
- Memiliki Icon Badge aksi (merah/danger untuk kembalikan/revisi, hijau/success untuk tandai selesai).
- Memiliki Callout Box informatif yang memuat:
  - Pertanyaan konfirmasi.
  - Kartu entitas (Judul Tugas & Nama Peserta Magang).
  - Ringkasan/preview catatan umpan balik yang ditulis pembimbing.
  - Poin konsekuensi operasional yang jelas.
- Tombol aksi jelas: **"Batal"** (Netral/Ghost) dan **"Ya, Kembalikan Tugas"** (Merah/Danger).
- Mendukung penutupan via tombol Esc, tombol silang (x), dan klik di luar area backdrop.

---

## 2. Asumsi & Pertimbangan Teknis

1. **Aset CSS Sudah Tersedia:** Berkas `resources/css/components/confirm-modal.css` sudah ada di repositori dan sudah diimpor oleh `resources/css/app.css` (yang otomatis dimuat pada `layouts/dashboard.blade.php`). Kita tidak perlu membuat berkas CSS baru atau menulis CSS inline.
2. **Cakupan Aksi:** Selain aksi **"Kembalikan Tugas"**, tombol **"Tandai Selesai"** (`#btnSubmitSelesaiDirect`) saat ini juga masih memakai `confirm('Apakah Anda yakin seluruh hasil kerja tugas ini telah tuntas dan disetujui?')`. Rencana ini merancang 1 modal dialog terpadu yang dapat melayani kedua aksi tersebut secara dinamis (varian *danger/warning* untuk kembalikan, dan varian *success* untuk tandai selesai).
3. **Validasi Tanpa Alert Native:** Peringatan validasi catatan umpan balik minimal 5 karakter akan ditangani secara inline (menyorot field dengan border merah dan menampilkan pesan helper teks) tanpa memunculkan `window.alert()`.

---

## 3. Evaluasi Opsi Pendekatan Teknis

| Parameter | Opsi A: Modal Dinamis Terpadu (Direkomendasikan) | Opsi B: Modal Statis Hanya untuk Kembalikan |
| :--- | :--- | :--- |
| **Deskripsi** | Satu elemen dialog modal serbaguna `#modalKonfirmasiReviewTugas` yang varian visualnya (judul, icon, warna tombol, teks konsekuensi) diatur via JavaScript sesuai aksi yang ditekan (Kembalikan vs Tandai Selesai). | Menambahkan satu modal statis hanya khusus untuk aksi "Kembalikan Tugas", membiarkan "Tandai Selesai" tetap memakai `confirm()`. |
| **Kelebihan** | - Menghilangkan 100% dialog browser native di halaman review tugas.<br>- Kode Blade lebih ringkas (1 modal untuk 2 aksi).<br>- Pola identik dengan `resources/js/pages/admin/pendaftaran-show.js`. | Hanya menyentuh satu alur kecil. |
| **Kekurangan** | Memerlukan sedikit penyesuaian fungsi controller di `tugas-review.js`. | Inkonsistensi UX: satu tombol pakai modal cantik, tombol sebelahnya masih keluar popup browser hitam. |
| **Rekomendasi** | **PILIH OPSI A** demi konsistensi menyeluruh dan kepatuhan UI/UX design system. | Tidak direkomendasikan. |

---

## 4. Rincian Perubahan Berkas

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `resources/views/pembimbing/tugas/show.blade.php` | Modify | Menambahkan markup dialog modal `#modalKonfirmasiReviewTugas` (menggunakan kelas `.magkom-modal-*` dari `confirm-modal.css`) sebelum `@push('scripts')`. Menambahkan kontainer pesan error validasi inline di bawah textarea umpan balik. |
| `resources/js/pages/pembimbing/tugas-review.js` | Modify | 1. Mengeliminasi pemanggilan `window.confirm()` dan `window.alert()`.<br>2. Menambahkan fungsi kontrol modal (`openReviewModal`, `closeReviewModal`, `setModalVariant`).<br>3. Menghubungkan klik tombol "Kembalikan Tugas" dan "Tandai Selesai" ke modal.<br>4. Mengisi preview teks feedback secara reaktif ke dalam callout modal sebelum submit. |

---

## 5. Rincian Spesifikasi & Langkah Implementasi

### Bagian 5.1: Markup Blade (`resources/views/pembimbing/tugas/show.blade.php`)
Menempatkan dialog modal terstandarisasi sebelum penutup `@endsection`:

```blade
{{-- MODAL KONFIRMASI REVIEW TUGAS (STANDAR MAGKOM DESIGN SYSTEM) --}}
<div id="modalKonfirmasiReviewTugas" 
     class="magkom-modal-backdrop" 
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="reviewModalTitle"
     tabindex="-1">
    <div class="magkom-modal-dialog">
        <div class="magkom-modal-header">
            <div class="magkom-modal-header-left">
                <div id="reviewModalIconWrap" class="magkom-modal-icon-badge variant-danger">
                    <svg id="reviewModalIconSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        {{-- Icon SVG dinamis diinjeksi via JS --}}
                    </svg>
                </div>
                <div>
                    <h3 id="reviewModalTitle" class="magkom-modal-title">
                        {{ __('Konfirmasi Pengembalian Tugas') }}
                    </h3>
                    <span id="reviewModalSubtitle" class="magkom-modal-subtitle">
                        {{ __('Evaluasi Hasil Penugasan Peserta') }}
                    </span>
                </div>
            </div>
            <button type="button" 
                    id="reviewModalCloseBtn"
                    class="magkom-modal-close" 
                    aria-label="{{ __('Tutup dialog') }}"
                    title="{{ __('Tutup dialog') }}">
                &times;
            </button>
        </div>

        <div id="reviewModalCallout" class="magkom-modal-callout variant-danger">
            <p id="reviewModalQuestion" class="magkom-modal-question">
                {{ __('Apakah Anda yakin ingin mengembalikan tugas ini kepada peserta untuk diperbaiki?') }}
            </p>
            <div class="magkom-modal-target-box">
                <div class="magkom-modal-target-name">
                    {{ $tugas->judul }}
                </div>
                <div class="magkom-modal-target-sub">
                    {{ $menteeNama }} &bull; {{ Str::limit($institusi, 28) }}
                </div>
            </div>
            <div id="reviewModalFeedbackBox" style="display: none; margin-top: 0.65rem; background: rgba(255, 255, 255, 0.9); border-radius: 8px; padding: 0.65rem 0.85rem; border: 1px dashed var(--neutral-300);">
                <div style="font-size: 0.725rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase; margin-bottom: 0.25rem;">
                    {{ __('Catatan Umpan Balik:') }}
                </div>
                <div id="reviewModalFeedbackText" style="font-size: 0.825rem; color: var(--neutral-800); font-style: italic; max-height: 80px; overflow-y: auto; white-space: pre-wrap;"></div>
            </div>
            <p id="reviewModalConsequence" class="magkom-modal-consequence">
                {{ __('• Status tugas akan diubah menjadi Perlu Perbaikan dan peserta wajib mengirimkan revisi.') }}
            </p>
        </div>

        <div class="magkom-modal-footer">
            <button type="button" 
                    id="reviewModalCancelBtn"
                    class="magkom-modal-btn-cancel">
                {{ __('Batal') }}
            </button>
            <button type="button" 
                    id="reviewModalConfirmBtn"
                    class="magkom-modal-btn-confirm variant-danger">
                <span id="reviewModalConfirmText">{{ __('Ya, Kembalikan Tugas') }}</span>
            </button>
        </div>
    </div>
</div>
```

### Bagian 5.2: Logika JavaScript (`resources/js/pages/pembimbing/tugas-review.js`)
- Mengganti pemanggilan `confirm(...)` pada tombol:
  1. Klik "Kembalikan Tugas" (`#menuOptKembalikanTugas`):
     - Validasi `feedbackInput.value.trim().length >= 5`.
     - Jika tidak valid: buka sidebar (jika tertutup), fokus ke textarea, tambahkan kelas `has-error`, dan tampilkan teks error inline tanpa memanggil `alert()`.
     - Jika valid: isi teks catatan ke `#reviewModalFeedbackText`, atur mode modal ke `'kembalikan'`, dan buka modal dialog.
  2. Klik "Tandai Selesai" (`#btnSubmitSelesaiDirect` / `#menuOptTandaiSelesai`):
     - Atur mode modal ke `'selesai'` (varian Success/Hijau, icon centang, konsekuensi: "Tugas akan ditandai tuntas").
     - Buka modal dialog.
- Menangani penutupan modal:
  - Klik tombol "Batal" (`#reviewModalCancelBtn`).
  - Klik tombol silang (`#reviewModalCloseBtn`).
  - Klik di luar dialog (pada backdrop `.magkom-modal-backdrop`).
  - Tekan tombol keyboard `Escape`.
- Menangani submit form:
  - Saat tombol konfirmasi modal (`#reviewModalConfirmBtn`) diklik, jalankan `formActionKembalikan.submit()` atau `formTandaiSelesai.submit()` sesuai mode aktif.

---

## 6. Rencana Verifikasi & Pengujian

### Skenario Uji Manual:
1. **Pengembalian Tugas - Validasi Kurang dari 5 Karakter**:
   - Kosongkan catatan umpan balik, lalu klik "Kembalikan Tugas".
   - **Ekspektasi:** Tidak ada popup alert browser. Textarea langsung disorot merah dengan pesan validasi, dan layar bergeser dengan halus ke textarea umpan balik.
2. **Pengembalian Tugas - Muncul Modal Pop-up**:
   - Isi catatan umpan balik (contoh: "Mohon lengkapi laporan bab 2 bagian lampiran link figma").
   - Klik "Kembalikan Tugas".
   - **Ekspektasi:** Muncul pop-up modal di tengah layar dengan latar blur (bukan popup hitam bawaan browser).
   - Terdapat nama tugas, nama peserta, dan kutipan catatan umpan balik yang baru saja diketik.
3. **Pembatalan Modal**:
   - Tekan tombol **"Batal"**, atau tekan tombol **Esc**, atau klik pada latar belakang gelap di luar kotak modal.
   - **Ekspektasi:** Modal tertutup kembali dengan animasi halus tanpa melakukan pengiriman form.
4. **Konfirmasi Pengembalian**:
   - Klik **"Ya, Kembalikan Tugas"**.
   - **Ekspektasi:** Form terkirim, status tugas berubah menjadi *perlu_revisi*, dan muncul flash notification sukses.
5. **Aksi Tandai Selesai**:
   - Klik tombol **"Tandai Selesai"**.
   - **Ekspektasi:** Muncul pop-up modal bertema hijau/sukses, bebas dari dialog native `window.confirm`.
6. **Dark Mode & Responsif**:
   - Verifikasi tampilan modal saat mode gelap (`[data-theme="dark"]`) aktif dan pada viewport tablet/mobile.

### Skenario Uji Otomatis & Standar Kode:
- Jalankan formatting Pint: `./vendor/bin/pint --test`
- Jalankan analisis statis PHPStan jika ada perubahan PHP: `./vendor/bin/phpstan analyse --memory-limit=1G`
- Jalankan build aset Vite: `npm run build`

---

## 7. Kriteria Selesai (*Definition of Done*)

- [ ] Tidak ada lagi pemanggilan `confirm(...)` maupun `alert(...)` di `resources/js/pages/pembimbing/tugas-review.js`.
- [ ] Pop-up modal muncul di tengah layar (*centered modal*) dengan gaya visual konsisten berstandar MagKom Design System (seperti di SuperAdmin).
- [ ] Preview catatan umpan balik muncul secara otomatis di dalam kotak modal saat mengembalikan tugas.
- [ ] Modal dapat ditutup menggunakan keyboard (Escape) dan klik backdrop (a11y compliant).
- [ ] Tidak ada CSS atau JavaScript baru yang disisipkan secara inline di dalam berkas Blade (mematuhi Rule 3.1 & 3.2).
- [ ] Kompilasi `npm run build` berjalan sukses tanpa error.
- [ ] Status Git diperiksa dan dilaporkan dengan rapi.
