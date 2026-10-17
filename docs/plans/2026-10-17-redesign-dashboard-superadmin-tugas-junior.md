# Plan: Redesign Dashboard SuperAdmin (Paket Tugas Senior → Junior / AI)

- **Tanggal**: 2026-10-17
- **Branch kerja**: `test-1` (buat branch turunan `feat/sa-dashboard-redesign`)
- **Status**: Draft, siap dikerjakan
- **Penulis**: Senior Dev
- **Pembaca**: Junior developer atau AI model yang lebih murah. Dokumen ini sengaja ditulis eksplisit. Kerjakan **per tugas, berurutan**, jangan loncat.

---

## 0. Baca Ini Dulu (5 menit)

### 0.1 Tujuan
Merombak **tampilan** halaman `superadmin.dashboard` (`/superadmin/dashboard`) agar lebih ringkas, mudah dipindai, konsisten dengan Design System, dan kodenya bisa dirawat. **Tidak ada perubahan logika bisnis.**

### 0.2 Yang BOLEH diubah
| File | Keterangan |
|---|---|
| `resources/views/superadmin/dashboard.blade.php` | Dipecah menjadi partial (Tugas 3–8) |
| `resources/views/superadmin/dashboard/partials/*` | **Baru** |
| `resources/views/components/sa/*` | **Baru** (komponen Blade anonim) |
| `public/js/superadmin-dashboard.js` | **Baru** (JS tab) |
| `app/Http/Controllers/SuperAdmin/DashboardController.php` | Hanya dirapikan (Tugas 2), **nama variabel view tidak boleh berubah** |
| `tests/Feature/SuperAdminDashboardRedesignTest.php` | **Baru** |

### 0.3 Yang DILARANG diubah
- `layouts/dashboard.blade.php` (sidebar, topbar, CSS global). Ada tes yang memeriksa isinya. Bila butuh gaya baru, taruh di `@push('styles')` pada view dashboard.
- Route, middleware, model, enum, migrasi, query (hanya boleh dipindah, bukan diubah hasilnya).
- Teks yang diperiksa tes (lihat 0.6).
- Halaman superadmin lain (staf, role, audit-log, dll.). Itu fase berikutnya.

### 0.4 Kondisi saat ini (hasil audit)
| Temuan | Angka / fakta | Dampak |
|---|---|---|
| View dashboard satu file | **1.689 baris**, **515** atribut `style="..."` inline | Sulit dibaca, sulit diubah |
| Warna di-hardcode | `#2563EB`, `#D97706`, `#16A34A`, `#EFF6FF`, dst. | Menyimpang dari token Design System |
| Controller | 339 baris, ±60 variabel dikirim ke view, semua dihitung walau tab tidak dibuka | Lambat, sulit dites |
| Hero kepenuhan | Judul, tombol, quick actions, status landing page, jadwal, postur keamanan, dan selektor bulan menumpuk di satu kartu | Sulit dipindai |
| Kartu statistik | Kartu "Total pengguna" dan "Peserta aktif" memuat 3–4 baris teks kecil | Angka utama tenggelam |
| Tab | 4 tab (`Ringkasan Utama`, `Seleksi & Kuota`, `Pelaksanaan & Bimbingan`, `Tata Kelola Sistem & Laporan`) memakai `onclick` + `<script>` inline | **Diblokir CSP produksi** (`script-src 'self'`, lihat `SecurityHeaders.php`) |
| Komponen Blade | Folder `resources/views/components` belum ada | Markup kartu/badge disalin-tempel puluhan kali |

> **Catatan keputusan senior**: `@vite` tidak dipakai di view mana pun. Jadi **jangan** menambah Tailwind/Vite. Gaya baru ditaruh di `@push('styles')`, JS di `public/js/`.

### 0.5 Token wajib (dari `docs/07-DESIGN-SYSTEM-MAGKOM.md`, sudah ada di layout sebagai CSS variable)
Pakai **hanya** variabel ini. Dilarang menulis hex baru.

| Kebutuhan | Variabel |
|---|---|
| Latar kartu | `var(--neutral-0)` |
| Latar area kerja | `var(--neutral-150)` |
| Border | `var(--neutral-400)` |
| Teks utama / sekunder | `var(--neutral-800)` / `var(--neutral-600)` |
| Aksi, link | `var(--blue-600)`, hover `var(--blue-700)`, latar ringan `var(--blue-tint)` |
| Sukses | `var(--green-100)` / `var(--green-700)` |
| Peringatan | `var(--amber-200)` / `var(--amber-700)` |
| Bahaya | `var(--red-200)` / `var(--red-700)` |
| Bayangan | `var(--shadow-subtle)`, `var(--shadow-raised)` |
| Font | `var(--font-sans)`; bobot `var(--fw-regular/medium/semibold/bold)` |

