# MASTER PLAN & IMPLEMENTATION TASK SPECIFICATION
## Menghubungkan Visualisasi Dashboard ke Logic Riil (4 Role: Peserta, Pembimbing, Admin, SuperAdmin)
**Dokumen Referensi:** PRD MagKom v1.2, TDD v1.2, RBAC Matrix, Database Data Dictionary, Design System v1.0  
**Target Pelaksana:** Junior Developer / AI Agents (Gemini 3.8 Flash / Antigravity Assistant)  
**Tingkat Kerumitan:** Menengah (Intermediate Engineering)  
**Status:** Draf Rencana Kerja Eksekusi  

---

## 1. PANDUAN DASAR & ATURAN MUTLAK (GROUND RULES)

Sebelum mulai menulis kode, setiap pengembang atau AI wajib mematuhi aturan repositori berikut:

1. **LARANGAN KERAS BLADE MODIFICATION**:
   - Dilarang menulis tag `<style>` atau `<script>` di dalam file Blade (`.blade.php`).
   - Dilarang menambahkan inline event handlers seperti `onclick="..."`, `onsubmit="..."`, `onchange="..."`.
   - Gunakan atribut semantik `data-action="..."`, `data-id="..."`, atau class selector untuk ditangkap oleh modul JavaScript di `resources/js/`.
2. **ARSITEKTUR FRONTEND**:
   - Semua interaksi JavaScript baru atau penyesuaian harus berada di `resources/js/` (ES Modules).
   - Komunikasi data ke backend menggunakan `window.axios` yang sudah terpasang token CSRF otomatis.
   - Umpan balik visual ke pengguna menggunakan toast terpusat: `window.MagKomToast.success()`, `window.MagKomToast.error()`, atau `window.MagKomToast.warning()`.
   - Komponen Drawer / Modal menggunakan engine cockpit yang sudah ada di `resources/js/layouts/dashboard-cockpit.js` (`data-action="open-drawer"`, `data-action="close-drawer"`).
3. **ARSITEKTUR BACKEND (LARAVEL 10 / PHP 8.3)**:
   - Pola: **Route → Middleware/Policy → Controller (Skinny) → Service (Fat Logic) → Model**.
   - Query ke database wajib menggunakan Eloquent dengan Eager Loading (`with()`) untuk mencegah N+1 query problem.
   - Mutasi data sensitif (kuota, status pendaftaran, penempatan) wajib di dalam database transaction (`DB::transaction`) dan menggunakan `lockForUpdate()` pada record kuota (BR-05).
   - Seluruh mutasi data harus tercatat pada `AuditLog` via `AuditService` (BR-11 / F-13).
   - Isolasi SuperAdmin: SuperAdmin **hanya boleh membaca (Read-Only)** pada domain pembimbingan (tugas, logbook, nilai). Policy harus menolak mutasi dari SuperAdmin.

---

## 2. PETA ARSITEKTUR & STATUS SAAT INI (AS-IS VS TO-BE)

| Role Dashboard | Status Visual Saat Ini | Masalah / Kesenjangan (Gaps) | Target Status (To-Be) |
|---|---|---|---|
| **Peserta** | Bento Grid & KPI Cards sudah tampil modern | Tombol aksi cepat belum semua mengirim form riil, data kalender belum dinamis penuh, alur pembatalan & unduh SK perlu validasi token | 100% interaktif: Pengisian logbook kilat via drawer, hitung sisa hari & progress otomatis, unduh SK/Sertifikat valid, kalender menandai deadline tugas riil |
| **Pembimbing** | Bento Grid & 4 Tab Eksekutif tersedia | Tab switching belum menyimpan state, tombol reviu logbook kilat hanya pratinjau teks belum ada aksi setujui/revisi, tombol "Selesai 1-Klik" belum terpasang dialog konfirmasi | 4 Tab bekerja mulus, reviu logbook kilat dapat langsung menyetujui/meminta revisi, penetapan "Selesai 1-Klik" (BR-10) mutasi status & hitung masa tenggang (BR-11), ekspor portofolio kinerja ASN berfungsi |
| **Administrasi** | Cockpit Dashboard & Antrean FIFO | Filter bulan magang belum memuat metrik secara AJAX/query segar, tombol verifikasi berkas kilat di drawer belum langsung melakukan putusan cepat (Terima/Tolak) | Filter bulan dinamis, antrean verifikasi FIFO menampilkan indikator SLA kritis (>3 hari), drawer verifikasi kilat memiliki tombol putusan cepat atomik dengan kuota locking (BR-05 & BR-06) |
| **SuperAdmin** | Bento Executive Hub & Multi-Tab | Kran Toggle buka-tutup bulan belum mengirim AJAX mutasi riil, trigger backup snapshot belum memiliki loading state & toast, filter audit log belum reaktif | Kran status bulan terhubung AJAX instan (BR-04), trigger backup database menghasilkan file `.sql.gz` terverifikasi SHA-256 dengan visual progress, inspeksi drawer jejak audit interaktif |

