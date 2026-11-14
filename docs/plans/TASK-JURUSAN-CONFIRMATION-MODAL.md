# TASK SPECIFICATION & IMPLEMENTATION PRD
## Standarisasi Modal Dialog Konfirmasi (Eliminasi `window.confirm` Native Browser) pada Master Data Jurusan MagKom
**Dokumen Referensi:** PRD MagKom v1.2, Design System MagKom v1.0, WCAG 2.1 AA  
**Peran:** Task Directive dari Senior Lead Developer kepada Junior Developer / AI Assistant (Gemini 3.8 Flash)  
**Tingkat Kerumitan:** Pemula &ndash; Menengah (Beginner &ndash; Intermediate)  
**Target Modul:** Modul SuperAdmin &ndash; Master Data Jurusan  
**Task ID:** `TASK-SA-JURUSAN-CONFIRM-MODAL-02`  
**Status Dokumen:** Siap Dikerjakan (*Actionable & Ready for Execution*)  

---

## 1. DIRECTIVE & PESAN DARI SENIOR DEVELOPER

Halo Junior Developer / AI Assistant! 👋

Perhatikan gambar tangkapan layar bug yang dilaporkan pengguna pada halaman **Master Data Jurusan**:

### Masalah Utama:
1. **Muncul Alert Bawaan Browser**: Saat menekan tombol **"Nonaktifkan"** atau **"Aktifkan"** pada tabel Master Program Studi & Jurusan, browser memunculkan alert bawaan:
   ```text
   localhost:8000 says
   Nonaktifkan jurusan ini? Pastikan tidak ada kuota dan penempatan aktif.
   [ OK ]  [ Cancel ]
   ```
2. **Pelanggaran Aturan Arsitektur Repositori (Rule 3.1 & 3.2)**:
   - DILARANG menggunakan inline event handler seperti `onclick="return confirm(...)"` di dalam berkas Blade!
   - Semua event handling dan interaksi wajib dipisahkan ke berkas JavaScript modular (ES Modules) di `resources/js/pages/superadmin/jurusan-index.js`.
3. **Inkonsistensi Antarmuka**:
   - Fitur lain di SuperAdmin (Data Staf, Master Bidang, Slot Khusus, Bulan Magang, dan Backup) sudah menggunakan modal kustom yang elegan dan responsif. Master Jurusan adalah modul yang tertinggal dan harus disamakan (*parity*).

### Tugasmu:
Ganti dialog native `confirm(...)` pada tombol toggle status di **Master Data Jurusan** menjadi **Custom Modal Dialog UI** berstandar MagKom Design System v1.0.

Ikuti instruksi langkah demi langkah di bawah ini secara disiplin!

---

## 2. ANATOMI & STANDAR DESAIN MODAL MAGKOM (DESIGN SYSTEM v1.0)

Modal konfirmasi baru harus memiliki struktur visual yang konsisten dengan modul Master Bidang:

```text
┌─────────────────────────────────────────────────────────────┐
│  [Icon] Konfirmasi Status Jurusan                       [X] │  <-- Header (Icon Danger/Success + Title)
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐  │
│  │ Peringatan: Anda akan MENONAKTIFKAN jurusan:          │  │  <-- Callout Box (Konteks & Nama Jurusan)
│  │ Desain Grafis (Perguruan Tinggi)                      │  │
│  │                                                       │  │
│  │ Konsekuensi Operasional:                              │  │
│  │ • Pastikan tidak ada alokasi kuota dan penempatan     │  │
│  │   peserta aktif pada jurusan ini.                     │  │
│  │ • Jurusan tidak akan muncul pada formulir pendaftaran │  │
│  │   peserta baru.                                       │  │
│  └───────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                                      [ Batal ]  [ Ya, ... ] │  <-- Footer (Secondary + Destructive/Success)
└─────────────────────────────────────────────────────────────┘
```

### Standar Token & Styling:
- **Backdrop**: `position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1.5rem;`
- **Dialog Box**: `background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); border: 1px solid var(--neutral-300, #CBD5E1); position: relative;`
- **Mode Nonaktifkan (Danger / Merah)**:
  - Icon Wrap: Background `#FEE2E2`, Icon Color `#DC2626`
  - Alert Box: Background `#FFF1F2`, Border `#FECDD3`, Text `#9F1239`
  - Tombol Submit: Background `#DC2626`, Text `#FFFFFF`, Label `"Ya, Nonaktifkan Jurusan"`
- **Mode Aktifkan (Success / Hijau)**:
  - Icon Wrap: Background `#DCFCE7`, Icon Color `#16A34A`
  - Alert Box: Background `#F0FDF4`, Border `#BBF7D0`, Text `#166534`
  - Tombol Submit: Background `#16A34A`, Text `#FFFFFF`, Label `"Ya, Aktifkan Jurusan"`

---

## 3. FILE YANG HARUS DIMODIFIKASI

