# Rencana Kerja: Revamp Kartu Jadwal Pendaftaran & Standardisasi Ikon Dinas pada Panel Aside Dashboard Admin

**Tanggal Dokumen**: 2026-11-04  
**Modul**: Administrasi MagKom — Dashboard Operasional Administrasi (`admin/dashboard` / Aside Column)  
**Status**: ✅ Selesai Diimplementasikan & Terverifikasi (Siap Review)  

---

## 1. Ringkasan Kebutuhan & Analisis Visual

### 1.1 Masalah Eksisting Berdasarkan Tangkapan Layar Pengguna (`media_1793761817100_21d37961.png`)
1. **Kartu Jadwal Pendaftaran (Panel 3 di Aside)**:
   - **Badge Status Pecah/Tergulung**: Teks `● Ditutup` terpecah menjadi 2 baris (titik `•` di baris atas, kata `DITUTUP` di baris bawah) sehingga badge berbentuk lingkaran/oval lonjong anomali yang tampak berantakan.
   - **Tata Letak Padat & Kaku**: Seluruh informasi (Bulan, Batas Akhir, Keterangan, dan Status) dijejalkan ke dalam 1 baris sempit horizontal tanpa hirarki visual yang jelas.
   - **Tombol "Atur →"**: Berupa link teks polos biasa tanpa batas visual (*uncontained link*).
   - **Ikon Header**: Ikon kalender hanya berupa SVG garis tipis biasa tanpa wadah lencana resmi dinas.

2. **Ikon "Tanda !" yang Terlihat Murahan / "AI Banget"**:
   - Pada Panel 1 di file aside yang sama (`_aside.blade.php` baris 11), ikon judul "Perlu Tindakan Hari Ini" menggunakan karakter teks monospace mentah:
     `<span ...>!</span>`
   - Pada Panel 5 ("Offboarding Watchdog", baris 215), digunakan emoji mentah:
     `<span ...>⏳</span>`
   - Kedua elemen ini terlihat seperti placeholder kasar bawaan AI (*AI-generated placeholder*) yang tidak pantas dan tidak layak untuk standar website kantor dinas pemerintah kabupaten.

### 1.2 Tujuan Solusi Desain
1. **Tata Letak Elegan & Rapih untuk Jadwal Pendaftaran**:
   - Membagi isi kartu menjadi 2 zona terstruktur:
     - **Zona Header**: Lencana ikon kalender resmi dinas, judul tebal `Jadwal Pendaftaran`, dan micro-button tombol `Atur →` yang berpenampilan profesional.
     - **Zona Periode & Status**: Nama bulan dan status pendaftaran (`Pendaftaran Dibuka` / `Pendaftaran Ditutup`) dengan *status pill* satu baris utuh (`white-space: nowrap`), dot status, dan warna semantik yang kontras.
     - **Zona Deadline & Keterangan**: Tanggal batas akhir pendaftaran yang ditonjolkan dengan tanggal kalender jelas serta catatan pembantu yang informatif (*helper note*).
2. **Standardisasi Ikon Kantor Dinas Resmi**:
   - Mengganti teks mentah `!` pada Panel 1 dengan lencana resmi SVG `<x-sa.icon name="bell" :size="15" />` atau `<x-sa.icon name="alert-circle" :size="15" />` (simbol agenda/peringatan kedinasan).
   - Mengganti emoji `⏳` pada Panel 5 dengan lencana resmi SVG `<x-sa.icon name="clock" :size="15" />` (simbol pengawasan waktu dinas).
   - Memastikan seluruh 5 panel aside menggunakan wadah lencana resmi seragam (`.adm-aside-icon-badge`) dengan warna tematik (Navy, Amber, Emerald, Blue).
3. **Penyelarasan Garis Atas (*Top Accent Border*)**:
   - Menyematkan garis aksen atas 3.5px konsisten pada setiap kartu aside (selaras dengan standar Gentelella di seluruh portal).

---

## 2. Evaluasi Pendekatan Teknis

