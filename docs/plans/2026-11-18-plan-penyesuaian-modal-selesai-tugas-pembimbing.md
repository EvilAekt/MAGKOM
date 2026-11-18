# Rencana Kerja: Penyesuaian Modal Konfirmasi Penyelesaian Tugas Pembimbing (Hapus Badge #TUGAS & Ubah Warna ke Biru Sleman)

Dokumen rencana kerja ini disusun berdasarkan permintaan pengguna untuk:
1. Menghapus badge `#TUGAS-xxx` pada kartu identitas modal (Tangkapan Layar 1).
2. Menyelaraskan skema warna modal **Konfirmasi Penyelesaian Tugas** dari hijau (`variant-success`) menjadi **biru Sleman (`variant-primary`)** agar konsisten dengan modal-modal konfirmasi lainnya di MAGKOM (Tangkapan Layar 2).

---

## 1. Analisis Kebutuhan

### Kebutuhan 1: Penghapusan Badge `#TUGAS-xxx` (SS 1)
- **Kondisi Saat Ini**:
  Di dalam [show.blade.php](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php#L532-L537), kartu identitas modal menampilkan:
  ```html
  <span class="magkom-modal-reg-badge" id="reviewModalTaskBadge">
      #TUGAS-{{ $tugas->id }}
  </span>
  ```
- **Tindakan**:
  Menghapus elemen `<span class="magkom-modal-reg-badge">` tersebut sehingga judul tugas memiliki ruang horizontal yang lebih leluasa dan tampilan menjadi lebih bersih (*clean & minimal*).

---

### Kebutuhan 2: Penyelarasan Warna Hijau ke Biru Sleman (SS 2)
- **Kondisi Saat Ini**:
  Pada [tugas-review.js](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-review.js#L185), ketika aksi adalah `'selesai'`, modal menggunakan `setModalVariant('success')` yang memicu warna hijau emerald:
  - Icon badge: hijau `#16A34A`
  - Avatar inisial: hijau `#16A34A`
  - Impact notice banner: hijau muda `#F0FDF4`
  - Tombol aksi: hijau `#16A34A` ("Ya, Tandai Selesai")
- **Kondisi Sasaran ("seperti yang lain sesuaikan")**:
  Mengubahnya menjadi varian utama MagKom (**`variant-primary`**) yang mengadopsi palet resmi *Sleman Royal Blue* (`#01347C`), identik dengan modal konfirmasi penerimaan berkas di Admin:
  - **Icon Badge**: Latar `#F0F6FF`, border halus `rgba(1, 52, 124, 0.16)`, ikon centang `#01347C`.
  - **Avatar Inisial**: Gradien royal blue `linear-gradient(135deg, #01347C 0%, #02275D 100%)`.
  - **Impact Notice Banner**: Latar *soft tint* `#F0F6FF`, border `rgba(1, 52, 124, 0.08)`, teks `#1E40AF`, dan ikon `#01347C`.
  - **Tombol Konfirmasi**: Sleman Blue `#01347C` (hover: `#002966`, shadow: `rgba(1, 52, 124, 0.22)`).
  - **Dark Mode**: Didukung secara otomatis melalui token `[data-theme="dark"] .variant-primary` yang sudah tersedia di `confirm-modal.css`.

---

## 2. Berkas yang Akan Diubah

Sesuai **`AGENTS.md` Rule 3 (Aturan khusus Blade)**:
- Tidak ada penambahan tag `<style>`, `<script>`, atau atribut inline handler di file Blade.
- Styling 100% menggunakan token CSS yang sudah tersedia di [`resources/css/components/confirm-modal.css`](file:///d:/laragon/www/MAGKOM/resources/css/components/confirm-modal.css).

| Berkas | Aksi | Rincian Perubahan |
| :--- | :---: | :--- |
| [`resources/views/pembimbing/tugas/show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php) | **Modify** | Hapus elemen badge `<span class="magkom-modal-reg-badge" id="reviewModalTaskBadge">#TUGAS-{{ $tugas->id }}</span>`. |
| [`resources/js/pages/pembimbing/tugas-review.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-review.js) | **Modify** | Ubah `setModalVariant('success')` menjadi `setModalVariant('primary')` pada blok `action === 'selesai'`. |

---

## 3. Langkah Pengerjaan Terstruktur

1. **Pembaruan Blade ([`show.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/pembimbing/tugas/show.blade.php))**:
   - Menghapus elemen `#reviewModalTaskBadge` di baris 534–536.
2. **Pembaruan JavaScript ([`tugas-review.js`](file:///d:/laragon/www/MAGKOM/resources/js/pages/pembimbing/tugas-review.js))**:
   - Menyesuaikan fungsi `openReviewModal`:
     Ganti `setModalVariant('success')` menjadi `setModalVariant('primary')`.
3. **Quality Gates & Pengujian**:
   - Jalankan `npm run build` untuk memverifikasi kompilasi Vite.
   - Jalankan `php artisan view:cache` untuk memverifikasi sintaks template Blade.
   - Jalankan `php vendor/bin/pint --test` untuk memverifikasi linter PSR-12.
   - Jalankan `vendor/bin/phpstan analyse --memory-limit=1G` untuk analisis statis.
   - Jalankan automated tests: `php artisan test tests/Feature/PembimbingReviewClassroomTest.php tests/Feature/TypographyDesignSystemTest.php`.

---

## 4. Konfirmasi Pengguna

Apakah rencana kerja penyesuaian modal ini disetujui untuk segera diimplementasikan?

