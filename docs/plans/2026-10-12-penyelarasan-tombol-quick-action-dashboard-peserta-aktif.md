# Rencana Kerja: Penyelarasan Tombol Quick Action Dashboard Peserta Aktif

## 1. Ringkasan Kebutuhan
- **Latar Belakang**:
  Pada Dashboard Peserta Magang yang berstatus aktif (`fase === 'aktif'`), terdapat 3 tombol aksi cepat (*Quick Actions Bar*):
  1. Tombol *"Isi Logbook Mingguan"* bergaya **Primary** (latar biru solid `--blue-600` / `#0751FF` dengan teks putih).
  2. Tombol *"Lihat Daftar Tugas"* bergaya **Secondary** (latar putih dengan border outline biru dan teks biru).
  3. Tombol *"Cek Lembar Nilai"* bergaya **Secondary** (latar putih dengan border outline biru dan teks biru).
- **Permintaan Pengguna**:
  Menyelaraskan gaya tombol agar seragam/sama persis menjadi biru solid seperti tombol logbook (`btn btn-primary`).

---

## 2. Analisis File & Perubahan Target
- **Target File**: [`resources/views/peserta/dashboard.blade.php`](file:///d:/MAGKOM/MAGKOM/resources/views/peserta/dashboard.blade.php)
- **Baris Kode Target**: Baris 598–620 pada blok `{{-- Quick Action Bar --}}`.
- **Modifikasi**:
  - Ubah class tombol **Lihat Daftar Tugas** dari `btn btn-secondary` menjadi `btn btn-primary`.
  - Ubah class tombol **Cek Lembar Nilai** dari `btn btn-secondary` menjadi `btn btn-primary`.

### Perbandingan Kode:
```blade
{{-- SEBELUM (Gaya Berbeda-beda): --}}
<div class="quick-actions-bar">
    <a href="{{ route('peserta.logbook.index') }}" class="btn btn-primary">
        ...
        Isi Logbook Mingguan
    </a>
    <a href="{{ route('peserta.tugas.index') }}" class="btn btn-secondary">
        ...
        Lihat Daftar Tugas
    </a>
    <a href="{{ route('peserta.nilai.show') }}" class="btn btn-secondary">
        ...
        Cek Lembar Nilai
    </a>
</div>

{{-- SESUDAH (Seragam Biru Primary Solid): --}}
<div class="quick-actions-bar">
    <a href="{{ route('peserta.logbook.index') }}" class="btn btn-primary">
        ...
        Isi Logbook Mingguan
    </a>
    <a href="{{ route('peserta.tugas.index') }}" class="btn btn-primary">
        ...
        Lihat Daftar Tugas
    </a>
    <a href="{{ route('peserta.nilai.show') }}" class="btn btn-primary">
        ...
        Cek Lembar Nilai
    </a>
</div>
```

---

## 3. Rencana Pengujian & Verifikasi
1. **Verifikasi Sintaks & Render**:
   - Memastikan view `peserta.dashboard` dapat di-render tanpa error sintaks.
   - Memastikan ketiga tombol pada `.quick-actions-bar` memiliki class `btn btn-primary`.
2. **Automated Feature Tests**:
   - Menjalankan test feature dashboard peserta: `php artisan test --filter=Dashboard` untuk memastikan seluruh alur dan assertion tetap lulus.

---

## 4. Konfirmasi Pengguna
Apakah rencana penyeragaman ketiga tombol quick action ini disetujui untuk diimplementasikan?
