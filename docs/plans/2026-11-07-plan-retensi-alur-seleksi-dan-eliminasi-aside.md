# Rencana Kerja: Retensi Alur Pendaftaran di Fase Seleksi dan Eliminasi Widget Aside Sebelum Aktif Magang

## 1. Analisis Masalah & Pertanyaan Pengguna
Berdasarkan keluhan dan screenshot pengguna (`media_1794041578251_18cf48c5.png`):

### Masalah A: Mengapa Alur Pendaftaran Hilang di Tahap Pengajuan?
- **Penyebab**:
  - Di `DashboardController.php`, ketika peserta membuat pengajuan pendaftaran (status pendaftaran ada), `$fase` berubah dari `'onboarding'` menjadi `'seleksi'`.
  - Di `dashboard.blade.php`, struktur sebelumnya adalah percabangan eksklusif:
    ```blade
    @if($fase === 'onboarding')
        @include('peserta.dashboard.partials._phase_onboarding')
    @elseif($fase === 'seleksi')
        @include('peserta.dashboard.partials._phase_seleksi')
    ```
  - Akibatnya, begitu peserta memilih kuota dan membuat draf pengajuan, kartu **Panduan Alur Pendaftaran Magang** langsung hilang dan digantikan oleh kartu ringkasan seleksi.
  - Peserta kehilangan panduan visual mengenai tahapan yang sedang dijalaninya (Langkah 1 selesai, Langkah 2 selesai, Langkah 3 sedang berlangsung/diajukan).

### Masalah B: Mengapa Widget Aside (Kalender, 1 Logbook, 72 Hari Tersisa) Tampil di Tahap Pengajuan?
- **Penyebab**:
  - Di `dashboard.blade.php`:
    ```blade
    @if($fase !== 'onboarding')
        <aside class="saas-cockpit-aside" id="dashboardAsideContext">
            @include('peserta.dashboard.partials._aside')
        </aside>
    @endif
    ```
  - Kondisi `@if($fase !== 'onboarding')` menyebabkan widget samping langsung dirender pada fase `'seleksi'`, padahal peserta **belum diterima dan belum aktif magang**.
  - Widget tersebut berisi hal-hal yang belum relevan untuk calon peserta:
    1. *Widget Kalender penanggalan logbook & tugas*.
    2. *Widget kartu merah "Perlu Perhatian: 1 logbook belum dikirim"*.
    3. *Widget kartu kuning "72 hari tersisa - MINGGU KE-3 DARI 13"*.
    4. *Widget "Pembimbing Lapangan: Belum Ditugaskan"*.
- **Solusi Pengguna**: Menghilangkan seluruh kolom aside tersebut dari tampilan tahap seleksi/pengajuan, sehingga kolom samping hanya muncul saat peserta sudah resmi diterima dan menjalani magang aktif (`$fase === 'aktif'`).

---

## 2. Rencana Implementasi Bertahap

### Tahap 1: Mempertahankan Alur Stepper pada Fase Seleksi
- Di file `resources/views/peserta/dashboard.blade.php`:
  - Pada bagian `@elseif($fase === 'seleksi')`, tampilkan kartu alur pendaftaran (`_phase_onboarding`) terlebih dahulu, diikuti dengan kartu status pendaftaran (`_phase_seleksi`):
    ```blade
    @elseif($fase === 'seleksi')
        @include('peserta.dashboard.partials._phase_onboarding')
        @include('peserta.dashboard.partials._phase_seleksi')
    ```
- Di file `resources/views/peserta/dashboard/partials/_phase_onboarding.blade.php`:
  - Perbarui status Langkah 3:
    - Jika pendaftaran masih berstatus draf (`berkas_belum_lengkap`): Langkah 3 bertanda `active` dengan tombol status *"Unggah Berkas"*.
    - Jika pendaftaran sudah diajukan (`diajukan`, `waiting_list`, `diterima`): Langkah 3 bertanda centang hijau `completed` dengan badge *"✓ Diajukan"*.

### Tahap 2: Menghilangkan Widget Aside pada Fase Seleksi & Onboarding
- Di file `resources/views/peserta/dashboard.blade.php`:
  - Ubah kondisi render aside:
    ```blade
    {{-- Kolom Samping (Konteks & Berkas) - HANYA Ditampilkan saat Peserta Aktif Magang --}}
    @if($fase === 'aktif' && $penempatan)
        <aside class="saas-cockpit-aside" id="dashboardAsideContext">
            @include('peserta.dashboard.partials._aside')
        </aside>
    @endif
    ```
  - Sesuaikan grid cockpit:
    ```blade
    <div class="saas-cockpit-grid {{ $fase !== 'aktif' ? 'is-onboarding' : '' }}">
    ```
  - Dengan kelas `is-onboarding` aktif saat `$fase !== 'aktif'`, layout otomatis menjadi 1 kolom penuh (*full width*), sehingga kartu alur pendaftaran dan kartu status pendaftaran membentang luas, lega, dan elegan.
- Di file `resources/views/peserta/dashboard/partials/_aside.blade.php`:
  - Sesuaikan pembungkus utama menjadi `@if(($fase ?? '') === 'aktif' && !empty($penempatan))`.

---

## 3. Rencana Pengujian & Quality Gates

1. **Pengujian Fungsional Otomatis (PHPUnit - PHP 8.3 Laragon)**:
   - `tests/Feature/PesertaFullPortalFeatureTest.php`: Memastikan seluruh 11 pengujian portal peserta tetap hijau.
   - `tests/Feature/GentelellaDashboardRevampTest.php`: Memastikan kompatibilitas tampilan dashboard x-panel.
   - `tests/Feature/PesertaPendaftaranFlowTest.php`: Memverifikasi alur pendaftaran end-to-end.
2. **Quality Gate Tooling**:
   - `php artisan view:clear`
   - `./vendor/bin/pint --test`
3. **Verifikasi Visual di Browser**:
   - Membuka Dashboard Peserta dengan akun yang sedang dalam tahap pengajuan pendaftaran:
     - Kartu Panduan Alur Pendaftaran Magang tetap tampil dengan Langkah 1 dan 2 bercentang hijau, dan Langkah 3 aktif/diajukan.
     - Di bawahnya tampil kartu Status Pendaftaran Magang Aktif.
     - Widget kalender, logbook merah, countdown kuning, dan pembimbing samping tidak lagi muncul.