| Parameter | Opsi A: Restrukturisasi Terpadu Aside & Standardisasi Ikon Dinas (Direkomendasikan) | Opsi B: Hanya Menambal CSS `white-space: nowrap` pada Badge |
| :--- | :--- | :--- |
| **Deskripsi** | Merombak tata letak kartu Jadwal Pendaftaran menjadi layout kartu dinas yang rapi dan mengganti seluruh placeholder mentah (`!` dan `⏳`) dengan ikon SVG kedinasan resmi. | Hanya menambahkan `white-space: nowrap` agar tulisan DITUTUP tidak turun, membiarkan ikon `!` dan emoji `⏳` tetap ada. |
| **Penyelesaian Masalah** | **Tuntas 100%**: Menyelesaikan masalah layout rusak, hirarki teks yang berantakan, sekaligus menghilangkan ikon murahan yang dikeluhkan pengguna. | **Parsial**: Ikon `!` yang dikritik "terlihat AI bgt dan jelek" tetap ada dan tampilan kartu tetap kaku. |
| **Kesan Visual Dinas** | **Tinggi (Resmi Pemerintah)**: Menggunakan lencana resmi, tipografi berjenjang, dan palet warna instansi. | **Rendah**: Masih terlihat seperti template cepat saji. |
| **Kepatuhan AGENTS.md** | **Patuh Penuh**: Markup Blade semantik bersih, styling terpusat di `resources/css/pages/admin/dashboard.css`. | Hanya CSS patch ad-hoc. |

> **Rekomendasi**: Mengadopsi **Opsi A** untuk standardisasi visual penuh.

---

## 3. Rencana Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `resources/views/admin/dashboard/partials/_aside.blade.php` | **Modify** | 1. **Panel 1**: Ganti `<span ...>!</span>` dengan badge lencana SVG `<x-sa.icon name="alert-circle" :size="15" />`.<br>2. **Panel 3 (Jadwal Pendaftaran)**: Revamp layout kartu: header ber-lencana kalender dinas, micro-button `Atur`, pill status `Dibuka`/`Ditutup` anti-wrap, dan deadline highlight box.<br>3. **Panel 5 (Watchdog)**: Ganti emoji `⏳` dengan badge lencana SVG `<x-sa.icon name="clock" :size="15" />`.<br>4. Tambahkan top border tematik pada tiap kartu. |
| `resources/css/pages/admin/dashboard.css` | **Modify** | Menambahkan definisi styling class semantik: `.adm-aside-icon-badge`, `.adm-schedule-box`, `.adm-schedule-pill`, `.adm-btn-micro`, dan dukungan Dark Mode. |
| `public/css/dashboard-cockpit-unified.css` | **Modify** | Sinkronisasi aturan CSS ke stylesheet fallback runtime. |

---

## 4. Rincian Desain & Kode

### A. Rincian Perubahan Blade pada `_aside.blade.php`

#### 1. Panel 1 (Perlu Tindakan Hari Ini):
```blade
<div style="display: flex; align-items: center; gap: 0.5rem;">
    <span class="adm-aside-icon-badge warning" aria-hidden="true">
        <x-sa.icon name="alert-circle" :size="15" />
    </span>
    <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Perlu Tindakan Hari Ini</h3>
</div>
```

#### 2. Panel 3 (Jadwal Pendaftaran):
```blade
<x-gentelella.card style="padding: 1.25rem; border-top: 3.5px solid var(--blue-600, #0641CC);">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.85rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.65rem;">
        <div style="display: flex; align-items: center; gap: 0.5rem;">
            <span class="adm-aside-icon-badge primary" aria-hidden="true">
                <x-sa.icon name="calendar" :size="15" />
            </span>
            <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Jadwal Pendaftaran</h3>
        </div>
        @if($bulanAktif)
            <a href="{{ route('admin.kuota.index', ['bulan_magang_id' => $bulanAktif->id]) }}" class="adm-btn-micro">
                <span>Atur</span> &rarr;
            </a>
        @endif
    </div>

    @if($bulanAktif)
        @php
            $tglTutup = $bulanAktif->tgl_tutup ? \Carbon\Carbon::parse($bulanAktif->tgl_tutup) : null;
            $tglBuka = $bulanAktif->tgl_buka ? \Carbon\Carbon::parse($bulanAktif->tgl_buka) : null;
            $isBuka = $tglBuka && $tglTutup && now()->between($tglBuka, $tglTutup);
            $sisaHari = $tglTutup && $tglTutup->isFuture() ? (int) now()->diffInDays($tglTutup, false) : 0;
        @endphp
        <div class="adm-schedule-card">
            {{-- Baris Periode & Status Pill --}}
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.65rem; gap: 0.5rem;">
                <span class="adm-schedule-period">
                    {{ $bulanAktif->bulan_mulai?->translatedFormat('F Y') }}
                </span>
                <span class="adm-schedule-pill {{ $isBuka ? 'is-open' : 'is-closed' }}">
                    <span class="dot-status"></span>
                    <span>{{ $isBuka ? 'Dibuka' : 'Ditutup' }}</span>
                </span>
            </div>

            {{-- Baris Batas Akhir Tanggal --}}
            <div class="adm-schedule-deadline-row">
                <div style="display: flex; align-items: center; gap: 0.4rem; color: var(--neutral-700);">
                    <x-sa.icon name="clock" :size="14" />
                    <span style="font-size: 0.8125rem; font-weight: 700;">
                        Batas: {{ $tglTutup ? $tglTutup->translatedFormat('d M Y') : '-' }}
                    </span>
                </div>
            </div>

            {{-- Helper Note Status --}}
            <div class="adm-schedule-note {{ $isBuka ? 'note-open' : 'note-closed' }}">
                @if($isBuka && $sisaHari >= 0)
                    <span>Tersisa <strong>{{ $sisaHari }} hari kalender</strong> masa penerimaan berkas.</span>
                @else
                    <span>Pendaftaran tidak sedang berlangsung saat ini.</span>
                @endif
            </div>
        </div>
    @else
        <p style="font-size: 0.8125rem; color: var(--neutral-500); margin: 0; text-align: center; padding: 1rem 0;">Belum ada bulan magang aktif.</p>
    @endif
</x-gentelella.card>
```

