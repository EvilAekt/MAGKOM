# Rencana Kerja: Perbaikan Pop-Up Notifikasi Realtime & Layout Grid Dashboard Peserta

Dokumen ini memuat rencana kerja terstruktur untuk menyelesaikan 4 keluhan terkait pop-up notifikasi di Dashboard Peserta MagKom:
1. Menghilangkan suara efek notifikasi (audio chime) yang berbunyi saat ada notifikasi / saat diklik.
2. Memperbaiki masalah tata letak (grid layout) di dashboard peserta magang saat pop-up muncul di kanan atas.
3. Mengubah frekuensi pop-up agar hanya muncul **satu kali saja** (tidak muncul berulang kali setiap kali refresh atau pindah halaman).
4. Menyesuaikan tampilan dan tema pop-up agar selaras dengan desain sistem notifikasi MagKom yang sudah ada.

---

## 1. Ringkasan Akar Masalah (Root Cause Analysis)

| No | Masalah | Akar Penyebab Teknis |
| :--- | :--- | :--- |
| 1 | **Suara notifikasi saat diklik** | Di `resources/js/services/realtime.js` dan `public/js/realtime-sse.js`, method `playNotificationChime()` memanggil Web Audio API synthesizer. Karena kebijakan browser memblokir audio autoplay sebelum interaksi pengguna, audio synthesizer tertunda dan baru meletup berbunyi seketika saat pengguna melakukan klik pertama di layar. |
| 2 | **Grid layout bergeser saat pop-up muncul** | Di `resources/views/layouts/peserta.blade.php`, kontainer pop-up `<div class="toast-floating-container">` diletakkan di dalam `<main class="content-area">`. Selain itu, SSE listener (`sse-toast-listener.blade.php`) membuat kontainer terpisah dengan kelas utilitas Tailwind (`fixed top-5 right-5 flex...`). Karena MAGKOM menggunakan Vanilla CSS murni tanpa Tailwind runtime, kelas Tailwind tidak aktif, sehingga kontainer SSE diperlakukan sebagai elemen `position: static` normal di dalam flow dokumen, mendistorsi lebar kontainer dan kolom `.saas-cockpit-grid`. |
| 3 | **Pop-up selalu muncul berulang kali** | Di backend, `RealtimeEventService::getPendingEvents()` hanya mengecek `where('id', '>', $lastEventId)` tanpa memfilter status baca (`whereNull('read_at')`). Di sisi frontend, setiap kali halaman di-refresh, koneksi EventSource dibuat baru dengan `lastEventId = 0`. Frontend juga tidak pernah memanggil endpoint `POST /realtime/read/{id}` saat menampilkan toast, sehingga server terus-menerus mengirimkan event lama yang sama pada setiap navigasi/reload halaman. |
| 4 | **Tema pop-up tidak selaras** | Notifikasi SSE menghasilkan markup HTML kustom dengan kelas Tailwind yang asing dan tidak cocok dengan tema MagKom (Gentelella & Sleman Navy). Padahal di `resources/views/layouts/peserta.blade.php` sudah ada standar komponen `window.MagKomToast` dengan kelas `.app-toast`, circular badge soft, animasi slide-in halus, dan timer auto-dismiss. |

---

## 2. Asumsi & Batasan Kerja

1. **Kompatibilitas Multi-Role**: Perbaikan SSE engine dan service realtime harus tetap kompatibel dengan role lain (SuperAdmin, Admin, Pembimbing).
2. **Aturan AGENTS.md**:
   - Tidak menulis CSS/JS baru di dalam Blade.
   - Perubahan styling tetap di `resources/css/` atau memanfaatkan token CSS yang sudah ada di layout.
   - Perubahan logika interaksi berada di `resources/js/services/realtime.js` (kemudian di-build ke `public/` jika diperlukan).
   - Menjaga keutuhan database migration dan business logic existing.

---

## 3. Pendekatan Teknis

### Opsi Alternatif yang Dievaluasi:

- **Opsi A (Frontend-Only Suppression)**: Menyimpan daftar ID notifikasi yang sudah muncul di `localStorage` browser saja tanpa menyentuh backend.
  - *Kelemahan*: Query database di endpoint `/realtime/stream` tetap menarik ratusan event lama setiap koneksi, membebani memori server, dan jika user berganti browser/perangkat, semua notifikasi lama akan membanjiri layar kembali.
- **Opsi B (Pendekatan Terpadu Backend + Frontend - TERPILIH)**:
  - **Backend**: Filter event di `RealtimeEventService::getPendingEvents()` dengan `whereNull('read_at')` sehingga event yang telah dibaca tidak pernah dikirim ulang ke client.
  - **Frontend Client**: Saat notifikasi diterima dan ditampilkan satu kali, client otomatis mengirim panggilan latar belakang ke `POST /realtime/read/{id}` untuk menandai timestamp `read_at`. Serta simpan pointer `last_event_id` di `sessionStorage` agar browser mengirim query parameter `last_event_id` pada inisialisasi EventSource berikutnya.
  - **Audio**: Hapus pemanggilan `playNotificationChime()` sepenuhnya dari `realtime.js` dan `realtime-sse.js`.
  - **Layout & Desain**:
    - Pindahkan `#toastFloatingContainer` di `resources/views/layouts/peserta.blade.php` dan `layouts/dashboard.blade.php` ke luar `<main class="content-area">` langsung di bawah `<body>`. Beri inline/class CSS murni `position: fixed; top: 24px; right: 24px; z-index: 99999; pointer-events: none;` yang terisolasi dari grid dokumen.
    - Satukan handler toast SSE dengan memanggil `window.MagKomToast.show()` yang sudah ada, sehingga tampilan, warna, font, ikon, dan animasi 100% identik dengan tema MagKom.

---

## 4. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :--- | :--- |
| `app/Services/RealtimeEventService.php` | Modify | Tambahkan filter `->whereNull('read_at')` pada query `getPendingEvents` agar event yang sudah dibaca tidak pernah dikirimkan kembali. |
| `resources/js/services/realtime.js` | Modify | 1) Hapus pemanggilan `playNotificationChime()`. 2) Ubah `showToast` agar mendelegasikan rendering ke `window.MagKomToast` atau menggunakan markup MagKom Toast murni. 3) Tambahkan auto mark-as-read via `fetch('/realtime/read/' + data.id)` dan kelola `last_event_id` di `sessionStorage`. |
| `public/js/realtime-sse.js` | Modify | Sinkronkan file public runtime SSE dengan implementasi `resources/js/services/realtime.js`. |
| `resources/views/layouts/peserta.blade.php` | Modify | 1) Pindahkan container `#toastFloatingContainer` ke luar elemen `<main class="content-area">` agar terisolasi dari layout grid dashboard. 2) Tambahkan opsi `actionUrl` pada method `window.MagKomToast.show()` agar tombol link rincian dapat ditampilkan selaras. |
| `resources/views/components/sse-toast-listener.blade.php` | Modify | Hilangkan elemen kontainer usang berkelas Tailwind (`#magkom-toast-container`) dan pastikan terhubung langsung ke `#toastFloatingContainer`. |
| `tests/Feature/RealtimeEventServiceTest.php` | Create / Modify | Buat/perbarui feature test untuk memastikan event yang memiliki `read_at` tidak terambil lagi oleh `getPendingEvents()`. |

---

## 5. Langkah-Langkah Implementasi

### Tahap 1: Backend - Pengendalian Frekuensi Event (Muncul 1 Kali Saja)
- [x] Buka `app/Services/RealtimeEventService.php`.
- [x] Perbarui method `getPendingEvents()` untuk menyertakan kondisi `->whereNull('read_at')`.
- [x] Pastikan endpoint `POST /realtime/read/{id}` di `RealtimeStreamController` dapat menerima request dengan lancar dan mengembalikan JSON status keberhasilan.