---

## 3. PEMBAGIAN TUGAS SPESIFIK (SPRINT TASK SPECIFICATION)

---

### TASK-DSH-01: WIRING DASHBOARD PESERTA (PARTICIPANT PORTAL)

#### 1. Tujuan & Aturan Bisnis Terkait
Memastikan peserta magang dapat memantau status fase (`onboarding`, `seleksi`, `aktif`, `selesai`), mengirim logbook harian langsung dari dashboard, memantau tenggat tugas, dan mengunduh berkas SK magang resmi.  
**Aturan Bisnis:** BR-03 (Pendaftaran), BR-06 (Status), BR-08 (Durasi), BR-09 (Logbook mingguan), BR-16 (Status Terlambat/Mendekati deadline).

#### 2. File Terkait
- Controller: `app/Http/Controllers/Peserta/DashboardController.php`
- Views: `resources/views/peserta/dashboard.blade.php`, `resources/views/peserta/dashboard/partials/*`
- JavaScript: `resources/js/pages/peserta/dashboard.js`
- Route: `routes/web.php` (`peserta.dashboard`, `peserta.logbook.store`, `peserta.penempatan.surat-diterima`)

#### 3. Rincian Pekerjaan Teknis (Step-by-Step)
1. **Validasi & Integrasi Drawer Logbook Kilat (`_modal_quick_logbook.blade.php`)**:
   - Pastikan form id `#quickLogbookForm` mengirim data ke route `peserta.logbook.store` dengan method `POST`.
   - Field yang dikirim: `tanggal` (wajib, max hari ini, min 3 hari ke belakang per BR-09), `kegiatan` (wajib, min 20 karakter), `link_laporan` (opsional), `file_laporan` (opsional, pdf, max 5MB).
   - Di `resources/js/pages/peserta/dashboard.js`, pasang event listener submit pada form:
     - Tampilkan state loading pada tombol submit (`disabled`, ubah teks jadi "Menyimpan...").
     - Kirim via `window.axios.post(form.action, new FormData(form))`.
     - Jika berhasil: panggil `window.MagKomToast.success('Logbook harian berhasil disimpan!')`, tutup drawer, dan perbarui angka KPI logbook di DOM atau reload halus.
     - Jika gagal validasi (HTTP 422): render pesan error di bawah input terkait dan tampilkan `window.MagKomToast.error(pesan)`.
2. **Kalkulasi Metrik & Kalender Riil**:
   - Di `DashboardController.php`, pastikan `$calendarEventDates` berisi array asosiatif tanggal riil dari tugas (`deadline`) dan logbook (`tanggal`):
     ```php
     $calendarEvents = [];
     foreach ($penempatan->logbooks as $lb) {
         $calendarEvents[$lb->tanggal->format('Y-m-d')] = ['type' => 'logbook', 'status' => $lb->status->value];
     }
     foreach ($publishedTugas as $tg) {
         if ($tg->deadline) {
             $calendarEvents[$tg->deadline->format('Y-m-d')] = ['type' => 'tugas', 'status' => $tg->status->value];
         }
     }
     ```
   - Masukkan data ini ke elemen HTML via `data-events="{{ json_encode($calendarEvents) }}"` pada kontainer kalender.
   - Di `resources/js/pages/peserta/dashboard.js`, parser kalender membaca data ini dan memberi indikator dot warna (Hijau untuk logbook terisi, Merah/Kuning untuk deadline tugas).
3. **Tombol Aksi Unduh SK Magang**:
   - Pastikan link "Unduh SK" pada hero banner (`_bento_hero.blade.php`) mengarah ke route `route('peserta.penempatan.surat-diterima', $penempatan)`.
   - Tambahkan atribut download dan icon loading saat proses unduh dimulai.