#### 3. Panel 5 (Offboarding Watchdog):
```blade
<div style="display: flex; align-items: center; gap: 0.5rem;">
    <span class="adm-aside-icon-badge warning" aria-hidden="true">
        <x-sa.icon name="clock" :size="15" />
    </span>
    <h3 style="font-size: 0.95rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Offboarding Watchdog</h3>
</div>
```

---

## 5. Permohonan Izin Perubahan Markup Blade (Sesuai AGENTS.md Bagian 3.3)

> [!IMPORTANT]
> **Alasan Perubahan Blade**:  
> Perubahan ini merestrukturisasi layout kartu sempit yang menyebabkan badge status pecah dua baris, serta mengganti karakter teks mentah (`!` dan `⏳`) menjadi elemen ikon SVG resmi kedinasan `<x-sa.icon>`.
> - **File yang diubah**: `resources/views/admin/dashboard/partials/_aside.blade.php`.
> - **Scope Perubahan**: Bagian header panel 1, 3, 5 dan container kartu jadwal pendaftaran.
> - Tidak ada logika bisnis backend, controller, atau route yang diubah.

---

## 6. Langkah Implementasi (Checklist)

- [x] **Langkah 1**: Tambahkan aturan CSS untuk `.adm-aside-icon-badge`, `.adm-schedule-card`, `.adm-schedule-pill`, `.adm-schedule-deadline-row`, `.adm-schedule-note`, `.adm-btn-micro`, dan varian Dark Mode di `resources/css/pages/admin/dashboard.css`.
- [x] **Langkah 2**: Sinkronkan aturan CSS ke `public/css/dashboard-cockpit-unified.css`.
- [x] **Langkah 3**: Perbarui markup di `resources/views/admin/dashboard/partials/_aside.blade.php` sesuai rincian desain dinas.
- [x] **Langkah 4**: Jalankan compile Vite via `npm run build`.
- [x] **Langkah 5**: Jalankan automated feature tests: `php artisan test tests/Feature/AdminDashboardEnhancementTest.php tests/Feature/AdminTerminologyDesignSystemTest.php tests/Feature/AdvancedThreePillarsDashboardTest.php`.
- [x] **Langkah 6**: Jalankan code formatter: `./vendor/bin/pint --test`.

---

## 7. Strategi Testing

1. **Automated Feature Tests**:
   - `AdminDashboardEnhancementTest`: Memverifikasi dashboard admin tetap memuat seluruh data penting dan komponen operasional.
   - `AdminTerminologyDesignSystemTest`: Memastikan teks judul `Jadwal Pendaftaran` tetap ada dan tidak berubah menjadi terminologi lama.
   - `AdvancedThreePillarsDashboardTest`: Memastikan assertion `Offboarding Watchdog` tetap lulus.
2. **Quality Gate**:
   - `npm run build`: Pastikan asset bundling Vite 100% sukses.
   - `pint --test`: Pastikan formatting kode PHP 100% rapi.

---

## 8. Kriteria Selesai (Definition of Done)

- [x] Ikon teks mentah `!` pada Panel 1 telah diganti dengan lencana ikon kedinasan resmi (`alert-circle`).
- [x] Emoji mentah `⏳` pada Panel 5 telah diganti dengan lencana ikon kedinasan resmi (`clock`).
- [x] Kartu Jadwal Pendaftaran tertata lapang, rapi, dan tidak ada teks/badge status yang terpotong atau wrap anomali.
- [x] Badge status (`Dibuka` / `Ditutup`) tampil utuh satu baris dengan visual dot status yang elegan.
- [x] Seluruh pengujian otomatis lulus 100% hijau (*PASS*).

