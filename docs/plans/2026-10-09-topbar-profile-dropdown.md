# Rencana Kerja: Interaktivitas Menu Profil Topbar & Perbaikan Binding Data Pengguna

**Tanggal:** 9 Oktober 2026  
**Status:** Menunggu Persetujuan  
**Target Komponen:** Portal Peserta (`layouts/peserta.blade.php`), Model `User.php`, dan Form Profil (`profil/show.blade.php`)

---

## 1. Tujuan
Mengubah elemen indikator profil pasif di bilah atas (topbar kanan) Portal Peserta menjadi dropdown menu interaktif yang dapat diklik, ramah aksesibilitas (keyboard navigation & WCAG 2.1 AA), serta memperbaiki binding nama pengguna yang saat ini kosong agar data akun tampil secara utuh dan konsisten.

---

## 2. Asumsi & Investigasi Awal
1. **Penyebab Utama Nama Kosong:**
   - Kolom database pada tabel `users` adalah `nama` (bukan `name`).
   - Kode tampilan saat ini memanggil `auth()->user()->name` dan `$user->name`, sehingga bernilai `null`. Hal ini menyebabkan inisial avatar hanya menampilkan fallback `"P"` dan nama pengguna tidak muncul di topbar maupun di field *Nama Lengkap* form biodata.
2. **Interaktivitas Topbar:**
   - Elemen `.user-chip` di `resources/views/layouts/peserta.blade.php` saat ini berupa tag `<div>` statis tanpa event listener.
   - Pengguna menginginkan chip tersebut dapat diklik dan memunculkan menu dropdown kontekstual.
3. **Ketersediaan Rute Terkait:**
   - Halaman Biodata Profil: `route('peserta.profil.show')`
   - Riwayat Pendaftaran: `route('peserta.pendaftaran.index')`
   - Ganti Kata Sandi: `route('password.ganti')`
   - Keluar Akun / Logout: `route('logout')` (metode `POST`)

---

## 3. Pendekatan Desain & Evaluasi Opsi

### Opsi A: Dropdown Menu Berbasis Vanilla JavaScript & CSS Tokens (Direkomendasikan)
- **Implementasi:**
  - Mengubah `.user-chip` menjadi elemen `<button type="button" class="user-chip-btn" aria-expanded="false" aria-haspopup="true">`.
  - Menambahkan ikon panah ke bawah (chevron-down) dengan transisi rotasi 180° saat status menu terbuka.
  - Menambahkan menu dropdown mengambang (`.profile-dropdown-menu`) dengan styling token resmi MagKom (`--neutral-0`, shadow elevasi, border `--neutral-400`, radius 12px).
  - Skrip Vanilla JS ringan: mendukung toggle saat diklik, *click outside* untuk menutup, dan tombol Escape (ESC) untuk menutup.
- **Kelebihan:** Tidak memerlukan library eksternal, performa sangat cepat, ramah aksesibilitas (keyboard accessible & screen reader friendly), dan kontrol visual penuh sesuai Design System MagKom.
- **Kekurangan:** Memerlukan sedikit penulisan CSS & event listener di layout.

### Opsi B: HTML `<details>` & `<summary>` Native
- **Implementasi:** Menggunakan elemen semantik `<details>` bawaan HTML.
- **Kelebihan:** Tanpa JavaScript tambahan untuk membuka/menutup.
- **Kekurangan:** Sulit menangani *click-outside* dan animasi transisi halus antar-state secara elegan di berbagai browser.

**Keputusan:** Memilih **Opsi A** demi UX yang mulus, modern, dan konsisten dengan antarmuka portal enterprise.

---

## 4. Perubahan File