#### 4. Kriteria Keberhasilan (Definition of Done)
- [ ] Peserta dapat membuka drawer isi logbook dari tombol dashboard, mengisi form, dan berhasil menyimpan logbook baru tanpa reload halaman penuh (AJAX) atau dengan flash message yang rapi.
- [ ] Tanggal pada kalender mini menampilkan indikator titik (dot) pada hari di mana peserta memiliki logbook atau tenggat tugas riil.
- [ ] Card "Sisa Hari Magang" menghitung hari secara akurat berdasarkan rentang `tgl_mulai` dan `tgl_selesai` riil.
- [ ] Tombol "Unduh SK" berhasil mengunduh dokumen PDF bertandatangan digital/QR code MagKom.

---

### TASK-DSH-02: WIRING DASHBOARD PEMBIMBING (SUPERVISOR PORTAL)

#### 1. Tujuan & Aturan Bisnis Terkait
Memfungsikan Action Center pembimbing agar dapat meninjau antrean reviu logbook mentee secara instan, mengevaluasi progres tugas, memantau batas beban bimbingan, serta mengeksekusi "Penyelesaian Bimbingan 1-Klik" (BR-10) tanpa rubrik rumit.  
**Aturan Bisnis:** BR-09 (Reviu logbook), BR-10 (Penyelesaian 1-Klik), BR-11 (Masa tenggang), BR-13 (Beban maks pembimbing), BR-16 (Deteksi keterlambatan).

#### 2. File Terkait
- Controller: `app/Http/Controllers/Pembimbing/DashboardController.php`
- Service: `app/Services/PembimbingDashboardMetricsService.php`
- Views: `resources/views/pembimbing/dashboard.blade.php`, `resources/views/pembimbing/dashboard/partials/*`
- JavaScript: `resources/js/pages/pembimbing/dashboard.js`
- Routes: `routes/web.php` (`pembimbing.dashboard`, `pembimbing.logbook.review`, `pembimbing.progres.review`, `pembimbing.penempatan.selesai`)

#### 3. Rincian Pekerjaan Teknis (Step-by-Step)
1. **Navigasi 4 Tab Eksekutif Berbasis State URL**:
   - Di `resources/js/pages/pembimbing/dashboard.js`, implementasikan listener klik pada tab header:
     - Sinkronkan tab aktif dengan hash URL (`#ringkasan`, `#antrean`, `#mentee`, `#evaluasi`).
     - Tampilkan panel tab yang sesuai dan sembunyikan panel lainnya dengan transisi halus (`display: block` / `none`).
     - Simpan preferensi tab di `sessionStorage` agar saat refresh halaman, tab yang terbuka tidak kembali ke tab awal.
2. **Reviu Cepat Logbook via Drawer (`_modal_review_logbook.blade.php`)**:
   - Tombol "Review" pada baris logbook antrean harus membawa data-attributes:
     `data-action="open-drawer" data-drawer="#drawerReviewLogbook" data-logbook-id="{{ $logbook->id }}" data-mentee="{{ $logbook->penempatan->peserta->nama }}" data-tanggal="{{ $logbook->tanggal->isoFormat('D MMMM Y') }}" data-kegiatan="{{ $logbook->kegiatan }}" data-url="{{ route('pembimbing.logbook.index', ['highlight' => $logbook->id]) }}"`.
   - Modifikasi drawer untuk menyediakan dua tombol aksi cepat di footer:
     - Tombol 1: **Setujui Langsung** (`POST` ke `/pembimbing/logbook/{id}/review` dengan payload `status=disetujui`).
     - Tombol 2: **Minta Revisi** (Menampilkan input catatan revisi, lalu `POST` dengan `status=revisi&catatan=...`).
   - Handler AJAX di `dashboard.js`:
     - Tangani klik tombol "Setujui Langsung" dengan konfirmasi cepat.
     - Kirim via Axios, tampilkan Toast sukses, hapus item logbook tersebut dari daftar antrean DOM, dan kurangi angka badge antrean secara live.