Aturan tipografi: ikuti `docs/plans/2026-10-16-standardisasi-tipografi-font-weight-magkom.md` (jangan pakai bobot 800).

### 0.6 Teks yang HARUS tetap muncul (dicek tes yang sudah ada)
Dari `SuperAdminFigmaDesignAlignmentTest` dan `SuperAdminDashboardAndAuditLogTest`:

`Dashboard SuperAdmin` · `Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi` · `Total pengguna` · `Menunggu verifikasi` · `Peserta aktif` · `Akun nonaktif` · `Pengguna terbaru` · `Kelola pengguna` · `Audit log terbaru` · `Lihat semua` · `Dashboard Eksekutif Tata Kelola MagKom` · `Jalur Umum` · `Jalur Khusus` · `Mengundurkan Diri` · `Perhatian Antrean Pendaftaran` · (nama pengguna dan aksi audit dari data) · plus teks sidebar dari layout (tidak disentuh).

Jika sebuah teks mau diganti, **jangan**. Tanyakan ke senior.

### 0.7 Cara kerja per tugas
1. Buat branch, jalankan tes dasar **sebelum** mengubah apa pun (Tugas 1).
2. Satu tugas = satu commit. Format: `feat(superadmin): <apa yang dilakukan>` atau `refactor(superadmin): ...`.
3. Setelah tiap tugas: `./vendor/bin/pint` lalu `php artisan test --filter=SuperAdmin`. Wajib hijau sebelum lanjut.
4. Buka halaman di browser (ukuran 1440px dan 390px) dan bandingkan dengan sebelum.
5. Bila ragu atau tes merah dan tidak paham sebabnya: **berhenti dan tanya**. Jangan menghapus atau melonggarkan tes.

---

## 1. Desain Target

### 1.1 Prinsip
1. **Angka dulu, penjelasan belakangan.** Satu angka besar + satu baris keterangan.
2. **"Perlu tindakan" selalu di atas**, bisa diklik langsung ke halaman terkait.
3. **Satu kartu = satu tujuan.** Tidak ada kartu yang merangkap tiga fungsi.
4. **Status memakai warna + ikon + teks** (aksesibilitas, NFR-U3).
5. Mobile 390px tetap terbaca: grid satu kolom, tab bisa digeser.

### 1.2 Wireframe (desktop)

```text
┌──────────────────────────────────────────────────────────────────────┐
│ [A] PAGE HEADER                                                      │
│  Dashboard SuperAdmin                  [Periode: Okt 2026 ▾] [Kelola │
│  Dashboard Eksekutif Tata Kelola MagKom ● Sistem aktif   pengguna][Audit]
├──────────────────────────────────────────────────────────────────────┤
│ [B] PERLU TINDAKAN  (hanya tampil jika ada item; chip bisa diklik)   │
│  ⚠ 7 antrean verifikasi   ⏱ 2 lewat SLA   ⌛ 3 waiting list         │
│  ⚑ 4 tugas terlambat      🔑 1 staf belum ganti sandi   🛡 5 login gagal│
├──────────────┬──────────────┬──────────────┬─────────────────────────┤
│ [C] KPI      │              │              │                         │
│ Total        │ Menunggu     │ Peserta      │ Akun                    │
│ pengguna     │ verifikasi   │ aktif        │ nonaktif                │
│ 128          │ 7            │ 42           │ 3                       │
│ +1 baris ket.│ +1 baris     │ Umum·Khusus  │ +1 baris                │
├──────────────┴──────────────┴──────────────┴─────────────────────────┤
│ [D] TAB:  Ringkasan | Seleksi & Kuota (7) | Pelaksanaan (4) | Sistem │
├──────────────────────────────────┬───────────────────────────────────┤
│ [E] Pengguna terbaru             │ [F] Audit log terbaru             │
│  avatar · nama · email · chip    │  ikon · aksi · aktor · jam        │
│  [Kelola pengguna →]             │  [Lihat semua →]                  │
├──────────────────────────────────┴───────────────────────────────────┤
│ [G] KPI Dinas (4 kartu mini)   │ [H] Status landing page + jadwal    │
└──────────────────────────────────────────────────────────────────────┘
```