| File | Peran / Tanggung Jawab |
|---|---|
| `resources/views/superadmin/jurusan/index.blade.php` | 1. Hapus form inline `onclick="return confirm(...)"` di baris ~192–198.<br>2. Ubah menjadi tombol pemicu `.btn-trigger-toggle-jurusan` dengan atribut semantik `data-*`.<br>3. Tambahkan komponen modal `#modalKonfirmasiToggleJurusan` di akhir file sebelum `@push('scripts')`. |
| `resources/js/pages/superadmin/jurusan-index.js` | 1. Tangkap event click pada `.btn-trigger-toggle-jurusan`.<br>2. Isi data dinamis (`nama`, `kategori`, `status_aktif`, `actionUrl`).<br>3. Buka modal dan sesuaikan warna/pesan untuk mode Aktif vs Nonaktif.<br>4. Tangani penutupan modal via tombol Batal, silang (X), ESC, atau klik backdrop. |

---

## 4. PANDUAN IMPLEMENTASI LANGKAH DEMI LANGKAH (STEP-BY-STEP)

---

### LANGKAH 1: Modifikasi Tombol di Tabel Jurusan (`resources/views/superadmin/jurusan/index.blade.php`)

Cari baris ~192–198 pada tabel jurusan:
```blade
{{-- SEBELUM: MENGGUNAKAN NATIVE ONCLICK CONFIRM --}}
<form action="{{ route('superadmin.jurusan.toggle', $jurusan) }}" method="POST" style="display: inline-block; margin: 0;">
    @csrf
    @method('PATCH')
    <button type="submit" onclick="return confirm('{{ $jurusan->aktif ? 'Nonaktifkan jurusan ini? Pastikan tidak ada kuota dan penempatan aktif.' : 'Aktifkan kembali jurusan ini?' }}')" class="{{ $jurusan->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $jurusan->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
        {{ $jurusan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
    </button>
</form>
```

Ganti menjadi tombol biasa tanpa form inline, dengan class `.btn-trigger-toggle-jurusan` dan atribut `data-*`:
```blade
{{-- SESUDAH: BERSIH TANPA INLINE HANDLER & MENGGUNAKAN DATASET SEMANTIK --}}
<button type="button" 
        class="btn-trigger-toggle-jurusan {{ $jurusan->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
        style="{{ $jurusan->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}"
        data-id="{{ $jurusan->id }}"
        data-nama="{{ $jurusan->nama_jurusan }}"
        data-kategori="{{ $jurusan->kategori === 'mahasiswa' ? 'Perguruan Tinggi (D3/D4/S1)' : 'SMK / Vokasi' }}"
        data-status-aktif="{{ $jurusan->aktif ? '1' : '0' }}"
        data-action-url="{{ route('superadmin.jurusan.toggle', $jurusan) }}">
    {{ $jurusan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
</button>
```

---

### LANGKAH 2: Tambahkan Markup HTML Modal di Akhir File Blade

Tambahkan elemen modal berikut tepat di bawah `modalEditJurusan` / sebelum `@push('scripts')`:

