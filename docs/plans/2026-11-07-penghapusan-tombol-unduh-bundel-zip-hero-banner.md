# Rencana Kerja: Penghapusan Tombol Unduh Bundel ZIP pada Hero Banner Verifikasi

**Tanggal:** 7 November 2026  
**Target File:** `resources/views/admin/pendaftaran/index.blade.php`  
**Tujuan:** Menghilangkan tombol aksi `Unduh Bundel ZIP` dari sisi kanan Hero Banner Verifikasi Berkas Pendaftaran Peserta sesuai permintaan pengguna, sehingga Hero Banner tampil murni sebagai panel identitas visual yang bersih (*clean informational banner*).

---

## 1. Analisis Kebutuhan

### Masalah / Kebutuhan Pengguna
- Pada sisi kanan Hero Banner `Daftar Pendaftaran Peserta Magang`, saat ini masih terdapat satu tombol:
  `[ ⬇ Unduh Bundel ZIP ]` (`.adm-hero-btn-primary`) di dalam kontainer `.adm-hero-actions`.
- Pengguna meminta secara spesifik melalui screenshot untuk menghapus dan menghilangkan tombol ini sepenuhnya.
- Dengan penghapusan ini, area Hero Banner akan tampil 100% bersih, fokus pada judul dan identitas instansi:
  - **Judul Utama**: `Daftar Pendaftaran Peserta Magang`
  - **Subtitle**: `Verifikasi Berkas Pendaftaran Peserta • Dinas Komunikasi dan Informatika Kabupaten Sleman`
  - **Sisi Kanan**: Bersih tanpa tombol mengambang.

### Analisis Dampak Teknis
1. **Backend / Routing**:
   - Route `admin.pendaftaran.download-zip` (`AdminPendaftaranController@downloadBundelZip`) tetap aman dan tidak dihapus, sehingga fungsionalitas unduhan ZIP di backend tetap utuh.
2. **Testing / Regresi**:
   - Berdasarkan penelusuran test suite, tidak ada test visual atau feature yang mewajibkan keberadaan string `"Unduh Bundel ZIP"` pada view `admin/pendaftaran/index.blade.php`.
   - Feature test `AdvancedRoleEnhancementPhaseSixTest::test_admin_download_bundel_zip_registration_documents` memanggil endpoint route secara langsung (`route('admin.pendaftaran.download-zip')`), sehingga seluruh test dijamin tetap **PASS**.
3. **Penyelarasan Layout**:
   - Struktur `.adm-hero-content` tetap responsif dan rapi saat hanya berisi `.adm-hero-text`.

---

## 2. Rencana Perubahan Kode

### Target: `resources/views/admin/pendaftaran/index.blade.php`

**Sebelum:**
```blade
    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Daftar Pendaftaran Peserta Magang</h1>
                <div class="adm-hero-subtitle">
                    <span>Verifikasi Berkas Pendaftaran Peserta</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
            </div>
            <div class="adm-hero-actions">
                <a href="{{ route('admin.pendaftaran.download-zip', request()->query()) }}" class="adm-hero-btn-primary">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    <span>Unduh Bundel ZIP</span>
                </a>
            </div>
        </div>
    </div>
```

**Sesudah:**
```blade
    {{-- 0. SLEMAN NAVY HERO BANNER (Khas MagKom) --}}
    <div class="adm-hero-banner">
        <div class="adm-hero-content">
            <div class="adm-hero-text">
                <h1 class="adm-hero-title">Daftar Pendaftaran Peserta Magang</h1>
                <div class="adm-hero-subtitle">
                    <span>Verifikasi Berkas Pendaftaran Peserta</span>
                    <span class="sep">&bull;</span>
                    <span>Dinas Komunikasi dan Informatika Kabupaten Sleman</span>
                </div>
            </div>
        </div>
    </div>
```

---

## 3. Rencana Verifikasi & Quality Gates

1. **Linting Code Style**:
   ```bash
   vendor\bin\pint.bat --test
   ```
2. **PHPUnit Feature Tests**:
   ```bash
   d:\laragon\bin\php\php-8.3.33-Win32-vs16-x64\php.exe artisan test --filter "AdminVerifikasi|AdminChildPages|DashboardModulesRenderingTest"
   ```
3. **Frontend Asset Build**:
   ```bash
   npm run build
   ```
4. **Dev Server Check**:
   ```bash
   curl.exe -I -s http://127.0.0.1:8000
   ```