### 1.3 Pemetaan isi lama → posisi baru
| Isi lama (baris di file lama) | Pindah ke |
|---|---|
| Hero 14–60 (judul, tombol) | [A] Page header |
| Quick actions 58–110 | Dihapus dari hero; link-nya sudah ada di sidebar. Pertahankan **hanya** `Konfigurasi Kuota Periode Baru` bila dipakai tes (cek) |
| Status landing page + jadwal 120–160 | [H] panel di tab Ringkasan |
| Postur keamanan ringkas 162 | Tab Sistem (sudah ada panelnya) |
| Selektor bulan 171 | [A] kanan atas |
| Alert antrean 186–206 | [B] Perlu tindakan (teks `Perhatian Antrean Pendaftaran` tetap ada sebagai judul bagian) |
| 4 stat card 208–292 | [C] |
| Tab bar 294–340 | [D] |
| Tab ringkasan 344–594 | [E][F][G][H]; demografi institusi tetap di bawah |
| Tab seleksi 598–810 | Partial `_tab-seleksi` (isi sama, markup dirapikan) |
| Tab bimbingan 815–1030 | Partial `_tab-bimbingan` |
| Tab sistem 1034–1626 | Partial `_tab-sistem` |

---

## 2. Daftar Tugas

Legenda tingkat: 🟢 mudah (AI murah cukup) · 🟡 sedang (hati-hati) · 🔴 butuh review senior.

| # | Tugas | Tingkat | Bergantung pada | Perkiraan |
|---|---|---|---|---|
| 1 | Baseline: tes + screenshot "sebelum" | 🟢 | – | 30 mnt |
| 2 | Rapikan controller (pecah method, hasil sama) | 🟡 | 1 | 1,5 jam |
| 3 | Buat komponen Blade `x-sa.*` | 🟡 | 1 | 2 jam |
| 4 | Buat file gaya `_styles` + JS tab eksternal | 🟡 | 3 | 1,5 jam |
| 5 | Page header [A] + Perlu tindakan [B] + KPI [C] | 🟡 | 3, 4 | 2 jam |
| 6 | Tab Ringkasan [E][F][G][H] | 🟡 | 5 | 2 jam |
| 7 | Tab Seleksi & Kuota + Pelaksanaan | 🟢 | 5 | 2 jam |
| 8 | Tab Sistem & Laporan | 🟢 | 5 | 1,5 jam |
| 9 | Responsif, aksesibilitas, empty state | 🟡 | 6–8 | 1,5 jam |
| 10 | Tes baru + pembersihan + PR | 🟡 | 9 | 1,5 jam |

---

### Tugas 1 🟢 — Baseline
**Tujuan**: punya bukti "sebelum" agar tidak ada yang rusak diam-diam.

Langkah:
1. `git checkout test-1 && git checkout -b feat/sa-dashboard-redesign`
2. `composer install && cp .env.example .env && php artisan key:generate` (sesuaikan DB uji, ikuti README).
3. Jalankan: `php artisan test --filter=SuperAdmin` dan `php artisan test --filter=DashboardLayoutTest`. Catat hasilnya di deskripsi PR.
4. Jalankan seeder, login sebagai superadmin, ambil screenshot `/superadmin/dashboard` di 1440px dan 390px (semua 4 tab). Simpan di `docs/plans/assets/sa-dashboard-before/` (jangan commit bila > 2 MB; lampirkan di PR saja).

**Selesai bila**: tes dasar tercatat (hijau/merah apa adanya), 8 screenshot ada.

---

### Tugas 2 🟡 — Rapikan DashboardController
**Tujuan**: kode terbaca, **hasil view identik**.

File: `app/Http/Controllers/SuperAdmin/DashboardController.php`

Langkah:
1. Biarkan `index()` sebagai pintu masuk. Pecah isinya menjadi method `private` yang masing-masing **mengembalikan array**:
   - `ringkasanMetrik()` → `totalPengguna, menungguVerifikasi, pesertaAktif, akunNonaktif, penggunaTerbaru, totalAlumni, totalPesertaMundur, totalPesertaMasaTenggang, stafPendingGantiPassword, totalPesertaJalurUmum, totalPesertaJalurKhusus, totalAdmin, totalPembimbing`
   - `periodeDanKuota(Request $request)` → bagian bulan, status publik, slot khusus, kuota
   - `seleksi()` → `pendaftaranMenunggu, totalAntreanDiajukan, totalWaitingList`
   - `bimbingan()` → logbook, tugas, beban pembimbing, off-boarding
   - `parameterSistem()` → parameter PRD Bab 8.6
   - `demografi()`, `auditTerbaru()`, `kpiDinas($bulanAktif)`, `keamanan()`, `kesehatanSistem()`