```blade
<!-- =====================================================================
     MODAL KONFIRMASI STATUS JURUSAN (NONAKTIFKAN / AKTIFKAN)
     Sesuai MagKom Design System v1.0 & Bebas Native Alert Pop-up
     ===================================================================== -->
<div id="modalKonfirmasiToggleJurusan" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleJurusanTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <!-- Header Modal -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleJurusanIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleJurusanTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; line-height: 1.3;">
                        Konfirmasi Status Jurusan
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                        Master Program Studi &amp; Kualifikasi Akademik
                    </span>
                </div>
            </div>
            <button type="button" 
                    data-modal-dismiss="modalKonfirmasiToggleJurusan" 
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400, #94A3B8); line-height: 1; padding: 0.25rem; border-radius: 6px;" 
                    title="Tutup dialog">
                &times;
            </button>
        </div>

        <!-- Form Aksi Nyata -->
        <form id="formToggleJurusan" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')

            <!-- Kotak Peringatan Konsekuensi -->
            <div id="toggleJurusanBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleJurusanMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin menonaktifkan program studi / jurusan ini?
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;" id="toggleJurusanNama">
                        Nama Jurusan
                    </div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600, #475569);" id="toggleJurusanKategori">
                        Perguruan Tinggi (D3/D4/S1)
                    </div>
                </div>
                <p id="toggleJurusanConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    • Pastikan tidak ada alokasi kuota dan penempatan aktif. Jurusan tidak akan muncul pada formulir pendaftaran peserta baru.
                </p>
            </div>

            <!-- Footer Tombol Aksi -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1rem;">
                <button type="button" 
                        data-modal-dismiss="modalKonfirmasiToggleJurusan" 
                        style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100, #F1F5F9); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700, #334155); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" 
                        id="btnSubmitToggleJurusan" 
                        style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                    <span id="btnSubmitToggleJurusanText">Ya, Nonaktifkan Jurusan</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

---

### LANGKAH 3: Modifikasi JavaScript Handler (`resources/js/pages/superadmin/jurusan-index.js`)

Buka berkas `resources/js/pages/superadmin/jurusan-index.js`:
1. Tambahkan deklarasi `modalToggle`:
   ```javascript
   const modalToggle = document.getElementById('modalKonfirmasiToggleJurusan');
   ```
2. Tangani klik pada `.btn-trigger-toggle-jurusan`:
   ```javascript
   // Trigger Buka Modal Konfirmasi Toggle Jurusan
   const triggerToggle = event.target.closest('.btn-trigger-toggle-jurusan');
   if (triggerToggle) {
       event.preventDefault();
       const nama = triggerToggle.dataset.nama || 'Jurusan';
       const kategori = triggerToggle.dataset.kategori || 'SMK / Mahasiswa';
       const isAktif = triggerToggle.dataset.statusAktif === '1';
       const actionUrl = triggerToggle.dataset.actionUrl;

       const formToggle = document.getElementById('formToggleJurusan');
       const namaEl = document.getElementById('toggleJurusanNama');
       const kategoriEl = document.getElementById('toggleJurusanKategori');
       const titleEl = document.getElementById('modalToggleJurusanTitle');
       const msgEl = document.getElementById('toggleJurusanMessage');
       const consequenceEl = document.getElementById('toggleJurusanConsequence');
       const boxAlert = document.getElementById('toggleJurusanBoxAlert');
       const iconWrap = document.getElementById('toggleJurusanIconWrap');
       const submitBtn = document.getElementById('btnSubmitToggleJurusan');
       const submitText = document.getElementById('btnSubmitToggleJurusanText');

       if (formToggle && actionUrl) formToggle.action = actionUrl;
       if (namaEl) namaEl.textContent = nama;
       if (kategoriEl) kategoriEl.textContent = kategori;

       if (isAktif) {
           if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Jurusan';
           if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan program studi / jurusan ini?';
           if (consequenceEl) consequenceEl.textContent = '• Pastikan tidak ada alokasi kuota dan penempatan aktif. Jurusan tidak akan muncul pada formulir pendaftaran peserta baru.';
           if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
           if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
           if (submitBtn) submitBtn.style.background = '#DC2626';
           if (submitText) submitText.textContent = 'Ya, Nonaktifkan Jurusan';
       } else {
           if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Jurusan';
           if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali program studi / jurusan ini?';
           if (consequenceEl) consequenceEl.textContent = '• Jurusan akan kembali aktif dan dapat dipilih oleh calon peserta magang pada saat pendaftaran online.';
           if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
           if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
           if (submitBtn) submitBtn.style.background = '#16A34A';
           if (submitText) submitText.textContent = 'Ya, Aktifkan Jurusan';
       }

       if (modalToggle) modalToggle.style.display = 'flex';
       return;
   }
   ```
3. Pastikan penutupan modal mencakup `modalToggle` pada:
   - Klik backdrop (`if (event.target === modalToggle) modalToggle.style.display = 'none';`)
   - Tombol keyboard ESC (`if (modalToggle) modalToggle.style.display = 'none';`)

---

## 5. QUALITY GATES & COMMAND VERIFIKASI

Jalankan perintah berikut sebelum menyatakan tugas selesai:

1. **Verifikasi Tidak Ada `confirm(` yang Tertinggal**:
   ```bash
   git grep "confirm(" resources/views/superadmin/jurusan/
   ```
   *Hasil harus KOSONG.*

2. **Kompilasi Aset Frontend Vite**:
   ```bash
   npm run build
   ```
   *Pastikan build selesai tanpa error.*

3. **Format & Standar Kode**:
   ```bash
   ./vendor/bin/pint --test
   ```

4. **Static Code Analysis**:
   ```bash
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ```

5. **Feature Test Jurusan**:
   ```bash
   php artisan test --filter=Jurusan
   ```

---

## 6. DEFINITION OF DONE (DoD)

Task ini dinyatakan **SELESAI & LULUS REVIEW** jika:
1. Saat menekan tombol "Nonaktifkan" atau "Aktifkan", browser **TIDAK PERNAH** memunculkan alert `localhost:8000 says: Nonaktifkan jurusan ini? ...`.
2. Muncul modal dialog kustom cantik dengan warna Sleman Navy/Red/Green, memuat nama jurusan dan kategori pendidikan yang bersangkutan.
3. Form di dalam modal berhasil melakukan submit `PATCH /superadmin/jurusan/{id}/toggle` dengan token `@csrf`.
4. Modal dapat ditutup dengan tombol Batal, tombol silang (X), klik backdrop luar, dan tombol `Escape`.
5. Semua quality gate (`pint`, `phpstan`, `npm run build`, `artisan test`) berstatus hijau.
