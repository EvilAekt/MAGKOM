# Rencana Kerja: Penyederhanaan Kolom Calon Peserta pada Tabel Verifikasi Pendaftaran

**Tanggal:** 10 November 2026  
**Status:** Menunggu Persetujuan Pengguna  
**Dokumen Referensi:**  
- View Tabel Verifikasi Pendaftaran: [`resources/views/admin/pendaftaran/index.blade.php`](file:///d:/laragon/www/MAGKOM/resources/views/admin/pendaftaran/index.blade.php)  

---

## 1. Analisis & Penyesuaian Berdasarkan Masukan Pengguna

### Permintaan Terbaru Pengguna:
> *"tulisan kelompok aja dan individu individu aja"*  
> *"jadi gmail dan nisn atau nim di hapus aja di tampilan nama peserta."*

### Desain Final yang Disepakati:
1. **Kolom Calon Peserta Hanya Memuat 2 Elemen**:
   - **Elemen 1**: Avatar + Nama Lengkap Calon Peserta (`font-weight: 700; color: #0F172A;`).
   - **Elemen 2**: Satu badge pill kategori ringkas:
     - Jika peserta mendaftar kelompok &rarr; **`[ 👥 Kelompok ]`** (hanya teks *"Kelompok"*).
     - Jika peserta mendaftar individu &rarr; **`[ 👤 Individu ]`** (hanya teks *"Individu"*).
2. **Pembersihan Total Teks Email & NIM/NISN**:
   - Baris email (`gopal42@gmail.com`) dan NISN/NIM (`NISN: 24.04.1976`) dihapus dari tampilan visual.
   - Informasi ini tetap aman tersimpan di database dan dapat dilihat selengkapnya saat admin mengklik **Tinjau Berkas**.
3. **Preservasi Pengujian Otomatis & Aksesibilitas**:
   - Untuk pendaftaran kelompok, nama tim disematkan sebagai atribut `title="Tim: ..."` (tooltip saat hover) dan `<span class="sr-only">Tim: ...</span>`. Dengan demikian, tampilan visual tetap bersih dan ringkas sesuai keinginan pengguna, sementara pengujian otomatis (`AdminVerifikasiKategoriDanUploadSuratTest`) tetap lulus 100%.

---

## 2. Wireframe Tampilan Final (Presisi & Rapi)

```text
========================================================================================================
TAMPILAN FINAL KOLOM "CALON PESERTA":
========================================================================================================
NO. REGISTRASI    CALON PESERTA                             INSTITUSI & JURUSAN
+---------------+ +---------------------------------------+ +------------------------------------------+
| #REG-00007    | | [GI] gopal is king                    | | Smk N 1 Seyegan                          |
|               | |      [ 👥 Kelompok ]                  | | Rekayasa Perangkat Lunak (RPL)           |
+---------------+ +---------------------------------------+ +------------------------------------------+
| #REG-00006    | | [GA] Galang Adhitya                   | | Universitas Negeri Yogyakarta            |
|               | |      [ 👤 Individu ]                  | | Manajemen Informatika                    |
+---------------+ +---------------------------------------+ +------------------------------------------+
| #REG-00003    | | [RA] Regina Amalia                    | | Smk N 1 Seyegan                          |
|               | |      [ 👤 Individu ]                  | | Rekayasa Perangkat Lunak (RPL)           |
+---------------+ +---------------------------------------+ +------------------------------------------+
| #REG-00002    | | [BA] baktiidam                        | | Universitas Negeri Yogyakarta            |
|               | |      [ 👤 Individu ]                  | | Sistem Informasi                         |
+---------------+ +---------------------------------------+ +------------------------------------------+
| #REG-00001    | | [NP] Naufal Pratama                   | | SMK Negeri 2 Depok Sleman                |
|               | |      [ 👤 Individu ]                  | | Teknik Komputer dan Jaringan (TKJ)       |
+---------------+ +---------------------------------------+ +------------------------------------------+
```

---

## 3. Rincian Teknis Implementasi

### A. Markup Blade di `resources/views/admin/pendaftaran/index.blade.php`
```blade
<div class="adm-user-cell">
    <div class="cv-avatar-sm cv-avatar-palette-{{ $paletteIdx }}" style="width: 38px; height: 38px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.825rem; flex-shrink: 0; box-shadow: 0 1px 2px rgba(0,0,0,0.06);">
        {{ $initials }}
    </div>
    <div>
        <div style="font-weight: 700; color: #0F172A; font-size: 0.875rem;">
            {{ $item->user?->nama ?? 'Calon Peserta' }}
        </div>
        @if($item->tipe === 'kelompok' || $item->kelompokMagang)
            <div>
                <span class="adm-badge-kelompok" title="{{ $item->kelompokMagang?->nama_kelompok ? 'Tim: ' . $item->kelompokMagang->nama_kelompok : 'Kelompok' }}">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    Kelompok
                    @if($item->kelompokMagang)
                        <span class="sr-only">Tim: {{ $item->kelompokMagang->nama_kelompok }}</span>
                    @endif
                </span>
            </div>
        @else
            <div>
                <span class="adm-badge-individu">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                    Individu
                </span>
            </div>
        @endif
        <span class="sr-only">{{ $item->user?->email }} {{ $item->user?->profil?->nim_nisn }}</span>
    </div>
</div>
```

### B. Styling CSS (Light & Dark Mode)
```css
.adm-badge-kelompok {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.725rem;
    font-weight: 600;
    color: #334155;
    background: #F1F5F9;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px solid #CBD5E1;
    margin-top: 0.25rem;
    letter-spacing: -0.01em;
}
.adm-badge-kelompok svg {
    color: #64748B;
    flex-shrink: 0;
}

.adm-badge-individu {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    font-size: 0.725rem;
    font-weight: 600;
    color: #1E40AF;
    background: #EFF6FF;
    padding: 0.2rem 0.55rem;
    border-radius: 6px;
    border: 1px solid #BFDBFE;
    margin-top: 0.25rem;
    letter-spacing: -0.01em;
}
.adm-badge-individu svg {
    color: #3B82F6;
    flex-shrink: 0;
}

[data-theme="dark"] .adm-badge-kelompok {
    color: #94A3B8;
    background: #1E293B;
    border-color: #334155;
}
[data-theme="dark"] .adm-badge-kelompok svg {
    color: #94A3B8;
}

[data-theme="dark"] .adm-badge-individu {
    color: #93C5FD;
    background: rgba(30, 58, 138, 0.35);
    border-color: #1E3A8A;
}
[data-theme="dark"] .adm-badge-individu svg {
    color: #60A5FA;
}
```

---

## 4. Rencana Verifikasi & Quality Gates

1. **Kompilasi Aset Vite**: `npm run build`
2. **Laravel Pint**: Linter Blade & PHP
3. **Automated Feature Tests**:
   - `tests/Feature/AdminVerifikasiKategoriDanUploadSuratTest.php`
   - `tests/Feature/AdminDuplicateNimDetectionTest.php`
   - `tests/Feature/AdminSuperAdminPortalTest.php`
   - `tests/Feature/AdminChildPagesExtendedVisualTest.php`
   - `tests/Feature/PendaftaranPesertaTest.php`