3. **Eksekusi Fitur BR-10: Penyelesaian Bimbingan 1-Klik**:
   - Di Tab 4 (Evaluasi & Penyelesaian), pada daftar mentee yang sudah memenuhi kriteria selesai, pasang tombol aksi:
     `<button type="button" class="btn btn-success btn-sm btn-selesaikan-mentee" data-penempatan-id="{{ $mentee->id }}" data-nama="{{ $mentee->peserta->nama }}">Tetapkan Selesai</button>`.
   - Buat modal konfirmasi khusus "Konfirmasi Penyelesaian Magang":
     - Menjelaskan bahwa penetapan ini akan mengubah status penempatan menjadi `selesai`, memicu perhitungan masa tenggang 2 bulan (BR-11), dan membuka akses sertifikat.
   - Pada saat submit form:
     - Kirim `POST` ke route `pembimbing.penempatan.selesai` (`/pembimbing/penempatan/{penempatan}/selesai`).
     - Berikan feedback `MagKomToast.success('Magang mentee telah resmi diselesaikan!')` dan perbarui badge status mentee di tabel.
4. **Ekspor Portofolio Kinerja Mentoring ASN**:
   - Pastikan link "Cetak Portofolio Kinerja" mengarah ke `route('pembimbing.laporan-kinerja.cetak')` (target `_blank` untuk print preview) dan "Ekspor Excel" ke `route('pembimbing.laporan-kinerja.export')`.

#### 4. Kriteria Keberhasilan (Definition of Done)
- [ ] 4 Tab Dashboard Pembimbing dapat berpindah mulus tanpa page reload penuh dan mengingat posisi tab saat refresh.
- [ ] Pembimbing dapat menyetujui logbook mentee langsung dari drawer dashboard dan item langsung berkurang dari antrean secara real-time.
- [ ] Tombol "Tetapkan Selesai" (BR-10) berhasil memanggil endpoint `penempatan.selesai` dan data mentee berpindah ke status Selesai dengan masa tenggang terhitung otomatis.
- [ ] Widget Beban Pembimbing menampilkan rasio riil terhadap kuota kapasitas maksimal (misal: 6/10 mentee aktif).

---

### TASK-DSH-03: WIRING DASHBOARD ADMINISTRASI (ADMIN PORTAL)

#### 1. Tujuan & Aturan Bisnis Terkait
Membuat Dashboard Admin menjadi pusat komando operasional pendaftaran (FIFO), monitoring ketersediaan kuota per jurusan, deteksi dini SLA verifikasi (>3 hari), dan penempatan peserta.  
**Aturan Bisnis:** BR-01 (Kuota umum), BR-05 (Penerimaan atomik & locking), BR-06 (Verifikasi tegas tanpa waiting list), BR-13 (Beban pembimbing).

#### 2. File Terkait
- Controller: `app/Http/Controllers/Admin/DashboardController.php`
- Service: `app/Services/AdminDashboardMetricsService.php`
- Views: `resources/views/admin/dashboard.blade.php`, `resources/views/admin/dashboard/partials/*`
- JavaScript: `resources/js/pages/admin/dashboard.js`
- Routes: `routes/web.php` (`admin.dashboard`, `admin.pendaftaran.index`, `admin.pendaftaran.putuskan`, `admin.pendaftaran.download-zip`)

#### 3. Rincian Pekerjaan Teknis (Step-by-Step)
1. **Filter Periode Bulan Magang Interaktif**:
   - Pada komponen dropdown seleksi bulan (`_header.blade.php`):
     `<select id="selectFilterBulanAdmin" class="form-control select-bulan" data-current="{{ $bulanAktif->id }}">`.
   - Di `resources/js/pages/admin/dashboard.js`, pasang event listener `change`:
     - Ketika pengguna memilih bulan lain, arahkan window location ke `{{ route('admin.dashboard') }}?bulan_id=` + value terpilih, atau lakukan fetch AJAX untuk merefresh section kuota & antrean.
     - Tambahkan spinner halus saat data sedang dimuat ulang.
2. **Action Hub Antrean Verifikasi FIFO & SLA Kritis**:
   - Di tabel antrean pendaftaran (`_queue_table.blade.php`), data diurutkan secara FIFO (`created_at ASC`).
   - Jika ada entri pendaftaran yang diajukan lebih dari 3 hari kerja, render badge peringatan:
     `<span class="badge badge-danger">SLA Kritis (>3 Hari)</span>`.
   - Tombol "Pratinjau Berkas" membuka drawer `#drawerVerifikasi` dengan menyuntikkan data:
     `data-nama`, `data-nomor`, `data-institusi`, `data-jurusan`, dan `data-url="{{ route('admin.pendaftaran.show', $p->id) }}"`.
