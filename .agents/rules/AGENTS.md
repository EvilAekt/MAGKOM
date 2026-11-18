# MAGKOM — Permanent AI Development Rules

Dokumen ini adalah aturan proyek yang harus dibaca dan dipatuhi setiap kali AI agent bekerja di repository MAGKOM. Aturan ini menjaga hasil migrasi PHP 8.3, Laravel 10, Vite, keamanan, dan tampilan agar tidak perlu dimigrasikan ulang pada setiap fitur baru.

## 1. Prioritas dan prinsip utama

1. Perlakukan repository saat ini sebagai **stable engineering baseline**, bukan proyek yang perlu dimigrasikan ulang.
2. Jangan mengulang migrasi PHP, Laravel, Vite, CSS, JavaScript, atau refactoring arsitektur kecuali pengguna secara eksplisit meminta pekerjaan migrasi tersebut.
3. Pertahankan business logic, kontrak route/API, authorization, audit trail, perilaku fitur, dan UI/UX yang sudah ada.
4. Lakukan perubahan sekecil mungkin (*minimal, focused change*). Jangan memperluas scope pekerjaan.
5. Jangan melakukan redesign, modernization, cleanup massal, atau refactoring yang tidak diperlukan oleh tugas.
6. Jika instruksi pengguna bertentangan dengan aturan ini, jelaskan konflik dan minta persetujuan sebelum melakukan perubahan yang berisiko.
7. Jangan menyatakan sesuatu sudah diuji jika command atau verifikasi aktual belum dijalankan.

## 2. Baseline teknologi yang wajib dipertahankan

- PHP: **8.3.x**.
- Laravel: **10.x**; jangan upgrade ke Laravel 11/12/versi lain tanpa persetujuan eksplisit.
- Backend: Laravel native, Eloquent.
- View: Blade.
- Frontend: Vanilla CSS dan Vanilla JavaScript (ES Modules).
- Asset pipeline: Vite yang sudah digunakan repository.
- Database: MySQL; gunakan SQLite in-memory atau database khusus testing untuk test otomatis sesuai konfigurasi proyek.
- Testing: PHPUnit.
- Static analysis: PHPStan/Larastan sesuai konfigurasi repository.
- Formatting: Laravel Pint.

Jangan menambahkan React, Vue, Angular, Tailwind, Bootstrap, jQuery, library UI, bundler, atau framework baru hanya karena lebih nyaman. Penambahan dependency atau perubahan stack harus memiliki alasan teknis dan persetujuan pengguna terlebih dahulu.

## 3. Aturan khusus Blade — jangan menulis implementasi frontend di Blade

**Blade bukan tempat menulis CSS atau JavaScript.** Blade hanya menjadi lapisan markup/presentasi Laravel.

### 3.1 Larangan default

AI agent DILARANG secara default:

- Menulis CSS di `<style>` dalam file Blade.
- Menulis JavaScript di `<script>` dalam file Blade.
- Menambahkan inline event handler seperti `onclick`, `onchange`, `onsubmit`, `oninput`, dan sejenisnya.
- Menambahkan atribut `style="..."` untuk styling statis.
- Menaruh fungsi JavaScript global di Blade.
- Menaruh konfigurasi frontend kompleks atau business logic di Blade.
- Menyalin kembali CSS/JS dari `resources/` ke Blade.
- Membuat file Blade monolitik baru yang berisi markup, styling, dan interaksi sekaligus.

### 3.2 Blade read-only secara default

Jangan mengubah file `resources/views/**/*.blade.php` hanya untuk:

- Mengubah warna, ukuran, spacing, font, layout, atau tampilan.
- Memperbaiki interaksi yang seharusnya ditangani JavaScript.
- Menambahkan atau memindahkan CSS/JavaScript.
- Mengatasi masalah asset pipeline yang seharusnya diselesaikan di Vite/CSS/JS.
- Menyimpan business logic yang seharusnya berada di Controller/Service.

Jika tugas dapat diselesaikan melalui file CSS, JavaScript, Controller, Service, Form Request, Model, atau Policy tanpa mengubah markup Blade, pilih cara tersebut.

### 3.3 Pengecualian perubahan Blade

Perubahan markup Blade hanya boleh dilakukan apabila perubahan struktur HTML memang mutlak diperlukan, misalnya menambahkan elemen semantik, form, field, slot, atau container yang belum tersedia.