2. Di `index()`: `return view('superadmin.dashboard', array_merge($a, $b, ...));`
3. Tambahkan variabel baru **`$perluTindakan`** (array berisi item untuk [B]), dibangun dari variabel yang sudah ada, tanpa query baru:
   ```php
   $perluTindakan = collect([
       ['label' => 'Antrean verifikasi', 'jumlah' => $totalAntreanDiajukan,        'tone' => 'warning', 'route' => 'admin.pendaftaran.index'],
       ['label' => 'Melewati SLA 5 hari','jumlah' => $antreanMelebihiSla,          'tone' => 'danger',  'route' => 'admin.pendaftaran.index'],
       ['label' => 'Waiting list',       'jumlah' => $totalWaitingList,            'tone' => 'info',    'route' => 'admin.waiting-list.index'],
       ['label' => 'Tugas terlambat',    'jumlah' => $totalTugasTerlambat,         'tone' => 'danger',  'route' => 'superadmin.monitoring.index'],
       ['label' => 'Staf belum ganti sandi','jumlah' => $stafPendingGantiPassword, 'tone' => 'warning', 'route' => 'superadmin.staf.index'],
       ['label' => 'Login gagal 24 jam', 'jumlah' => $loginGagal24Jam,             'tone' => 'danger',  'route' => 'superadmin.audit-log.index'],
   ])->filter(fn ($i) => $i['jumlah'] > 0)->values();
   ```
   Cek nama route `admin.waiting-list.index` dengan `php artisan route:list --name=waiting`. Jika beda, pakai nama yang benar.

Dilarang: mengubah query, nama variabel, atau `declare(strict_types=1)`.

**Selesai bila**: `php artisan test --filter=SuperAdmin` hasilnya sama dengan Tugas 1; `./vendor/bin/pint --test` bersih; halaman terlihat sama persis seperti sebelumnya.

---

### Tugas 3 🟡 — Komponen Blade `x-sa.*`
**Tujuan**: hentikan salin-tempel markup. Laravel 10 mendukung komponen anonim, cukup buat file.

Buat folder `resources/views/components/sa/` dengan file berikut. Semua tanpa `style=""` inline; kelas CSS dibuat di Tugas 4 (awalan `sa-`).

| File | Pemakaian | Props |
|---|---|---|
| `panel.blade.php` | `<x-sa.panel title="..." subtitle="..." :href="route(...)" link-label="Lihat semua">…isi…</x-sa.panel>` | `title`, `subtitle=null`, `href=null`, `linkLabel=null` |
| `stat-card.blade.php` | `<x-sa.stat-card label="Total pengguna" :value="$totalPengguna" tone="blue" icon="users" hint="Seluruh akun terdaftar" />` | `label`, `value`, `tone` (`blue/amber/green/red`), `icon`, `hint=null`, `meta=null` |
| `badge.blade.php` | `<x-sa.badge tone="success">Aktif</x-sa.badge>` | `tone` (`neutral/info/success/warning/danger`) |
| `avatar.blade.php` | `<x-sa.avatar :name="$u->nama" />` (inisial 2 huruf) | `name`, `size=36` |
| `empty-state.blade.php` | `<x-sa.empty-state icon="inbox" text="Belum ada data" />` | `icon`, `text` |
| `icon.blade.php` | `<x-sa.icon name="users" />` | `name`, `size=18` |
| `action-chip.blade.php` | chip di [B]: `<x-sa.action-chip :href=... tone=... :count=... label=... />` | |

Aturan:
- `icon.blade.php`: satu `@switch($name)` berisi SVG outline 24×24 yang **disalin dari SVG yang sudah ada** di `dashboard.blade.php` (stroke `currentColor`, `stroke-width=2`). Jangan menambah pustaka ikon. Ikon yang dibutuhkan: `users, inbox, clipboard-check, lock, file-text, alert, clock, key, shield, activity, settings, chevron-right`.
- Jangan emoji di markup produksi (emoji di wireframe hanya ilustrasi).
- Gunakan `@props([...])` dan `$attributes->merge(['class' => 'sa-panel'])`.

**Selesai bila**: tiap komponen bisa dirender di route percobaan sementara (hapus sebelum commit) tanpa error; tidak ada `style=` di file komponen.

---

### Tugas 4 🟡 — Gaya & JS eksternal
**Tujuan**: satu sumber gaya, JS patuh CSP.

