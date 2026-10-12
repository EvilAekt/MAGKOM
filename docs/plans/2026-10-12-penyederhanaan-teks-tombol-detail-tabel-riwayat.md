# Rencana Kerja: Penyederhanaan Teks Tombol Aksi Tabel Riwayat Pendaftaran Menjadi "Detail"

## 1. Konteks & Permasalahan
Pada tabel **Riwayat Pengajuan Pendaftaran Magang** (`resources/views/peserta/pendaftaran/index.blade.php`), kolom **Aksi**:
- Tombol aksi saat ini menggunakan teks **"Lihat Detail"** dengan ikon mata.
- Karena lebar kolom aksi tabel yang terbatas, teks tersebut membungkus (*wrap*) menjadi dua baris: baris atas *"Lihat"* dan baris bawah *"Detail"*, sehingga tombol terlihat terjepit, aneh, dan tidak proporsional secara vertikal.

## 2. Solusi Desain yang Diusulkan
Menyederhanakan teks tombol aksi tabel menjadi **"Detail"** saja serta menambahkan proteksi `white-space: nowrap`:

1. **Perubahan Teks & Markup**:
   - Mengubah label tombol dari `Lihat Detail` menjadi `Detail`.
   - Menambahkan properti CSS `white-space: nowrap;` pada elemen tombol dan sel `<td>` aksi agar teks tidak pernah terputus menjadi dua baris dalam kondisi resolusi layar apa pun.
   - Mengatur flex alignment `display: inline-flex; align-items: center; gap: 6px;` dengan tinggi proporsional (`height: 34px; padding: 0 14px;`).

2. **Dampak Estetika**:
   - Tombol menjadi ramping, proporsional, dan elegan (*compact secondary button*).
   - Tampilan baris tabel riwayat menjadi lebih rapi tanpa ada elemen yang terhimpit.

## 3. File yang Dimodifikasi
1. `resources/views/peserta/pendaftaran/index.blade.php`:
   - Baris 966-974: Penyederhanaan label teks tombol menjadi `Detail` dan penambahan styling `white-space: nowrap`.

## 4. Pengujian & Verifikasi
1. **Pengujian Visual**:
   - Memastikan tombol aksi pada tabel riwayat pendaftaran bertuliskan `[ 👁 Detail ]` dalam satu baris rapi dan proporsional.
2. **Automated Testing**:
   - Menjalankan `tests/Feature/PesertaPendaftaranFlowTest.php` untuk memastikan seluruh alur dan navigasi ke halaman detail pendaftaran tetap 100% PASS.