3. **Penyempurnaan Drawer Verifikasi Cepat**:
   - Di `_drawer_verifikasi.blade.php`, tambahkan tombol tindakan langsung:
     - Tombol "Buka Verifikasi Lengkap" menuju ke `admin.pendaftaran.show`.
     - Tombol "Terima Cepat" (opsional, jika kuota jurusan masih tersedia).
4. **Tombol Unduh Bundel Berkas Pendaftaran (ZIP)**:
   - Pastikan link unduh bundel berkas mengarah ke `route('admin.pendaftaran.download-zip', ['bulan_id' => $bulanAktif->id])`.
   - Berikan feedback toast jika berkas sedang dipersiapkan di server.
5. **Widget Keterisian Kuota Umum & Slot Khusus**:
   - Di `_quota_monitoring.blade.php`, pastikan persentase progress bar dihitung riil:
     `($terisi / max(1, $jumlah_kuota)) * 100%`.
   - Tampilkan badge status otomatis:
     - Hijau jika terisi < 70%
     - Kuning/Amber jika terisi 70% - 99%
     - Merah jika kuota Penuh (100%).

#### 4. Kriteria Keberhasilan (Definition of Done)
- [ ] Mengubah dropdown bulan magang memuat data metrik kuota dan pendaftaran yang sesuai dengan bulan terpilih.
- [ ] Pendaftaran yang diajukan > 3 hari otomatis memiliki indikator visual SLA Kritis.
- [ ] Drawer verifikasi pendaftaran terbuka mulus dengan data dinamis calon peserta terpilih dan link verifikasi lengkap valid.
- [ ] Progress bar kuota per jurusan merefleksikan angka riil di database MySQL secara akurat.

---

### TASK-DSH-04: WIRING DASHBOARD SUPERADMIN (EXECUTIVE PORTAL)

#### 1. Tujuan & Aturan Bisnis Terkait
Menghubungkan dashboard eksekutif SuperAdmin dengan kontrol master sistem: kran buka/tutup bulan magang instan (BR-04), trigger cadangan database (BR-19), stream jejak audit multi-kanal (F-13), dan ringkasan eksekutif seluruh modul tanpa melanggar batasan Read-Only pada fungsi mentoring.  
**Aturan Bisnis:** BR-02 (Slot khusus), BR-04 (Override kran bulan), BR-15 (Staf pending password), BR-19 (Backup database), BR-21 (Role matrix).

#### 2. File Terkait
- Controller: `app/Http/Controllers/SuperAdmin/DashboardController.php`
- Service: `app/Services/SuperAdminExecutiveMetricsService.php`
- Views: `resources/views/superadmin/dashboard.blade.php`, `resources/views/superadmin/dashboard/partials/*`
- JavaScript: `resources/js/pages/superadmin/dashboard.js`
- Routes: `routes/web.php` (`superadmin.dashboard`, `superadmin.bulan.toggle`, `superadmin.backup.snapshot`, `superadmin.audit-log.index`)

#### 3. Rincian Pekerjaan Teknis (Step-by-Step)
1. **Fitur Kran Buka/Tutup Bulan Magang (BR-04 AJAX Toggle)**:
   - Pada baris status bulan di hero/ringkasan dashboard, terdapat switch/toggle kran buka-tutup:
     `<button type="button" class="btn-kran-toggle {{ $bulanAktif->status_buka ? 'active' : '' }}" data-action="toggle-kran-bulan" data-bulan-id="{{ $bulanAktif->id }}" data-status="{{ $bulanAktif->status_buka ? 1 : 0 }}">`.
   - Di `resources/js/pages/superadmin/dashboard.js`:
     - Tangkap event klik pada elemen dengan `data-action="toggle-kran-bulan"`.
     - Tampilkan modal/alert konfirmasi: "Apakah Anda yakin ingin membuka/menutup pendaftaran untuk periode ini?".
     - Saat dikonfirmasi, kirim request `PATCH` via Axios ke `{{ url('/superadmin/bulan') }}/${bulanId}/toggle`.
     - Saat sukses: ubah tampilan tombol secara instan (toggle class aktif/nonaktif, ubah teks label "Buka" / "Tutup") dan munculkan `window.MagKomToast.success('Status pendaftaran bulan berhasil diubah!')`.