1. Buat `resources/views/superadmin/dashboard/partials/_styles.blade.php`, isinya satu blok `<style>` dengan kelas berawalan `sa-`:
   `sa-page`, `sa-header`, `sa-header__title`, `sa-header__actions`, `sa-attention`, `sa-chip` (+ `--warning/--danger/--info`), `sa-grid-kpi` (4 kolom ≥1100px, 2 kolom ≥640px, 1 kolom di bawahnya), `sa-card`, `sa-card__head`, `sa-stat`, `sa-stat__value`, `sa-grid-2`, `sa-list`, `sa-list__item`, `sa-badge--*`, `sa-avatar`, `sa-table`, `sa-progress`, `sa-empty`.
   - Hanya `var(--...)` untuk warna. Radius kartu `12px`, padding kartu `1.25rem`, gap grid `1rem`.
   - Fokus terlihat: `:focus-visible { box-shadow: var(--focus-ring); outline: none; }`.
   - Target klik minimal 44px pada chip dan tombol.
   - **Jangan** menimpa `.tab-btn`, `.dashboard-tab-bar`, `.tab-content-panel` yang sudah didefinisikan di layout; pakai apa adanya.
2. Di `dashboard.blade.php` tambahkan `@push('styles') @include('superadmin.dashboard.partials._styles') @endpush`.
3. Buat `public/js/superadmin-dashboard.js`: pindahkan fungsi `switchDashboardTab` dan inisialisasi hash/localStorage dari `<script>` lama (baris 1629–1688), tetapi:
   - tanpa `onclick`; tombol tab memakai `data-tab="ringkasan"` dan JS memasang `addEventListener('click', ...)`
   - tambahkan navigasi keyboard panah kiri/kanan antar tab (`role="tablist"`)
   - tautan `javascript:void(0)` di alert lama diganti `<a href="#seleksi" data-tab-link="seleksi">`
4. Muat lewat `@push('scripts') <script src="{{ asset('js/superadmin-dashboard.js') }}" defer></script> @endpush`.

**Selesai bila**: tab berfungsi (klik, hash `#sistem`, refresh mengingat tab) dan **tidak ada** `<script>` inline maupun atribut `onclick` di view dashboard (`grep -n "onclick\|<script>" resources/views/superadmin/dashboard*` kosong).

---

### Tugas 5 🟡 — Header [A], Perlu Tindakan [B], KPI [C]
Buat partial di `resources/views/superadmin/dashboard/partials/`:

**`_header.blade.php`** [A]
- `h2` "Dashboard SuperAdmin"; di bawahnya `p` berisi persis kalimat `Akses penuh: kelola pengguna, role, rubrik, audit log, konfigurasi, dan seluruh modul Administrasi, Pembimbing, serta Peserta.`
- Badge kecil `Dashboard Eksekutif Tata Kelola MagKom` + titik hijau `status-dot-pulse` + "Sistem Operasional Aktif".
- Kanan: form GET selektor periode (pertahankan `name="bulan_id"`, `$daftarBulan`, nilai terpilih `$bulanAktif`) dan dua tombol: `Kelola Pengguna` (primer, `superadmin.staf.index`) dan `Audit Trail` (sekunder, `superadmin.audit-log.index`).

**`_perlu-tindakan.blade.php`** [B]
- Bungkus dengan `@if($perluTindakan->isNotEmpty())`. Judul panel: jika `$totalAntreanDiajukan > 0 || $totalWaitingList > 0` tulis **`Perhatian Antrean Pendaftaran`** (teks ini dicek tes lama), selain itu tulis `Perlu tindakan`. Jangan menyembunyikan teks dengan CSS agar lolos tes.
- Render `@foreach($perluTindakan as $item)` sebagai `<x-sa.action-chip>`.
- Bila kosong: `<x-sa.badge tone="success">Tidak ada yang perlu ditindaklanjuti</x-sa.badge>`.
- Tombol `Konfigurasi Kuota Periode Baru` **bukan** di sini; teks itu hanya berasal dari data audit (tidak perlu dibuat manual).

