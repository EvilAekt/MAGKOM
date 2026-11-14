# TASK SPECIFICATION & IMPLEMENTATION PLAN
## Mengganti Native Browser Confirm (`window.confirm`) dengan Custom Modal Dialog UI pada Modul SuperAdmin
**Dokumen Referensi:** PRD MagKom v1.2 (F-11 & BR-15), Design System MagKom v1.0, WCAG 2.1 AA  
**Target Pelaksana:** Junior Developer / AI Agents (Gemini 3.8 Flash / Antigravity Assistant)  
**Tingkat Kerumitan:** Pemula &ndash; Menengah (Beginner &ndash; Intermediate)  
**Task ID:** `TASK-SA-CONFIRM-MODAL-01`  
**Status:** Siap Dieksekusi  

---

## 1. LATAR BELAKANG & ANALISIS MASALAH (ROOT CAUSE ANALYSIS)

### 1.1. Kondisi Saat Ini (Current State)
Saat SuperAdmin menekan tombol **"Nonaktifkan"** atau **"Aktifkan"** pada tabel manajemen staf (`resources/views/superadmin/staf/index.blade.php`), browser memunculkan dialog modal bawaan sistem operasi / browser:
```text
localhost:8000 says
Nonaktifkan akun staf ini?
[ OK ]  [ Cancel ]
```

### 1.2. Akar Masalah Teknis (Root Cause)
Pada berkas [resources/views/superadmin/staf/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) baris 293, terdapat kode inline event handler:
```blade
<button type="submit" onclick="return confirm('{{ $staf->status_aktif ? 'Nonaktifkan akun staf ini?' : 'Aktifkan kembali akun staf ini?' }}')" ...>
```

### 1.3. Mengapa Native Browser Dialog Ini Harus Dihapus?
1. **Pelanggaran Aturan Repositori (Rule 3.1 & SEC-02)**: Dilarang menggunakan inline handler `onclick="..."` di file Blade. Seluruh interaksi harus dikelola secara modular oleh ES Modules di `resources/js/`.
2. **Estetika & Brand Identity Rusak**: Dialog native browser tidak mengikuti token warna Pemkab Sleman, font Plus Jakarta Sans / Open Sans, dan terkesan seperti aplikasi belum selesai dibuat.
3. **Aksesibilitas (WCAG 2.1 AA) Rendah**: Native confirm tidak mendukung screen reader announcement khusus, tidak bisa menampilkan detail nama pengguna yang akan dinonaktifkan, dan tidak memiliki indikasi konsekuensi aksi.
4. **UX Buruk pada Perangkat Bergerak**: Pada layar ponsel atau tablet, popup native browser memblokir seluruh viewport dan sering kali tertekan secara tidak sengaja.

---

## 2. SPESIFIKASI DESAIN MODAL (DESIGN SYSTEM v1.0 COMPLIANCE)

Modal konfirmasi baru harus mengikuti standar kartu modal MagKom yang sudah terpasang pada `modalTambahStaf`, `modalEditStaf`, dan `modalResetSandi`:

### 2.1. Anatomi Komponen Modal
```
┌─────────────────────────────────────────────────────────────┐
│  [!] Konfirmasi Status Akun Staf                        [X] │  <-- Header (Icon Danger/Success + Title)
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐  │
│  │ Peringatan: Anda akan MENONAKTIFKAN akun staf:        │  │  <-- Callout Box (Konteks & Nama Staf)
│  │ Budi Santoso, S.Kom (budi.santoso@gmail.com)          │  │
│  │                                                       │  │
│  │ Konsekuensi:                                          │  │
│  │ • Sesi login aktif staf akan langsung dicabut.        │  │
│  │ • Staf tidak dapat mengakses dashboard portal MagKom  │  │
│  │   hingga akun diaktifkan kembali.                     │  │
│  └───────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                                      [ Batal ]  [ Ya, ... ] │  <-- Footer (Secondary + Destructive/Success)
└─────────────────────────────────────────────────────────────┘
```

### 2.2. Token Warna & Styling
- **Backdrop**: `position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999;`
- **Dialog Box**: Lebar maksimal `480px`, border radius `16px`, padding `1.75rem`, box shadow `var(--shadow-modal)`, background `var(--neutral-0, #FFFFFF)`.
- **Icon Box**:
  - Untuk Aksi Nonaktifkan: Background `var(--red-100, #FEE2E2)`, Icon `var(--red-600, #DC2626)`.
  - Untuk Aksi Aktifkan: Background `var(--green-100, #DCFCE7)`, Icon `var(--green-600, #16A34A)`.
