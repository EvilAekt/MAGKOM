# [SENIOR DEV TASK] Standardisasi Header Banner ala Halaman Tugas, Pembersihan Tab Filter, Penghapusan Alert Perlu Perhatian & Kolom Status Nilai pada Direktori Peserta

**ID Task**: `TASK-HEADER-FILTER-PESERTA-REVAMP-01`  
**Target Eksekutor**: Junior Full-Stack Developer / AI Model Ringan (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Author / Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation (Plan / PRD)  
**Tanggal**: 06 November 2026  

---

## 1. Background & Analisis Kebutuhan Pengguna

Berdasarkan permintaan pengguna terbaru, terdapat 5 poin penyempurnaan UI/UX yang ditargetkan pada modul Pembimbing:

### 1.1 Kebutuhan 1: Penyelarasan Header Banner Keempat Tampilan Utama Pembimbing
- **Kondisi Eksisting**:  
  Halaman **Tugas & Progres Magang** (`/pembimbing/tugas`) telah memiliki header banner modern melengkung (*rounded 16px/20px*) dengan gradasi Sleman Navy (`.classroom-banner`), tipografi elegan, dan tombol aksi yang selaras. Namun, beberapa tampilan (seperti `Dashboard Utama` dengan `.zenius-greeting-card` atau tampilan lama) masih menggunakan model kotak kaku ("kotak jelek"). Topbar profil pengguna (Gambar 2) sudah berbentuk rounded pill kapsul, tetapi keseragaman hero banner di keempat modul utama belum 100% konsisten.
- **Kebutuhan**:  
  Selaraskan tampilan header di keempat tampilan utama pembimbing:
  1. **Dashboard Utama** (`/pembimbing/dashboard`)
  2. **Direktori Peserta Bimbingan** (`/pembimbing/peserta`)
  3. **Logbook Peserta** (`/pembimbing/logbook`)
  4. **Tugas & Progres Magang** (`/pembimbing/tugas`)
  agar semuanya menggunakan pola **Classroom Hero Banner Khas Sleman (`.classroom-banner`)** dengan sudut melengkung halus (*border-radius: 16px*), latar belakang gradasi navy berkelas, dan layout responsif yang identik.

---

### 1.2 Kebutuhan 2: Penyederhanaan Filter Direktori Peserta (Hanya "Peserta Aktif" dan "Alumni Selesai")
- **Kondisi Eksisting**:  
  Pada baris filter tab di Direktori Peserta ([`peserta/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/peserta/index.blade.php)), terdapat 3 segmented tab:
  `[ Peserta Aktif 13 ]` `[ Perlu Perhatian (Alert) 12 ]` `[ Alumni Selesai 3 ]`
- **Kebutuhan**:  
  Hapus tab filter `Perlu Perhatian (Alert)`. Tampilan filter disederhanakan hanya memiliki **2 Tab**:
  1. `Peserta Aktif`
  2. `Alumni Selesai`
  Serta selaraskan kartu Tile Stats di atasnya agar fokus pada metrik yang relevan (Total Mentee, Peserta Aktif, Alumni Selesai).

---

### 1.3 Kebutuhan 3: Penghapusan Subtitle Panel Direktori Peserta
- **Kondisi Eksisting**:  
  Panel tabel daftar mentee memiliki subtitle panjang:
  `<x-gentelella.x-panel title="Daftar Mentee Bimbingan" subtitle="Monitoring progres durasi dan status evaluasi peserta magang">`
- **Kebutuhan**:  
  Hapus teks subtitle tersebut sehingga panel hanya menampilkan judul bersih:
  `title="Daftar Mentee Bimbingan"`.

---

### 1.4 Kebutuhan 4: Penghapusan Alert "Perlu Perhatian" di Daftar Mentee Bimbingan
- **Kondisi Eksisting**:  
  Di kolom nama peserta pada tabel Daftar Mentee Bimbingan, hampir seluruh peserta memiliki badge kuning `[Perlu Perhatian]` (akibat logika overdue atau jeda logbook harian). Hal ini menimbulkan kepanikan visual (alert fatigue) dan tidak dibutuhkan oleh pembimbing.
- **Kebutuhan**:  
  Hapus badge alert `[Perlu Perhatian]` di samping nama mentee pada tabel. Nama mentee ditampilkan secara bersih dan profesional.

---

### 1.5 Kebutuhan 5: Penghapusan Kolom & Data "Status Nilai" di Daftar Mentee Bimbingan
- **Kondisi Eksisting**:  
  Tabel Direktori Peserta memuat kolom `<th>Status Nilai</th>` dengan deretan badge `[Belum Dinilai]`.
- **Kebutuhan**:  
  Karena evaluasi nilai sudah tidak dikelola di tabel ini (atau telah dipusatkan pada modul terpisah), hapus kolom header `<th>Status Nilai</th>` dan seluruh sel `<td>` status nilai dari tabel Direktori Peserta. Sesuaikan `colspan` tabel empty-state dari 7 menjadi 6.

---

## 2. Rincian Perubahan Langkah demi Langkah (Step-by-Step Task)

### Step 1: Penyesuaian View Direktori Peserta (`resources/views/pembimbing/peserta/index.blade.php`)

Lokasi file: [`resources/views/pembimbing/peserta/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/peserta/index.blade.php)

1. **Hapus Tile Stat "Perlu Perhatian"**:
   Hapus blok `<x-gentelella.tile-stat title="Perlu Perhatian" ...>` (baris 70-82).  
   Tersisa 3 kartu metrik utama: `Total Mentee`, `Peserta Aktif`, dan `Alumni Selesai`.

2. **Hapus Tab Filter "Perlu Perhatian (Alert)"** (baris 101-114):
   ```blade
   {{-- SEBELUM --}}
   <div class="peserta-tabs">
       <a href="{{ route('pembimbing.peserta.index', ['tab' => 'aktif']) }}" class="peserta-tab-btn {{ $currentTab === 'aktif' ? 'active' : '' }}">
           <span>Peserta Aktif</span>
           <span class="peserta-tab-badge">{{ $countAktif }}</span>
       </a>
       <a href="{{ route('pembimbing.peserta.index', ['tab' => 'perhatian']) }}" class="peserta-tab-btn {{ $currentTab === 'perhatian' ? 'active' : '' }}">
           <span>Perlu Perhatian (Alert)</span>
           <span class="peserta-tab-badge {{ ($countPerhatian ?? 0) > 0 ? 'badge-alert' : '' }}">{{ $countPerhatian ?? 0 }}</span>
       </a>
       <a href="{{ route('pembimbing.peserta.index', ['tab' => 'selesai']) }}" class="peserta-tab-btn {{ $currentTab === 'selesai' ? 'active' : '' }}">
           <span>Alumni Selesai</span>
           <span class="peserta-tab-badge">{{ $countSelesai }}</span>
       </a>
   </div>

   {{-- SESUDAH --}}
   <div class="peserta-tabs">
       <a href="{{ route('pembimbing.peserta.index', ['tab' => 'aktif']) }}" class="peserta-tab-btn {{ $currentTab !== 'selesai' ? 'active' : '' }}">
           <span>Peserta Aktif</span>
           <span class="peserta-tab-badge">{{ $countAktif }}</span>
       </a>
       <a href="{{ route('pembimbing.peserta.index', ['tab' => 'selesai']) }}" class="peserta-tab-btn {{ $currentTab === 'selesai' ? 'active' : '' }}">
           <span>Alumni Selesai</span>
           <span class="peserta-tab-badge">{{ $countSelesai }}</span>
       </a>
   </div>
   ```

3. **Hapus Subtitle Panel "Daftar Mentee Bimbingan"** (baris 133):
   ```blade
   {{-- SEBELUM --}}
   <x-gentelella.x-panel title="Daftar Mentee Bimbingan" subtitle="Monitoring progres durasi dan status evaluasi peserta magang">

   {{-- SESUDAH --}}
   <x-gentelella.x-panel title="Daftar Mentee Bimbingan">
   ```

4. **Hapus Kolom "Status Nilai" pada Header Tabel** (baris 136-146):
   ```blade
   {{-- SEBELUM --}}
   <thead>
       <tr>
           <th>Peserta Bimbingan</th>
           <th>Asal Institusi &amp; Jurusan</th>
           <th>Jalur &amp; Bidang</th>
           <th>Periode Magang</th>
           <th>Progres Durasi</th>
           <th>Status Nilai</th>
           <th style="text-align: right;">Aksi</th>
       </tr>
   </thead>

   {{-- SESUDAH --}}
   <thead>
       <tr>
           <th>Peserta Bimbingan</th>
           <th>Asal Institusi &amp; Jurusan</th>
           <th>Jalur &amp; Bidang</th>
           <th>Periode Magang</th>
           <th>Progres Durasi</th>
           <th style="text-align: right;">Aksi</th>
       </tr>
   </thead>
   ```

5. **Hapus Alert "Perlu Perhatian" di Kolom Nama Mentee** (baris 165-172):
   ```blade
   {{-- SEBELUM --}}
   <div style="display: flex; align-items: center; gap: 6px;">
       <a href="{{ route('pembimbing.peserta.show', $p->id) }}" class="mentee-name-text">
           {{ $p->peserta?->nama }}
       </a>
       @if(in_array($p->id, $perhatianIds ?? []))
           <x-gentelella.badge variant="warning">Perlu Perhatian</x-gentelella.badge>
       @endif
   </div>

   {{-- SESUDAH --}}
   <div style="display: flex; align-items: center; gap: 6px;">
       <a href="{{ route('pembimbing.peserta.show', $p->id) }}" class="mentee-name-text">
           {{ $p->peserta?->nama }}
       </a>
   </div>
   ```

6. **Hapus Sel "Status Nilai" & Sesuaikan Empty State Colspan** (baris 202-225):
   - Hapus blok `<td>...Status Nilai...</td>`.
   - Ubah `<td colspan="7" class="table-empty-td">` menjadi `<td colspan="6" class="table-empty-td">`.

---

### Step 2: Penyesuaian Header Dashboard Utama Pembimbing (`resources/views/pembimbing/dashboard/partials/_header.blade.php`)

Lokasi file: [`resources/views/pembimbing/dashboard/partials/_header.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/dashboard/partials/_header.blade.php)

Ubah format greeting card kaku menjadi model **Classroom Hero Banner** yang selaras dengan halaman Tugas:
```blade
<div class="classroom-banner">
    <div class="banner-top-row">
        <div>
            <h1 class="banner-title">Dashboard Pembimbing</h1>
            <div class="banner-subtitle">
                <span>Bidang: <strong>{{ $pembimbingBidang }}</strong></span>
                <span>&bull;</span>
                <span>Pembimbing: <strong>{{ auth()->user()->nama }}</strong></span>
                <span>&bull;</span>
                <span>Diskominfo Kabupaten Sleman</span>
            </div>
        </div>
        <div>
            <a href="{{ route('pembimbing.tugas.create') }}" class="btn-create-task">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Beri Tugas Baru</span>
            </a>
        </div>
    </div>
</div>
```

---

### Step 3: Penyesuaian CSS Layout & Grid Statistik (`resources/css/pages/pembimbing/peserta-index.css`)

Lokasi file: [`resources/css/pages/pembimbing/peserta-index.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/peserta-index.css)

1. Sesuaikan grid `.tiles-grid` agar otomatis proporsional dengan 3 kartu metrik:
   ```css
   .tiles-grid {
       display: grid;
       grid-template-columns: repeat(3, 1fr);
       gap: 16px;
       margin-bottom: 24px;
   }

   @media (max-width: 900px) {
       .tiles-grid {
           grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
       }
   }
   ```
2. Pastikan header banner `.classroom-banner` memiliki `border-radius: 16px;`, background gradasi Sleman Navy, dan tanpa sudut kotak tajam di seluruh breakpoint.

---

### Step 4: Penyesuaian Backend Controller (`app/Http/Controllers/Pembimbing/PesertaController.php`)

Lokasi file: [`app/Http/Controllers/Pembimbing/PesertaController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/PesertaController.php)

- Pada penanganan query parameter `tab`:
  Jika `tab === 'selesai'`, filter mentee dengan status selesai. Jika tidak, default selalu menampilkan mentee aktif (`tab === 'aktif'`).
- Pertahankan penghitungan counter dan variabel backward compatibility untuk mencegah error pada API / JSON response.

---

## 3. Rencana Pengujian (Test Plan & Assertions)

Lokasi file uji:
- [`tests/Feature/PembimbingPortalStandardizationTest.php`](file:///g:/MAGKOM/tests/Feature/PembimbingPortalStandardizationTest.php)
- [`tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php`](file:///g:/MAGKOM/tests/Feature/AdvancedRoleEnhancementPhaseSixTest.php)

Poin verifikasi:
1. **Verifikasi Tab Filter**: Memastikan halaman hanya menampilkan `Peserta Aktif` dan `Alumni Selesai`, dan tidak lagi menampilkan `Perlu Perhatian (Alert)`.
2. **Verifikasi Penghapusan Subtitle**: Memastikan teks *"Monitoring progres durasi dan status evaluasi peserta magang"* tidak lagi muncul di halaman.
3. **Verifikasi Penghapusan Badge Alert**: Memastikan badge warning `Perlu Perhatian` di kolom nama peserta tidak ada lagi.
4. **Verifikasi Penghapusan Kolom Nilai**: Memastikan header `<th>Status Nilai</th>` dan badge `Belum Dinilai` tidak lagi muncul di tabel Direktori Peserta.
5. **Verifikasi Header Banner**: Memastikan keempat halaman pembimbing merender `.classroom-banner` yang konsisten.

---

## 4. Checklist Quality Gates

| No | Quality Gate | Perintah / Uji | Target |
| :---: | :--- | :--- | :--- |
| 1 | **Blade Guardrail** | Code review | Bebas tag `<style>` dan `<script>` di Blade |
| 2 | **CSS Pipeline** | `npm run build` | Sukses tanpa error |
| 3 | **Code Style** | `./vendor/bin/pint --test` | 100% Passed |
| 4 | **Static Analysis** | `./vendor/bin/phpstan analyse` | 0 errors |
| 5 | **Feature Tests** | `php artisan test --filter=Peserta` | All Green |

---

## 5. Pertanyaan Konfirmasi Sebelum Eksekusi

Mohon konfirmasi keselarasan rencana di atas:
- Apakah 3 kartu metrik di Direktori Peserta (`Total Mentee`, `Peserta Aktif`, `Alumni Selesai`) sudah sesuai dengan kebutuhan setelah tile "Perlu Perhatian" ditiadakan?
- Apakah dokumen plan ini sudah disetujui untuk dieksekusi?