**`_kpi.blade.php`** [C]
Empat `<x-sa.stat-card>` dengan label persis: `Total pengguna`, `Menunggu verifikasi`, `Peserta aktif`, `Akun nonaktif`.
| Kartu | Nilai | `hint` (satu baris) | `meta` (opsional, satu baris) |
|---|---|---|---|
| Total pengguna | `$totalPengguna` | `Seluruh akun terdaftar` | `{{ $totalAdmin }} Admin · {{ $totalPembimbing }} Pembimbing` |
| Menunggu verifikasi | `$menungguVerifikasi` | `Berkas perlu ditinjau` | – |
| Peserta aktif | `$pesertaAktif` | `{{ $totalPesertaJalurUmum }} Jalur Umum · {{ $totalPesertaJalurKhusus }} Jalur Khusus` | `{{ $totalPesertaMundur }} Mengundurkan Diri` |
| Akun nonaktif | `$akunNonaktif` | `Akses ditangguhkan` | – |

`$stafPendingGantiPassword`, `$totalAlumni`, `$totalPesertaMasaTenggang` **dipindah** ke chip [B] dan tab Ringkasan; jangan dibuang datanya.

**Selesai bila**: bagian atas halaman sesuai wireframe, semua teks di 0.6 yang berasal dari area ini masih muncul, tes hijau.

---

### Tugas 6 🟡 — Tab Ringkasan
Partial `_tab-ringkasan.blade.php` (membungkus `<div id="tab-ringkasan" class="tab-content-panel active" role="tabpanel" ...>`; **id panel dan id tombol tidak boleh berubah**).

- [E] `Pengguna terbaru`: `<x-sa.panel title="Pengguna terbaru" subtitle="Pendaftaran akun dan staf internal terkini" :href="route('superadmin.staf.index')" link-label="Kelola pengguna">`. Isi `@forelse($penggunaTerbaru as $u)` → avatar, nama, email, chip peran. `@empty` → `<x-sa.empty-state>`. Salin logika pewarnaan chip peran dari markup lama; ganti warnanya ke `<x-sa.badge>`.
- [F] `Audit log terbaru`: `<x-sa.panel title="Audit log terbaru" :href="route('superadmin.audit-log.index')" link-label="Lihat semua">`, isi `@forelse($auditTerbaru as $log)` → ikon status, `$log->aksi`, `$log->user->nama ?? 'Sistem'`, jam `$log->waktu->format('H:i')`.
- [G] `KPI Dinas`: 4 kartu mini dari `$anomaliKuotaCount`, `$rataRataSlaVerifikasiHari`, `$kepatuhanNilaiSelesaiPersen`, `$securityPosture['total_audit_events']`. Salin kata-kata target dari markup lama (baris 346–425).
- [H] `Status landing page`: `$statusPublik`, `$isOverrideStatus`, jadwal `tgl_buka` – `tgl_tutup` (blok lama baris 120–160), dibungkus `@if($bulanAktif)`.
- Demografi institusi (`$jenjangRaw`, `$topInstitusi`): `x-sa.panel` dengan bar horizontal `sa-progress`, tinggi bar 8px.

**Selesai bila**: tab terlihat seperti wireframe; tidak ada error saat data kosong (uji dengan DB tanpa pengguna/audit).

---

### Tugas 7 🟢 — Tab Seleksi & Kuota, Pelaksanaan & Bimbingan
Pindahkan isi lama ke `_tab-seleksi.blade.php` dan `_tab-bimbingan.blade.php`. **Isi data dan logika sama**, hanya markup:
1. Ganti setiap blok kartu `style="background: var(--neutral-0); border: 1px solid ..."` dengan `<x-sa.panel>`.
2. Ganti `<table style=...>` dengan `<table class="sa-table">`; bungkus dengan `<div class="sa-table-wrap">` (overflow-x auto) agar tidak merusak mobile.
3. Ganti badge warna-warni dengan `<x-sa.badge tone=...>`.
4. Progress kuota: `<div class="sa-progress"><span style="width: {{ $persen }}%"></span></div>`. Ini **satu-satunya** `style=` yang diperbolehkan (lebar dinamis), dan hanya properti `width`.
5. Pertahankan id panel `tab-seleksi` dan `tab-bimbingan`.

Pastikan teks `Jalur Umum`/`Jalur Khusus` tetap ada bila sebelumnya ada di tab ini.

**Selesai bila**: `grep -c 'style="' ` pada kedua partial ≤ jumlah bar progress.

---

### Tugas 8 🟢 — Tab Sistem & Laporan
Pindahkan ke `_tab-sistem.blade.php` dengan cara yang sama seperti Tugas 7. Bagian: Hub ekspor 4 laporan, Pusat monitoring keamanan (4 kartu + radar insiden + sebaran audit), Pintasan tata kelola, Kesehatan sistem.
- Segmented bar sebaran audit: tiap segmen `style="width: X%"` saja.
- Tombol ekspor tetap memakai route yang sama (jangan ubah `href`/`route()`).
- Empat kartu postur keamanan memakai `<x-sa.stat-card>` bila bentuknya cocok; bila tidak cocok, pakai `x-sa.panel`.