- **Tombol Konfirmasi**:
  - Nonaktifkan: Warna merah solid (`background: #DC2626; color: #FFFFFF; font-weight: 700; height: 42px; border-radius: 8px;`).
  - Aktifkan: Warna hijau solid (`background: #16A34A; color: #FFFFFF; font-weight: 700; height: 42px; border-radius: 8px;`).
- **Tombol Batal**: Tombol sekunder (`background: var(--neutral-100); border: 1px solid var(--neutral-300); color: var(--neutral-700); height: 42px; border-radius: 8px;`).

---

## 3. FILE YANG HARUS DIMODIFIKASI

| File | Peran / Tanggung Jawab |
|---|---|
| [resources/views/superadmin/staf/index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php) | 1. Hapus inline `onclick="return confirm(...)"`<br>2. Ubah button menjadi trigger modal dengan class `.btn-trigger-toggle-staf`<br>3. Tambahkan komponen modal `#modalKonfirmasiToggleStaf` di akhir file. |
| [resources/js/pages/superadmin/staf-index.js](file:///g:/MAGKOM/resources/js/pages/superadmin/staf-index.js) | 1. Tangkap event click pada `.btn-trigger-toggle-staf`<br>2. Isi data dinamis (`nama`, `email`, `role`, `status_aktif`, form action URL)<br>3. Tampilkan modal konfirmasi.<br>4. Tangani penutupan modal via tombol Batal, tombol silang (X), ESC, atau klik backdrop. |

---

## 4. PANDUAN IMPLEMENTASI LANGKAH DEMI LANGKAH (STEP-BY-STEP)

---

### LANGKAH 1: Modifikasi Tombol di Tabel Staf ([index.blade.php](file:///g:/MAGKOM/resources/views/superadmin/staf/index.blade.php))

Cari bagian form toggle status di baris ~289-296:
```blade
<!-- SEBELUM: MENGGUNAKAN NATIVE ONCLICK CONFIRM -->
<form action="{{ route('superadmin.staf.toggle', $staf) }}" method="POST" style="display: inline-block; margin: 0;">
    @csrf
    @method('PATCH')
    <button type="submit" onclick="return confirm('{{ $staf->status_aktif ? 'Nonaktifkan akun staf ini?' : 'Aktifkan kembali akun staf ini?' }}')" class="{{ $staf->status_aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $staf->status_aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
        {{ $staf->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
    </button>
</form>
```

Ubah menjadi tombol biasa (`type="button"`) tanpa inline handler, dan berikan atribut semantik `data-*`:
```blade
<!-- SESUDAH: BERSIH TANPA INLINE HANDLER & TERHUBUNG KE MODAL -->
<button type="button" 
        class="btn-trigger-toggle-staf {{ $staf->status_aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
        style="{{ $staf->status_aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}"
        data-id="{{ $staf->id }}"
        data-nama="{{ $staf->nama }}"
        data-email="{{ $staf->email }}"
        data-role="{{ $staf->role->label() ?? $staf->role->value }}"
        data-status-aktif="{{ $staf->status_aktif ? '1' : '0' }}"
        data-action-url="{{ route('superadmin.staf.toggle', $staf) }}">
    {{ $staf->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
</button>
```

---

### LANGKAH 2: Tambahkan Markup HTML Modal Konfirmasi di Akhir File Blade

Tambahkan elemen modal berikut tepat di bawah `modalResetSandi` / sebelum `@endsection`:

```blade
<!-- =====================================================================
     MODAL KONFIRMASI STATUS AKUN STAF (NONAKTIFKAN / AKTIFKAN)
     Sesuai MagKom Design System v1.0 & Bebas Native Alert Pop-up
     ===================================================================== -->
<div id="modalKonfirmasiToggleStaf" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleStafTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal, 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <!-- Header Modal -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleStafIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <!-- SVG Warning Icon -->
                    <svg id="toggleStafIconSvg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleStafTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0; line-height: 1.3;">
                        Konfirmasi Perubahan Akun
                    </h3>
                    <span id="toggleStafSubtitle" style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                        Manajemen Staf Internal Diskominfo Sleman
                    </span>
                </div>
            </div>
            <button type="button" 
                    data-modal-dismiss="modalKonfirmasiToggleStaf" 
                    style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400, #94A3B8); line-height: 1; padding: 0.25rem; border-radius: 6px;"
                    title="Tutup dialog">
                &times;
            </button>
        </div>

        <!-- Form Aksi Nyata -->
        <form id="formToggleStaf" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')

            <!-- Kotak Peringatan Konsekuensi -->
            <div id="toggleStafBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleStafMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin menonaktifkan akun staf berikut?
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(225, 29, 72, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900, #0F172A); font-size: 0.875rem;" id="toggleStafNama">
                        Nama Staf
                    </div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600, #475569);" id="toggleStafEmail">
                        email@sleman.go.id
                    </div>
                </div>
                <p id="toggleStafConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    &bull; Sesi login aktif staf akan ditutup dan staf tidak dapat mengakses portal MagKom hingga diaktifkan kembali.
                </p>
            </div>

            <!-- Footer Tombol Aksi -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200, #E2E8F0); padding-top: 1rem;">
                <button type="button" 
                        data-modal-dismiss="modalKonfirmasiToggleStaf" 
                        style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100, #F1F5F9); border: 1px solid var(--neutral-300, #CBD5E1); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700, #334155); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" 
                        id="btnSubmitToggleStaf" 
                        style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem; transition: background 0.15s ease;">
                    <span id="btnSubmitToggleText">Ya, Nonaktifkan Staf</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

---

### LANGKAH 3: Tambahkan Logic Handler di JavaScript ([staf-index.js](file:///g:/MAGKOM/resources/js/pages/superadmin/staf-index.js))

Buka berkas `resources/js/pages/superadmin/staf-index.js`. Di dalam event listener `document.addEventListener('click', ...)`, tambahkan handler untuk kelas `.btn-trigger-toggle-staf`:

```javascript
        // Trigger Buka Modal Konfirmasi Toggle Status Staf (Bebas window.confirm)
        const triggerToggle = event.target.closest('.btn-trigger-toggle-staf');
        if (triggerToggle) {
            event.preventDefault();
            const id = triggerToggle.dataset.id;
            const nama = triggerToggle.dataset.nama || 'Staf';
            const email = triggerToggle.dataset.email || '-';
            const isAktif = triggerToggle.dataset.statusAktif === '1';
            const actionUrl = triggerToggle.dataset.actionUrl || `/superadmin/staf/${id}/toggle`;

            const modalToggle = document.getElementById('modalKonfirmasiToggleStaf');
            const formToggle = document.getElementById('formToggleStaf');
            const titleEl = document.getElementById('modalToggleStafTitle');
            const namaEl = document.getElementById('toggleStafNama');
            const emailEl = document.getElementById('toggleStafEmail');
            const msgEl = document.getElementById('toggleStafMessage');
            const boxAlert = document.getElementById('toggleStafBoxAlert');
            const iconWrap = document.getElementById('toggleStafIconWrap');
            const consequenceEl = document.getElementById('toggleStafConsequence');
            const submitBtn = document.getElementById('btnSubmitToggleStaf');
            const submitText = document.getElementById('btnSubmitToggleText');

            if (formToggle) formToggle.action = actionUrl;
            if (namaEl) namaEl.textContent = nama;
            if (emailEl) emailEl.textContent = email;

            if (isAktif) {
                // Mode: Menonaktifkan Akun Staf (Danger / Merah)
                if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Staf';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan akun staf berikut?';
                if (consequenceEl) consequenceEl.textContent = '• Sesi aktif staf akan dicabut seketika dan staf tidak dapat login ke portal hingga diaktifkan kembali.';
                if (boxAlert) {
                    boxAlert.style.background = '#FFF1F2';
                    boxAlert.style.borderColor = '#FECDD3';
                }
                if (iconWrap) {
                    iconWrap.style.background = '#FEE2E2';
                    iconWrap.style.color = '#DC2626';
                }
                if (submitBtn) {
                    submitBtn.style.background = '#DC2626';
                }
                if (submitText) submitText.textContent = 'Ya, Nonaktifkan Staf';
            } else {
                // Mode: Mengaktifkan Kembali Akun Staf (Success / Hijau)
                if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Staf';
                if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali akun staf berikut?';
                if (consequenceEl) consequenceEl.textContent = '• Akun staf akan dipulihkan dan staf dapat kembali login ke portal internal MagKom.';
                if (boxAlert) {
                    boxAlert.style.background = '#F0FDF4';
                    boxAlert.style.borderColor = '#BBF7D0';
                }
                if (iconWrap) {
                    iconWrap.style.background = '#DCFCE7';
                    iconWrap.style.color = '#16A34A';
                }
                if (submitBtn) {
                    submitBtn.style.background = '#16A34A';
                }
                if (submitText) submitText.textContent = 'Ya, Aktifkan Staf';
            }

            if (modalToggle) modalToggle.style.display = 'flex';
            return;
        }
```

Pastikan juga `modalKonfirmasiToggleStaf` ditutup saat backdrop diklik atau tombol ESC ditekan:
```javascript
// Di dalam event listener backdrop click:
if (event.target === modalKonfirmasiToggleStaf) modalKonfirmasiToggleStaf.style.display = 'none';

// Di dalam event listener keydown ESC:
if (modalKonfirmasiToggleStaf) modalKonfirmasiToggleStaf.style.display = 'none';
```

---

## 5. POLA EKSTENSI: REUSABLE CONFIRM MODAL (UNTUK MODUL LAIN)

Pola yang sama dapat digunakan untuk menghapus seluruh dialog `window.confirm` lainnya di SuperAdmin:
1. **Master Bidang** (`resources/views/superadmin/bidang/index.blade.php`): Ganti `confirm('Nonaktifkan bidang ini?')`.
2. **Master Jurusan** (`resources/views/superadmin/jurusan/index.blade.php`): Ganti `confirm('Nonaktifkan jurusan ini?')`.
3. **Kran Bulan Magang** (`resources/views/superadmin/bulan/index.blade.php`): Ganti `confirm('Ubah status aktif bulan magang ini?')`.
4. **Trigger Snapshot Backup** (`resources/views/superadmin/backup/index.blade.php`): Ganti `confirm('Picu pembuatan snapshot basis data baru?')`.

---

## 6. KRITERIA PENERIMAAN (DEFINITION OF DONE / DoD)

Tugas ini dinyatakan selesai dan lulus jika memenuhi seluruh kriteria berikut:
- [ ] **Zero Native Dialog**: Saat menekan tombol "Nonaktifkan" atau "Aktifkan", browser **TIDAK PERNAH** memunculkan alert `localhost:8000 says: Nonaktifkan akun staf ini?`.
- [ ] **Custom UI Rendered**: Tampil modal kustom cantik dengan warna Sleman Navy/Red, ikon warning, nama staf terpilih, dan rincian konsekuensi.
- [ ] **Mode Dinamis**: Saat menonaktifkan akun staf, tombol bertuliskan "Ya, Nonaktifkan Staf" dengan aksen merah; saat mengaktifkan kembali, tombol bertuliskan "Ya, Aktifkan Staf" dengan aksen hijau.
- [ ] **Eksekusi Sukses**: Menekan tombol konfirmasi di modal berhasil mengeksekusi request `PATCH /superadmin/staf/{id}/toggle`, mengubah status di basis data MySQL, dan menampilkan notifikasi toast sukses.
- [ ] **Aksesibilitas & Keyboard Navigation**: Dialog dapat ditutup dengan tombol "Batal", tombol "X", klik backdrop, atau menekan tombol `ESC`.
- [ ] **Zero Inline Handlers**: Tidak ada atribut `onclick="return confirm(...)"` yang tersisa di `resources/views/superadmin/staf/index.blade.php`.

---

## 7. QUALITY GATES & COMMAND VERIFIKASI

Jalankan perintah berikut sebelum menyatakan tugas selesai:

1. **Jalankan Feature Test Staf SuperAdmin**:
   ```bash
   php artisan test --filter=Staf
   ```
2. **Jalankan Static Analysis**:
   ```bash
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ```
3. **Jalankan Format Linter**:
   ```bash
   ./vendor/bin/pint --test
   ```
4. **Kompilasi Ulang Aset Vite**:
   ```bash
   npm run build
   ```
   *Pastikan build selesai tanpa error.*

---
**Persetujuan Rencana:**  
Rencana tugas ini siap dieksekusi secara terfokus pada modul SuperAdmin Staf. Mohon konfirmasi untuk langsung memulai eksekusinya.