### Tahap 2: Frontend JS - Penghapusan Suara & Integrasi Tema
- [x] Buka `resources/js/services/realtime.js`.
- [x] Hapus pemanggilan dan fungsi audio synth `playNotificationChime()`.
- [x] Perbarui `handleEvent()`: Saat menerima event, simpan ID event ke `sessionStorage` dan kirim request asynchronous `fetch('/realtime/read/' + eventId, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken } })`.
- [x] Sesuaikan method `showToast()` agar menggunakan `window.MagKomToast` jika tersedia, atau merender komponen `.app-toast` dengan tema Sleman/MagKom murni tanpa dependensi kelas Tailwind.
- [x] Sinkronkan perubahan ke `public/js/realtime-sse.js`.

### Tahap 3: Blade Layout - Isolasi Kontainer Toast dari Grid Dasbor
- [x] Buka `resources/views/layouts/peserta.blade.php`.
- [x] Pindahkan tag `<div class="toast-floating-container" id="toastFloatingContainer">` agar berada tepat sebelum penutup `</body>` (atau langsung di bawah `<body>`), bukan di dalam `<main class="content-area">`.
- [x] Perbarui fungsi `window.MagKomToast.show` di layout peserta agar mendukung parameter opsional `actionUrl` dengan styling tombol Sleman Navy yang rapi.
- [x] Perbarui `resources/views/components/sse-toast-listener.blade.php` untuk menghapus duplikasi kontainer `#magkom-toast-container`.

### Tahap 4: Build Asset & Quality Gate
- [x] Jalankan `npm run build` untuk mengompilasi asset Vite.
- [x] Jalankan pengujian PHPUnit (`php artisan test tests/Feature/RealtimeEventServiceTest.php tests/Feature/RealtimeTugasLogbookFeedbackTest.php`).
- [x] Jalankan linter Laravel Pint (`./vendor/bin/pint --test`).

---

## 6. Strategi Pengujian (Testing Strategy)

1. **Unit / Feature Testing**:
   - Menguji bahwa `RealtimeEventService::getPendingEvents` hanya mengembalikan event dengan `read_at === null`.
   - Menguji bahwa setelah `markAsRead($id, $user)` dipanggil, pemanggilan kedua `getPendingEvents` tidak lagi menyertakan event tersebut.
2. **Verifikasi Manual di Browser**:
   - Buka dasbor peserta `http://127.0.0.1:8000/peserta/dashboard`.
   - Verifikasi tidak ada bunyi audio chime sama sekali saat halaman dimuat maupun saat mengklik elemen halaman.
   - Verifikasi bahwa pop-up muncul 1 kali, dan setelah di-refresh (F5), pop-up yang sama **tidak muncul kembali**.
   - Verifikasi bahwa grid layout 2 kolom dasbor peserta (`saas-cockpit-grid`: kolom konten utama dan aside) tetap kokoh, sejajar, dan tidak bergeser saat pop-up muncul maupun saat ditutup.
   - Verifikasi tampilan visual pop-up menggunakan gaya rounded badge MagKom dengan warna status yang sesuai (info: biru, success: hijau, warning: kuning, error: merah).

---

## 7. Risiko & Mitigasi

- **Risiko**: Pengguna membuka 2 tab sekaligus dan notifikasi hanya terkirim ke tab yang memproses lebih dulu.
  - *Mitigasi*: Event ditandai `read_at` setelah dikonsumsi. Tab lain yang sudah terbuka tetap menerima event secara bersamaan melalui koneksi SSE aktif masing-masing.
- **Risiko**: CSRF Token kedaluwarsa saat memanggil endpoint `/realtime/read/{id}`.
  - *Mitigasi*: Request fetch membaca token dari meta tag `<meta name="csrf-token">` yang selalu diperbarui saat halaman direfresh.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Tidak ada efek suara/audio synth notifikasi saat event tiba maupun saat diklik.
- [x] Pop-up notifikasi hanya muncul 1 kali untuk setiap event dan tidak berulang setiap kali me-refresh halaman.
- [x] Grid layout dashboard peserta (`saas-cockpit-grid`) tidak goyang, tidak turun, dan tidak mengalami pergeseran tata letak saat pop-up muncul di pojok kanan atas.
- [x] Tampilan pop-up selaras 100% dengan tema MagKom Toast (warna, border, font, rounded badge).
- [x] Seluruh test otomatis PHPUnit lulus dan Laravel Pint berstatus `passed`.