**Selesai bila**: semua link ekspor dan pintasan masih mengarah ke route yang sama (bandingkan daftar `route(` lama vs baru: `grep -o "route('[^']*'" file | sort -u`).

---

### Tugas 9 🟡 — Responsif, aksesibilitas, state kosong
Checklist (centang satu per satu):
- [ ] 1440px, 1024px, 768px, 390px: tidak ada scroll horizontal pada halaman (tabel boleh scroll di dalam pembungkusnya)
- [ ] Grid KPI: 4 → 2 → 1 kolom
- [ ] Tab bar bisa digeser di mobile, tab aktif terlihat
- [ ] Semua tombol/link dapat difokus keyboard dan fokusnya terlihat
- [ ] Setiap ikon dekoratif memakai `aria-hidden="true"`; ikon yang membawa arti punya teks di sebelahnya
- [ ] Status tidak hanya warna: selalu ada teks
- [ ] Kontras teks kecil (12px) memakai `--neutral-600` atau lebih gelap, bukan `--neutral-500`
- [ ] Setiap daftar/tabel punya `@empty` / empty state
- [ ] Skenario kosong: DB tanpa bulan aktif (`$bulanAktif = null`), tanpa audit, tanpa pembimbing: halaman tetap 200

---

### Tugas 10 🟡 — Tes baru, pembersihan, PR
1. Buat `tests/Feature/SuperAdminDashboardRedesignTest.php` (ikuti gaya `SuperAdminFigmaDesignAlignmentTest`):
   - `test_dashboard_renders_four_kpi_cards_and_tabs` → `assertSee` 4 label KPI dan 4 nama tab
   - `test_perlu_tindakan_hanya_menampilkan_item_dengan_jumlah_lebih_dari_nol`
   - `test_dashboard_tanpa_bulan_aktif_tidak_error` → status 200
   - `test_dashboard_tidak_memakai_script_inline` → `assertDontSee('onclick=', false)` dan `assertSee('js/superadmin-dashboard.js', false)`
   - `test_non_superadmin_tetap_403`
2. Pembersihan: hapus blok `<style>`/`<script>` lama dari view, hapus komentar `Figma Node` yang usang, pastikan `dashboard.blade.php` ≤ **120 baris** (hanya `@extends`, section, dan `@include`).
3. `./vendor/bin/pint` → `php artisan test` (seluruh suite).
4. Tulis PR: ringkasan, screenshot sebelum/sesudah, hasil tes, daftar hal yang sengaja **tidak** diubah.

---

## 3. Definition of Done (seluruh paket)
- [ ] Seluruh tes lama hijau; tes baru hijau; Pint bersih
- [ ] `dashboard.blade.php` ≤ 120 baris; `grep -c 'style="'` pada folder `superadmin/dashboard/` hanya untuk lebar dinamis
- [ ] Tidak ada hex warna baru; tidak ada `onclick`/`<script>` inline pada dashboard
- [ ] Semua teks di 0.6 tetap ada
- [ ] Screenshot sebelum/sesudah terlampir (4 tab × 2 ukuran)
- [ ] Tidak ada perubahan pada `layouts/`, route, model, atau query

## 4. Kesalahan yang sering terjadi
| Jangan | Karena |
|---|---|
| Mengubah nama variabel view (`$pesertaAktif`, dst.) | Tes dan partial lain bergantung padanya |
| Menambah Tailwind/Vite/Alpine | Proyek tidak memuatnya di view; build tidak ada |
| Menulis hex warna sendiri | Menyimpang dari Design System |
| Menaruh `<script>` inline atau `onclick` | Diblokir CSP produksi |
| Menghapus/melonggarkan tes yang merah | Berarti ada yang rusak; tanyakan |
| Menampilkan data slot khusus ke area publik | Melanggar BR-02. Dashboard ini khusus SuperAdmin, tapi jangan menyalin markup-nya ke halaman publik |
| Mengubah teks "Total pengguna", dll. | Dicek tes |