Dalam kondisi tersebut:

1. Jelaskan mengapa perubahan tidak dapat diselesaikan tanpa markup baru.
2. Sebutkan file Blade dan bagian yang perlu diubah.
3. Minta persetujuan eksplisit pengguna sebelum mengubah Blade.
4. Setelah disetujui, buat perubahan markup sekecil mungkin.
5. Jangan menyisipkan CSS, JavaScript, inline event handler, atau styling statis di Blade.
6. Letakkan styling di `resources/css/` dan interaksi di `resources/js/`.
7. Laporkan setiap file Blade yang diubah dan alasan spesifiknya.

Jangan menganggap permintaan membuat fitur baru sebagai izin otomatis untuk mengubah layout Blade yang sudah ada. Jika fitur benar-benar memerlukan markup baru, minta persetujuan untuk perubahan markup tersebut.

### 3.4 Isi dan keamanan Blade

- Gunakan output escaping Blade (`{{ }}`) untuk data yang tidak sepenuhnya tepercaya.
- Hindari `{!! !!}` kecuali konten memang sudah tepercaya atau telah disanitasi dengan benar dan alasannya jelas.
- Jangan menaruh password, token, secret, atau credential di atribut HTML maupun JavaScript.
- Jangan memindahkan business logic, query database, authorization, atau kalkulasi domain ke Blade.
- Gunakan route name yang sudah ada; jangan mengubah route contract tanpa persetujuan.

## 4. Aturan CSS — satu sumber kebenaran di resources/css

1. Semua CSS baru atau perubahan styling harus berada di `resources/css/`.
2. Ikuti struktur dan pola file yang sudah ada. Baca `resources/css/app.css`, direktori `base/`, `components/`, `layouts/`, `pages/`, dan `vite.config.js` sebelum menentukan lokasi file.
3. Gunakan CSS yang scoped sesuai tanggung jawab komponen, layout, atau halaman.
4. Gunakan design token yang sudah tersedia. Jangan membuat token duplikat atau hardcode nilai baru apabila token existing sudah sesuai.
5. Jangan menaruh CSS baru di `public/css/` atau Blade.
6. Jangan memindahkan ulang stylesheet yang sudah termigrasi.
7. Jangan mengubah nilai CSS existing hanya untuk preferensi estetika. Pertahankan warna, font, ukuran, spacing, grid, breakpoint, border, shadow, animasi, hover, dan transition.
8. Jangan menambahkan reset, global selector, atau rule yang berpotensi memengaruhi halaman lain tanpa analisis dampak.
9. Hindari selector terlalu umum. Utamakan scope komponen/layout/halaman.
10. Jangan memuat stylesheet yang sama melalui Vite dan `asset()` sekaligus. Audit urutan dan sumber pemuatan sebelum perubahan.
11. Jangan membuat entry point Vite baru jika import pada entry point existing sudah cukup. Jika entry point baru benar-benar diperlukan, jelaskan alasannya dan periksa seluruh pemanggilannya.
12. Jangan menjalankan build yang menimpa file tracked atau mengubah manifest di workspace utama tanpa izin; gunakan environment/salinan sementara bila diperlukan.

## 5. Aturan JavaScript — modular ES Modules di resources/js

1. Semua JavaScript baru atau perubahan interaksi harus berada di `resources/js/`.
2. Ikuti struktur existing: `components/`, `layouts/`, `services/`, dan `pages/`.
3. Gunakan ES Modules (`import`/`export`) dan pola inisialisasi yang sudah digunakan proyek.
4. Jangan menulis JavaScript baru di Blade atau `public/js/`.
5. Jangan menggunakan inline event handler.
6. Gunakan event listener terstruktur atau event delegation yang sesuai dengan pola existing.
7. Cegah listener terpasang berulang ketika modul dapat diinisialisasi lebih dari sekali.
8. Jangan membuat global variable/function kecuali ada kebutuhan kompatibilitas yang telah dibuktikan dan disetujui.
9. Jangan menginterpolasikan input tidak tepercaya ke `innerHTML`. Gunakan DOM API aman seperti `createElement()` dan `textContent`.
10. Validasi URL/action URL, protokol, selector, dan data dari server sebelum digunakan.
11. Jangan mengubah kontrak SSE, API, event, atau payload tanpa persetujuan dan pengujian.
12. Jangan memindahkan ulang file JS yang sudah dimigrasikan.