2. **Trigger Backup Database Seketika (One-Click Snapshot BR-19)**:
   - Pada tile Cadangan Sistem / Disaster Recovery di dashboard:
     `<button type="button" class="btn btn-outline-primary btn-sm" id="btnTriggerQuickBackup" data-url="{{ route('superadmin.backup.snapshot') }}">Snapshot Basis Data</button>`.
   - Di `dashboard.js`:
     - Tangkap event klik tombol `#btnTriggerQuickBackup`.
     - Nonaktifkan tombol sementara dan ganti teks dengan spinner animasi ("Membuat cadangan basis data...").
     - Kirim `POST` ke route `superadmin.backup.snapshot`.
     - Tangani response JSON:
       - Berhasil: tampilkan `window.MagKomToast.success('Cadangan snapshot basis data berhasil dibuat! File: ' + res.data.filename)`.
       - Perbarui waktu "Terakhir Cadangan" di widget dashboard.
       - Gagal: munculkan `window.MagKomToast.error('Gagal membuat cadangan: ' + res.data.message)`.
3. **Inspeksi Cepat Jejak Rekam Audit Log via Drawer**:
   - Di tabel 5 jejak audit terkini (`_drawer_audit_detail.blade.php`), setiap baris memiliki tombol inspeksi:
     `data-action="open-drawer" data-drawer="#drawerAuditDetail" data-action-name="{{ $log->deskripsi }}" data-user="{{ $log->user?->nama ?? 'Sistem' }}" data-ip="{{ $log->ip_address }}" data-waktu="{{ $log->created_at->isoFormat('D MMMM Y, HH:mm:ss') }}"`.
   - Engine `dashboard-cockpit.js` sudah siap membaca data-attributes tersebut dan mengisinya ke `.cv-dyn-*` di dalam drawer.
   - Pastikan link di footer drawer mengarah tepat ke `route('superadmin.audit-log.index')`.
4. **Verifikasi Penegakan Aturan Read-Only SuperAdmin**:
   - Pastikan SuperAdmin tidak memiliki tombol mutasi pada fungsi bimbingan (misal: tombol 'Beri Tugas' atau 'Nilai Mentee' tidak boleh tampil atau dinonaktifkan dengan tooltip "Mode SuperAdmin: Pengawasan Read-Only").

#### 4. Kriteria Keberhasilan (Definition of Done)
- [ ] Kran toggle status buka/tutup bulan magang dapat diklik dan langsung mengubah status di database via AJAX dengan feedback toast sukses.
- [ ] Tombol snapshot backup database dapat diklik, memunculkan loading state, dan menghasilkan file backup terverifikasi tanpa error timeout.
- [ ] Baris tabel audit log dapat diklik untuk membuka drawer inspeksi dengan alamat IP dan deskripsi mutasi riil.
- [ ] Widget peringatan menampilkan jumlah staf baru yang belum mengganti password awal (BR-15).

---

## 4. PEDOMAN INTEGRASI JAVASCRIPT & AJAX (KODE STANDAR CONTOH)

Bagi Junior Developer atau AI yang mengimplementasikan interaksi di file `resources/js/pages/`, gunakan pola standar proyek berikut:

```javascript
/**
 * Contoh Pola Penanganan Form Interaktif Dashboard (ES Module)
 * File: resources/js/pages/peserta/dashboard.js
 */

export function initQuickLogbookForm() {
    const form = document.getElementById('quickLogbookForm');
    if (!form) return;

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;

        // 1. Set Loading State
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Menyimpan...';

        try {
            const formData = new FormData(form);
            const response = await window.axios.post(form.action, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });

            if (response.data.success || response.status === 200 || response.status === 201) {
                // 2. Beri Notifikasi Sukses
                if (window.MagKomToast) {
                    window.MagKomToast.success('Logbook harian berhasil dikirim!');
                }

                // 3. Tutup Drawer
                if (typeof window.openMagkomDrawer === 'function') {
                    // Atau trigger click penutup drawer
                    const closeBtn = document.querySelector('#drawerQuickLogbook [data-action="close-drawer"]');
                    if (closeBtn) closeBtn.click();
                }

                // 4. Reset Form & Refresh Data (atau reload ringan)
                form.reset();
                setTimeout(() => window.location.reload(), 800);
            }
        } catch (error) {
            console.error('Error submitting logbook:', error);
            let errorMessage = 'Terjadi kesalahan saat menyimpan data.';

            if (error.response && error.response.status === 422) {
                const errors = error.response.data.errors;
                errorMessage = Object.values(errors).flat().join('<br>');
            } else if (error.response && error.response.data && error.response.data.message) {
                errorMessage = error.response.data.message;
            }

            if (window.MagKomToast) {
                window.MagKomToast.error(errorMessage);
            } else {
                alert(errorMessage);
            }
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}
```