| File Path | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Models/User.php` | Ubah | Menambahkan accessor `getNameAttribute(): ?string` yang me-return `$this->nama` agar `$user->name` maupun `$user->nama` bekerja transparan. |
| `resources/views/layouts/peserta.blade.php` | Ubah | Mengganti `.user-chip` menjadi dropdown interaktif, menambahkan styling CSS dropdown & chevron, serta script JavaScript toggle & keyboard listener. |
| `resources/views/peserta/profil/show.blade.php` | Ubah | Mengubah pemanggilan nama menjadi `$user->nama ?? $user->name` untuk memastikan field "Nama Lengkap" terisi dengan benar. |

---

## 5. Langkah Implementasi Terurut

- [x] **Langkah 1: Perbaikan Model Data (`User.php`)**
  - Buka `app/Models/User.php`.
  - Tambahkan accessor Eloquent `getNameAttribute()` yang mengembalikan nilai kolom `nama`.
  - Verifikasi bahwa pemanggilan `$user->name` menghasilkan nama lengkap akun.

- [x] **Langkah 2: Pembaruan Tampilan Form Profil (`profil/show.blade.php`)**
  - Buka `resources/views/peserta/profil/show.blade.php`.
  - Perbarui field input *Nama Lengkap* agar menampilkan `$user->nama ?? $user->name`.

- [x] **Langkah 3: Markup & Struktur Dropdown Topbar (`layouts/peserta.blade.php`)**
  - Ganti elemen `.user-chip` statis dengan struktur button dropdown interaktif.

- [x] **Langkah 4: Styling CSS Dropdown Sesuai Design System MagKom**
  - Tambahkan CSS untuk container dropdown, trigger button, menu, item navigasi, dan chevron indicator.

- [x] **Langkah 5: Interaktivitas JavaScript**
  - Tambahkan skrip toggle dropdown, click outside close, dan Escape key listener.

---

## 6. Strategi Testing & Verifikasi
1. **Verifikasi Visual Browser:**
   - Akses `http://localhost:8000/peserta/profil` menggunakan akun peserta (`peserta@magkom.go.id`).
   - Pastikan nama lengkap peserta muncul di chip kanan atas dan di kolom form biodata.
   - Klik chip profil: menu dropdown harus terbuka dengan mulus.
   - Klik tombol di luar menu: menu dropdown harus tertutup.
   - Tekan tombol `Escape`: menu dropdown harus tertutup.
2. **Pengujian Tautan:**
   - Klik "Biodata & Profil" -> mengarah ke `/peserta/profil`.
   - Klik "Riwayat Pendaftaran" -> mengarah ke `/peserta/pendaftaran`.
   - Klik "Ganti Kata Sandi" -> mengarah ke `/password/ganti`.
   - Klik "Keluar Akun" -> berhasil logout dan dialihkan ke `/login`.
3. **Automated Test:**
   - Jalankan `php artisan test --filter=PesertaPortalWorkflowTest` untuk memastikan tidak ada alur yang terganggu.

---

## 7. Risiko & Mitigasi
- **Risiko Aksesibilitas (A11y):** Dropdown hanya bisa diakses via mouse.  
  *Mitigasi:* Gunakan tombol `<button>` asli dengan `aria-expanded`, `role="menu"`, dan keyboard navigation (Escape & Tab).
- **Risiko Tabrakan Z-Index:** Dropdown tertutup komponen tabel atau modal.  
  *Mitigasi:* Berikan `z-index: 50` pada dropdown menu (di atas topbar `z-index: 40`).

---

## 8. Kriteria Selesai (Definition of Done)
- [x] Nama peserta muncul dengan benar di topbar kanan dan di field form biodata.
- [x] Elemen profil di bilah atas dapat diklik dan membuka/menutup dropdown menu.
- [x] Dropdown menu menyediakan opsi: *Biodata & Profil*, *Riwayat Pendaftaran*, *Ganti Kata Sandi*, dan *Keluar Akun*.
- [x] Fitur *Click Outside* dan tombol *Escape* berhasil menutup menu.
- [x] Seluruh unit & feature tests yang ada tetap berjalan hijau (pass).