## 5. Temuan di luar lingkup (untuk senior, jangan dikerjakan sekarang)
1. **CSP produksi vs layout**: `layouts/dashboard.blade.php` memuat Google Fonts dari CDN dan `<script>` inline (sidebar `toggleNavGroup`, `toggleSidebar`). Dengan CSP produksi (`style-src 'self' 'unsafe-inline'`, `script-src 'self'`) font eksternal dan script inline akan diblokir. Perlu self-host font dan pindah JS layout ke file. Ada tes yang memeriksa string fungsi tersebut, jadi ubah bersama tesnya.
2. ±1.000 baris CSS inline di layout sebaiknya dipindah ke berkas CSS.
3. Fase berikutnya: terapkan komponen `x-sa.*` ke halaman superadmin lain (staf, audit-log, role, pengaturan, dll.).
4. `DashboardController` menghitung data semua tab pada setiap request; pertimbangkan memuat tab berat (keamanan, audit) secara lazy.

## 6. Prompt siap pakai untuk AI model murah
Tempel ini + isi satu bagian "Tugas N" dari dokumen ini:

> Kamu junior developer Laravel 10 + Blade. Kerjakan **hanya** Tugas N dari dokumen plan terlampir. Patuhi bagian 0.3 (yang dilarang), 0.5 (token), dan 0.6 (teks wajib). Jangan mengubah file di luar daftar tugas. Jangan menambah dependensi. Setelah selesai, tampilkan: (1) daftar file yang diubah, (2) hasil `php artisan test --filter=SuperAdmin`, (3) hal yang kamu ragukan. Jika ada instruksi yang bertentangan atau tidak jelas, berhenti dan tanyakan, jangan menebak.

---

## 7. Catatan Hasil Implementasi Percobaan (branch `feat/sa-dashboard-redesign`)

**Selesai**: Tugas 1–6, 9 (sebagian), 10.
- `DashboardController` dipecah per domain; `$perluTindakan` ditambahkan; nama variabel view tidak berubah.
- Komponen `x-sa.*` (panel, stat-card, badge, avatar, empty-state, icon, action-chip), `_styles`, dan `public/js/superadmin-dashboard.js` (tab tanpa inline script, ada navigasi panah keyboard).
- `dashboard.blade.php` turun dari 1.689 baris menjadi 31 baris; header, perlu tindakan, KPI, dan tab Ringkasan memakai komponen dan token tanpa `style=` inline.
- Tes baru `SuperAdminDashboardRedesignTest` (6 tes). Hasil seluruh suite sama dengan baseline: 7 tes gagal lama (semuanya sisi Admin), 0 tambahan.

**Belum selesai (lanjutan Tugas 7 dan 8)**: partial `_tab-seleksi`, `_tab-bimbingan`, `_tab-sistem` baru dipindahkan apa adanya (masih ±370 `style=` inline). Isi dan perilakunya sama dengan sebelumnya; konversi ke `x-sa.*` tinggal dikerjakan sesuai Tugas 7–8.

**Koreksi terhadap plan di atas** (ditemukan saat implementasi):
1. **Ada file tes yang terlewat di bagian 0.6**: `tests/Feature/SuperAdminUnifiedDashboardFeaturesTest.php` juga mengecek teks dashboard, antara lain `+ Akun Staf`, `+ Periode Magang`, `Tinjau Berkas`, `Penempatan Peserta`, `Ekspor Laporan`, `Status Publik:`, `Staf Pending Sandi Awal`, `Masa Tenggang`, `Pusat Metrik Keberhasilan Produk & KPI Dinas` (dicek tanpa escape, jadi judulnya dikirim sebagai `HtmlString`), serta semua judul panel di tab Seleksi, Bimbingan, dan Sistem. Karena itu baris "Quick actions dihapus" di bagian 1.3 **tidak berlaku**: pintasan dipertahankan sebagai baris `Pintasan Tindakan`. Hapus atau ubah teks-teks ini hanya bersama tesnya.
2. Header tidak lagi mengulang `h2` dan deskripsi, karena `layouts/dashboard` sudah menampilkannya dari `@section('page_title')` dan `@section('page_description')`.
3. Model `BulanMagang` **tidak punya atribut `nama_bulan`** (kode lama menampilkan kosong). Gunakan `bulan_mulai?->translatedFormat('F Y')`, seperti halaman admin lain.
4. **Jangan menjalankan `pint` pada folder `app` secara keseluruhan**: banyak file lama belum sesuai Pint dan akan ikut berubah. Jalankan hanya pada file yang kamu ubah (`./vendor/bin/pint path/ke/file.php`).
5. Tes `test_dashboard_tidak_memakai_script...` memeriksa file dashboard, bukan HTML penuh, karena layout masih punya handler inline sendiri (lihat bagian 5, butir 1).