---

## 5. URUTAN PENGERJAAN & ESTIMASI (ROADMAP EKSEKUSI)

Untuk memastikan pekerjaan terstruktur dan tidak saling bentrok, kerjakan berdasarkan urutan prioritas berikut:

```
[Tahap 1: Persiapan & Audit Rute]
       │
       ▼
[Tahap 2: Task-DSH-01 Dashboard Peserta] ───► Test Form Logbook & Unduh SK
       │
       ▼
[Tahap 3: Task-DSH-02 Dashboard Pembimbing] ─► Test Tab Switching & Selesai 1-Klik
       │
       ▼
[Tahap 4: Task-DSH-03 Dashboard Admin] ─────► Test Filter Bulan & Antrean FIFO
       │
       ▼
[Tahap 5: Task-DSH-04 Dashboard SuperAdmin] ─► Test Kran Bulan & Quick Backup
       │
       ▼
[Tahap 6: Quality Gate & Full Verification] ─► PHPUnit, PHPStan, Pint, Vite Build
```

---

## 6. QUALITY GATE & COMMAND VERIFIKASI (TESTING CHECKLIST)

Sebelum menyatakan tugas selesai, pengembang atau AI wajib menjalankan seluruh perintah verifikasi berikut di terminal:

1. **Jalankan Unit & Feature Test Dashboard Terkait**:
   ```bash
   php artisan test --filter=Dashboard
   ```
2. **Jalankan Static Analysis (PHPStan/Larastan)**:
   ```bash
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ```
3. **Format Kode Backend (Laravel Pint)**:
   ```bash
   ./vendor/bin/pint --test
   ```
4. **Kompilasi Aset Frontend (Vite)**:
   ```bash
   npm run build
   ```
   *Pastikan build Vite selesai tanpa error atau peringatan modul hilang.*

---

## 7. MATRIKS RISIKO & MITIGASI (PANDUAN UNTUK JUNIOR DEV / AI)

| Potensi Masalah / Jebakan | Dampak | Solusi & Pencegahan Mutlak |
|---|---|---|
| **Menulis script/style di Blade** | Melanggar aturan repositori (SEC-02/Clean Architecture) | **Jangan pernah** tambahkan `<script>` atau `<style>` di Blade. Seluruh kode JS harus ditaruh di `resources/js/pages/` dan diexport secara modular. |
| **Lupa CSRF Token di Axios** | HTTP 419 Page Expired | `bootstrap.js` sudah mengatur header `X-CSRF-TOKEN` secara global dari meta tag. Cukup gunakan `window.axios`. |
| **N+1 Query pada Relasi Dashboard** | Database lambat saat load dashboard | Gunakan eager loading pada Controller/Service: `Penempatan::with(['peserta.profil', 'pembimbing', 'bidang', 'tugas', 'logbooks'])`. |
| **Race Condition pada Kuota** | Overbooking kuota (BR-01 & BR-05) | Gunakan `DB::transaction()` dan `lockForUpdate()` pada model `Kuota` saat proses penerimaan pendaftaran. |
| **IDOR pada Aksi Dashboard** | Peserta/Pembimbing mengakses data orang lain | Gunakan Policy atau pengecekan user ownership: `$pendaftaran->user_id === $request->user()->id` atau via `Authorize`. |
| **SuperAdmin mengubah bimbingan** | Pelanggaran aturan read-only SuperAdmin | Pastikan `TugasPolicy` dan `LogbookPolicy` menolak mutasi jika `auth()->user()->isSuperAdmin()`. |

---
**Persetujuan Rencana Kerja:**  
Dokumen rencana kerja ini siap dieksekusi oleh tim pengembang atau AI pelaksana. Mohon konfirmasi untuk memulai implementasi per tahapan tugas.