## 6. Vite dan asset loading

1. Vite tetap menjadi pusat pengelolaan asset frontend.
2. Sebelum menambah/mengubah asset, periksa `vite.config.js`, `resources/css/app.css`, `resources/js/app.js`, dan manifest/build setup.
3. Ikuti entry point dan pola `@vite` yang sudah dipakai halaman terkait.
4. Hindari pemuatan ganda antara asset legacy `public/css`/`public/js` dan bundle Vite.
5. Jangan menghapus asset legacy hanya karena terlihat tidak dipakai. Cari semua referensi dan verifikasi dependensinya terlebih dahulu.
6. Jangan membuat ulang seluruh pipeline, entry points, atau struktur folder untuk tugas fitur biasa.
7. Setelah perubahan asset, jalankan `npm run build` pada environment aman dan periksa hasilnya.
8. Jangan commit `public/build/`, `node_modules/`, atau hasil generated lain kecuali kebijakan repository secara eksplisit mengharuskannya.

## 7. Aturan backend dan pemisahan tanggung jawab

Ikuti arsitektur existing:

**Route → Middleware/Policy → Form Request → Controller → Service → Model/Eloquent → Database**

- Controller: HTTP orchestration dan response; jangan menambahkan business logic kompleks.
- Form Request: validasi dan authorization input sesuai pola proyek.
- Service: business logic, transaksi, dan operasi domain yang perlu digunakan ulang.
- Model: relasi, cast, scope, dan perilaku model.
- Policy/middleware: authorization dan role access.
- Jangan melakukan query atau kalkulasi domain di Blade.
- Gunakan transaction untuk operasi multi-write yang membutuhkan konsistensi.
- Periksa eager loading dan potensi N+1.
- Pertahankan nama route, payload, response, enum, relasi, dan perilaku yang sudah menjadi kontrak sistem.
- Jangan mengubah schema/database migration tanpa alasan dan persetujuan eksplisit.
- Jangan melakukan destructive database operation.

## 8. Keamanan wajib

- Pertahankan seluruh mitigasi SEC-01 sampai SEC-06 yang sudah ada.
- Jangan mengembalikan shared/default password.
- Jangan mencatat password, token, secret, atau credential dalam log, session, response, atau audit log.
- Gunakan hashing Laravel untuk password.
- Pertahankan CSRF protection dan logout POST-only.
- Pertahankan CSP dan security headers; perubahan origin harus memiliki alasan dan scope sekecil mungkin.
- Validasi dan authorize setiap input serta akses objek; jangan membuka IDOR.
- Jangan membangun SQL identifier dinamis dari input tanpa whitelist dan verifikasi schema.
- Jangan menonaktifkan middleware, Policy, rate limit, atau security test untuk membuat fitur bekerja.
- Jangan menampilkan isi `.env`, secret, private key, atau credential dalam output/log/laporan.
- Jangan melakukan penetration test terhadap sistem eksternal atau production tanpa izin eksplisit.

## 9. UI/UX dan business logic preservation

1. MAGKOM bukan proyek redesign. Jangan melakukan visual improvement atas inisiatif sendiri.
2. Perubahan fitur harus mempertahankan tampilan existing kecuali pengguna secara eksplisit meminta perubahan desain.
3. Untuk perubahan yang memengaruhi tampilan, identifikasi nilai CSS dan komponen existing sebelum mengubahnya.
4. Jangan mengubah typography, warna, spacing, ukuran, breakpoint, posisi, layout, animasi, ikon, gambar, atau teks UI tanpa persetujuan eksplisit.
5. Pertahankan seluruh alur pendaftaran, kuota, waiting list, penempatan, pembimbing, logbook, tugas, penilaian, audit log, realtime, dan modul SuperAdmin.
6. Jangan menghapus fitur, field, tombol, navigasi, assertion, atau behavior hanya agar implementasi lebih sederhana.
7. Jika perubahan berpotensi memengaruhi UI atau business logic, jelaskan dampaknya dan buat regression test yang relevan.

## 10. Workflow wajib sebelum mengubah kode

Untuk setiap tugas baru:

1. Baca aturan ini dan dokumentasi terkait.
2. Periksa branch dan working tree:
   - `git branch --show-current`
   - `git status --short`
3. Jangan mengasumsikan working tree bersih. Lindungi perubahan lokal pengguna.
4. Telusuri implementasi existing sebelum menulis kode.
5. Identifikasi file yang benar-benar perlu diubah.
6. Klasifikasikan dampak: backend, Blade markup, CSS, JS, database, security, CI, atau dokumentasi.
7. Buat perubahan sekecil mungkin; jangan mengulang migrasi yang sudah selesai.
8. Jika perubahan Blade, schema, dependency, security boundary, atau kontrak API diperlukan, jelaskan dan minta persetujuan eksplisit sebelum melakukannya.
9. Jangan membuat file kosong atau struktur folder yang tidak dibutuhkan.
10. Jangan melakukan cleanup di luar scope tugas.

## 11. Quality gate setelah perubahan

Gunakan PHP 8.3 aktual, bukan PHP 8.2 dan bukan `--ignore-platform-reqs`.

Jalankan pemeriksaan yang relevan dengan perubahan:

- PHPUnit test terkait.
- `php artisan test` untuk perubahan yang berdampak luas.
- `./vendor/bin/phpstan analyse --memory-limit=1G` untuk perubahan backend yang relevan.
- `./vendor/bin/pint --test` untuk memastikan formatting.
- `npm run build` untuk perubahan CSS/JS/Vite.
- Browser/visual check jika perubahan memengaruhi tampilan atau interaksi.

Database test harus terisolasi; jangan menjalankan test terhadap database development atau production.

Jangan mengubah atau melemahkan test agar hasil terlihat hijau. Jika test gagal, identifikasi root cause terlebih dahulu.

Laporkan environment dan versi runtime yang benar-benar digunakan. Jangan mengklaim verifikasi PHP 8.3 jika command berjalan dengan versi lain.

## 12. Git, commit, dan deployment

- Jangan menjalankan `git reset --hard`, `git clean`, `git restore`, atau operasi destruktif lainnya.
- Jangan menghapus atau menimpa perubahan lokal pengguna.
- Jangan stage, commit, push, merge, atau deploy kecuali pengguna meminta secara eksplisit.
- Jika diminta commit, periksa diff dan daftar file terlebih dahulu.
- Jangan memasukkan `.env`, secret, `vendor/`, `node_modules/`, cache, log, dump database, atau file sementara.
- Jangan menganggap file untracked otomatis aman untuk di-commit.

## 13. Format laporan setiap tugas

Setelah menyelesaikan tugas, laporkan:

1. Ringkasan perubahan.
2. Daftar seluruh file yang diubah/dibuat/dihapus.
3. Alasan setiap file perlu diubah.
4. Konfirmasi apakah Blade disentuh. Jika ya, jelaskan persetujuan dan alasan markup diperlukan.
5. Konfirmasi CSS/JS tetap berada di `resources/` dan dikelola Vite.
6. Test dan quality gate yang benar-benar dijalankan beserta hasilnya.
7. Risiko, keterbatasan, dan hal yang belum diverifikasi.
8. Status Git; jangan menyatakan bersih tanpa memeriksanya.

## 14. Aturan akhir yang tidak boleh dilanggar

**JANGAN MIGRASI ULANG.**

**JANGAN MENULIS CSS ATAU JAVASCRIPT DI BLADE.**

**JANGAN MENGUBAH BLADE TANPA KEBUTUHAN MARKUP YANG NYATA DAN PERSETUJUAN PENGGUNA.**

**SEMUA CSS BARU/EDIT BERADA DI `resources/css/`.**

**SEMUA JAVASCRIPT BARU/EDIT BERADA DI `resources/js/`.**

**SEMUA ASSET FRONTEND DIKELOLA MELALUI VITE.**

**PERTAHANKAN BUSINESS LOGIC, SECURITY, ROUTE CONTRACT, DAN UI/UX EXISTING.**

**UTAMAKAN PERUBAHAN KECIL, TERARAH, DAN DAPAT DIUJI.**

Jika tidak dapat mematuhi salah satu aturan tanpa mengubah scope pekerjaan, hentikan bagian yang berisiko, jelaskan hambatannya, dan minta persetujuan pengguna.
