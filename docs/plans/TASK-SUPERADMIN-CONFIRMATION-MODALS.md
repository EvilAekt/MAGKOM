# TASK SPECIFICATION & IMPLEMENTATION PRD
## Standarisasi Modal Dialog Konfirmasi (Eliminasi `window.confirm` Native Browser) pada Modul SuperAdmin MagKom
**Dokumen Referensi:** PRD MagKom v1.2 (F-11 & BR-15), Design System MagKom v1.0, WCAG 2.1 AA  
**Peran:** Task Directive dari Senior Lead Developer kepada Junior Developer / AI Assistant (Gemini 3.8 Flash)  
**Tingkat Kerumitan:** Pemula &ndash; Menengah (Beginner &ndash; Intermediate)  
**Target Modul:** Modul SuperAdmin (Data Staf, Master Bidang, Slot Khusus, Bulan Magang, dan Backup Basis Data)  
**Status Dokumen:** Siap Dikerjakan (*Actionable & Ready for Execution*)  

---

## 1. DIRECTIVE & PESAN DARI SENIOR DEVELOPER

Halo Junior Developer / AI Assistant! 👋

Perhatikan gambar tangkapan layar bug yang dilaporkan pengguna saat menekan tombol **"Sandi Baru"** pada portal MagKom:

### Masalah Utama:
1. **Muncul Alert Bawaan Browser**: Saat tombol aksi ditekan, muncul kotak dialog kuno bawaan sistem operasi / browser (`localhost:8000 says: Buat kata sandi sementara baru...`). Hal ini merusak tampilan portal premium MagKom Dinas Kominfo Sleman.
2. **Pelanggaran Aturan Arsitektur Repositori (Rule 3.1 & 3.2)**:
   - DILARANG menggunakan inline event handler seperti `onclick="return confirm(...)"` atau `onsubmit="return confirm(...)"` di dalam berkas Blade!
   - Semua event handling dan interaksi wajib dipisahkan ke berkas JavaScript modular (ES Modules) di `resources/js/pages/superadmin/`.
3. **Inkredibilitas & Keamanan**:
   - Dialog native tidak memiliki konteks visual yang jelas, tidak menampilkan ringkasan data yang aman, dan tidak memberikan indikasi resiko aksi (*destructive vs non-destructive*).

### Tugasmu:
Ganti seluruh popup native `confirm(...)` yang tersisa pada 5 area modul SuperAdmin berikut menjadi **Custom Modal Dialog UI** berstandar MagKom Design System v1.0:
1. **Sandi Baru (Regenerate Temporary Password)** pada Data Staf
2. **Toggle Aktif / Nonaktif** pada Master Bidang Dinas Kominfo
3. **Toggle Aktif / Nonaktif** pada Manajemen Slot Khusus Internal
4. **Toggle Aktif / Nonaktif** pada Manajemen Bulan Magang
5. **Hapus Berkas Snapshot Permanen** pada Pusat Cadangan & Pemulihan Basis Data

Ikuti instruksi langkah demi langkah di bawah ini secara disiplin. Jangan mengarang nama class atau API baru!

---

## 2. ANATOMI & STANDAR DESAIN MODAL MAGKOM (DESIGN SYSTEM v1.0)

Semua modal konfirmasi di MagKom wajib memiliki struktur visual konsisten:

```text
┌─────────────────────────────────────────────────────────────┐
│  [Icon] Judul Modal Konfirmasi                          [X] │  <-- Header
├─────────────────────────────────────────────────────────────┤
│  ┌───────────────────────────────────────────────────────┐  │
│  │ [Peringatan / Konsekuensi Aksi]                       │  │  <-- Callout Box Konteks
│  │ • Data Entitas Terkait (Nama / Berkas / Periode)      │  │
│  │ • Rincian Efek Operasional                            │  │
│  └───────────────────────────────────────────────────────┘  │
├─────────────────────────────────────────────────────────────┤
│                                      [ Batal ]  [ Ya, ... ] │  <-- Footer Aksi
└─────────────────────────────────────────────────────────────┘
```

### Standar Styling:
- **Backdrop**: `position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1.5rem;`
- **Dialog Box**: `background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); border: 1px solid var(--neutral-300, #CBD5E1); position: relative;`
- **Aksen Tipe Aksi**:
  - **Danger / Destructive (Hapus / Nonaktifkan / Putus Sesi)**:
    - Icon Box: Background `#FEE2E2`, Icon Color `#DC2626`
    - Callout Box: Background `#FFF1F2`, Border `#FECDD3`, Text `#9F1239`
    - Submit Button: Background `#DC2626`, Hover `#B91C1C`, Text `#FFFFFF`
  - **Success / Restore (Aktifkan Kembali)**:
    - Icon Box: Background `#DCFCE7`, Icon Color `#16A34A`
    - Callout Box: Background `#F0FDF4`, Border `#BBF7D0`, Text `#166534`
    - Submit Button: Background `#16A34A`, Hover `#15803D`, Text `#FFFFFF`
  - **Warning / Security Key (Sandi Baru Sementara)**:
    - Icon Box: Background `#FEF3C7`, Icon Color `#D97706`
    - Callout Box: Background `#FFFBEB`, Border `#FDE68A`, Text `#92400E`
    - Submit Button: Background `#0641CC` (Sleman Navy) atau `#D97706` (Amber Security)

---

## 3. RINCIAN TASK IMPLEMENTASI (5 MODUL)

---

### TASK 1: Pop-up Konfirmasi "Sandi Baru" pada Data Staf

#### Lokasi Berkas:
- View: `resources/views/superadmin/staf/index.blade.php` (baris ~270)
- Script: `resources/js/pages/superadmin/staf-index.js`

#### Langkah 1.1: Ubah Tombol di Tabel Staf (Hapus `onsubmit="return confirm(...)"`)
Ganti form inline pada baris 270–277:
```blade
{{-- SEBELUM --}}
<form action="{{ route('superadmin.staf.regenerate-password', $staf) }}" method="POST" style="display: inline-block; margin: 0;" onsubmit="return confirm('Buat kata sandi sementara baru untuk akun staf ini? Sesi aktif staf akan ditutup dan staf wajib mengganti kata sandi saat login.');">
    @csrf
    <button type="submit" class="sa-btn-action-ghost" title="Buat kata sandi sementara baru secara instan">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
        Sandi Baru
    </button>
</form>
```
Menjadi tombol pemicu modal kustom (tanpa inline handler):
```blade
{{-- SESUDAH --}}
<button type="button" 
        class="sa-btn-action-ghost btn-trigger-sandi-baru" 
        title="Buat kata sandi sementara baru secara instan"
        data-id="{{ $staf->id }}"
        data-nama="{{ $staf->nama }}"
        data-email="{{ $staf->email }}"
        data-action-url="{{ route('superadmin.staf.regenerate-password', $staf) }}">
    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon></svg>
    Sandi Baru
</button>
```

#### Langkah 1.2: Tambahkan Markup Modal `#modalKonfirmasiSandiBaru` di Akhir Blade
Letakkan tepat sebelum `@push('scripts')`:
```blade
<!-- MODAL KONFIRMASI BUAT KATA SANDI SEMENTARA BARU (BEBAS WINDOW.CONFIRM) -->
<div id="modalKonfirmasiSandiBaru" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalSandiBaruTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"></polygon>
                    </svg>
                </div>
                <div>
                    <h3 id="modalSandiBaruTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                        Buat Kata Sandi Baru
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500, #64748B);">
                        Regenerasi Kredensial Akses Staf Internal (BR-15)
                    </span>
                </div>
            </div>
            <button type="button" data-modal-dismiss="modalKonfirmasiSandiBaru" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1; padding: 0.25rem;" title="Tutup dialog">&times;</button>
        </div>

        <form id="formSandiBaru" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            <div style="background: #FFFBEB; border: 1px solid #FDE68A; border-radius: 10px; padding: 1rem 1.25rem;">
                <p style="font-size: 0.875rem; color: #92400E; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin membuat kata sandi sementara baru untuk akun staf berikut?
                </p>
                <div style="background: rgba(255, 255, 255, 0.75); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(217, 119, 6, 0.2);">
                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;" id="sandiBaruNama">Nama Staf</div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600);" id="sandiBaruEmail">email@sleman.go.id</div>
                </div>
                <div style="margin-top: 0.65rem; font-size: 0.75rem; color: #B45309; line-height: 1.4;">
                    <strong>Konsekuensi Keamanan:</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                        <li>Sesi login aktif staf akan langsung diputus secara paksa.</li>
                        <li>Staf wajib melakukan penggantian kata sandi saat login berikutnya.</li>
                    </ul>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalKonfirmasiSandiBaru" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: #0641CC; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span>Ya, Buat Sandi Sementara</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

#### Langkah 1.3: Tambahkan Handler di `staf-index.js`
Di berkas `resources/js/pages/superadmin/staf-index.js`, tangkap klik `.btn-trigger-sandi-baru` dan daftarkan ke penutupan modal:
```javascript
// Trigger Buka Modal Konfirmasi Sandi Baru
const triggerSandiBaru = event.target.closest('.btn-trigger-sandi-baru');
if (triggerSandiBaru) {
    event.preventDefault();
    const nama = triggerSandiBaru.dataset.nama || 'Staf';
    const email = triggerSandiBaru.dataset.email || '-';
    const actionUrl = triggerSandiBaru.dataset.actionUrl;

    const modalSandiBaru = document.getElementById('modalKonfirmasiSandiBaru');
    const formSandiBaru = document.getElementById('formSandiBaru');
    const namaEl = document.getElementById('sandiBaruNama');
    const emailEl = document.getElementById('sandiBaruEmail');

    if (formSandiBaru && actionUrl) formSandiBaru.action = actionUrl;
    if (namaEl) namaEl.textContent = nama;
    if (emailEl) emailEl.textContent = email;
    if (modalSandiBaru) modalSandiBaru.style.display = 'flex';
    return;
}
```
*Pastikan `#modalKonfirmasiSandiBaru` juga ditutup saat backdrop diklik atau tombol `Escape` ditekan!*

---

### TASK 2: Pop-up Konfirmasi "Toggle Status Aktif/Nonaktif" pada Master Bidang Dinas Kominfo

#### Lokasi Berkas:
- View: `resources/views/superadmin/bidang/index.blade.php` (baris ~188)
- Script: `resources/js/pages/superadmin/bidang-index.js`

#### Langkah 2.1: Ubah Tombol di Tabel Bidang
Ganti tombol di baris 188–194:
```blade
{{-- SEBELUM --}}
<form action="{{ route('superadmin.bidang.toggle', $bidang) }}" method="POST" style="display: inline-block; margin: 0;">
    @csrf
    @method('PATCH')
    <button type="submit" onclick="return confirm('{{ $bidang->aktif ? 'Nonaktifkan bidang ini?' : 'Aktifkan kembali bidang ini?' }}')" class="{{ $bidang->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $bidang->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
        {{ $bidang->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
    </button>
</form>
```
Menjadi tombol pemicu modal kustom:
```blade
{{-- SESUDAH --}}
<button type="button" 
        class="btn-trigger-toggle-bidang {{ $bidang->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
        style="{{ $bidang->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}"
        data-id="{{ $bidang->id }}"
        data-nama="{{ $bidang->nama_bidang }}"
        data-status-aktif="{{ $bidang->aktif ? '1' : '0' }}"
        data-action-url="{{ route('superadmin.bidang.toggle', $bidang) }}">
    {{ $bidang->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
</button>
```

#### Langkah 2.2: Tambahkan Markup Modal `#modalKonfirmasiToggleBidang` di Akhir Blade
Letakkan tepat sebelum `@push('scripts')`:
```blade
<!-- MODAL KONFIRMASI STATUS BIDANG DINAS -->
<div id="modalKonfirmasiToggleBidang" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleBidangTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleBidangIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                        <line x1="12" y1="9" x2="12" y2="13"></line>
                        <line x1="12" y1="17" x2="12.01" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleBidangTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Konfirmasi Status Bidang
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500);">
                        Master Bidang Unit Kerja Dinas Kominfo
                    </span>
                </div>
            </div>
            <button type="button" data-modal-dismiss="modalKonfirmasiToggleBidang" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1; padding: 0.25rem;" title="Tutup dialog">&times;</button>
        </div>

        <form id="formToggleBidang" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')
            <div id="toggleBidangBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleBidangMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin menonaktifkan bidang dinas ini?
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;" id="toggleBidangNama">Nama Bidang</div>
                </div>
                <p id="toggleBidangConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    • Bidang yang dinonaktifkan tidak akan muncul pada pilihan pendaftaran magang baru maupun penugasan pembimbing.
                </p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalKonfirmasiToggleBidang" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitToggleBidang" style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer;">
                    <span id="btnSubmitToggleBidangText">Ya, Nonaktifkan Bidang</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

#### Langkah 2.3: Tambahkan Handler di `bidang-index.js`
Buka `resources/js/pages/superadmin/bidang-index.js`, tambahkan penanganan klik `.btn-trigger-toggle-bidang`:
```javascript
// Trigger Buka Modal Konfirmasi Toggle Bidang
const triggerToggle = event.target.closest('.btn-trigger-toggle-bidang');
if (triggerToggle) {
    event.preventDefault();
    const nama = triggerToggle.dataset.nama || 'Bidang';
    const isAktif = triggerToggle.dataset.statusAktif === '1';
    const actionUrl = triggerToggle.dataset.actionUrl;

    const modalToggle = document.getElementById('modalKonfirmasiToggleBidang');
    const formToggle = document.getElementById('formToggleBidang');
    const namaEl = document.getElementById('toggleBidangNama');
    const titleEl = document.getElementById('modalToggleBidangTitle');
    const msgEl = document.getElementById('toggleBidangMessage');
    const consequenceEl = document.getElementById('toggleBidangConsequence');
    const boxAlert = document.getElementById('toggleBidangBoxAlert');
    const iconWrap = document.getElementById('toggleBidangIconWrap');
    const submitBtn = document.getElementById('btnSubmitToggleBidang');
    const submitText = document.getElementById('btnSubmitToggleBidangText');

    if (formToggle && actionUrl) formToggle.action = actionUrl;
    if (namaEl) namaEl.textContent = nama;

    if (isAktif) {
        if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Bidang';
        if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan bidang dinas ini?';
        if (consequenceEl) consequenceEl.textContent = '• Bidang yang dinonaktifkan tidak akan muncul pada pilihan pendaftaran magang baru maupun penugasan pembimbing.';
        if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
        if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
        if (submitBtn) submitBtn.style.background = '#DC2626';
        if (submitText) submitText.textContent = 'Ya, Nonaktifkan Bidang';
    } else {
        if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Bidang';
        if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali bidang dinas ini?';
        if (consequenceEl) consequenceEl.textContent = '• Bidang akan kembali tersedia untuk dipilih pada pendaftaran peserta dan penempatan operasional.';
        if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
        if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
        if (submitBtn) submitBtn.style.background = '#16A34A';
        if (submitText) submitText.textContent = 'Ya, Aktifkan Bidang';
    }

    if (modalToggle) modalToggle.style.display = 'flex';
    return;
}
```

---

### TASK 3: Pop-up Konfirmasi "Toggle Status Aktif/Nonaktif" pada Manajemen Slot Khusus Internal

#### Lokasi Berkas:
- View: `resources/views/superadmin/slot-khusus/index.blade.php` (baris ~238 & ~410)
- Script: `resources/js/pages/superadmin/slot-khusus.js`

#### Langkah 3.1: Ubah Tombol di Tabel Slot Khusus & Rapikan Modal Tambah
1. Di baris 238–244, ganti:
```blade
{{-- SEBELUM --}}
<form action="{{ route('superadmin.slot-khusus.toggle', $slot) }}" method="POST" style="display: inline-block; margin: 0;">
    @csrf
    @method('PATCH')
    <button type="submit" onclick="return confirm('{{ $slot->aktif ? 'Nonaktifkan slot khusus ini?' : 'Aktifkan kembali slot khusus ini?' }}')" class="{{ $slot->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $slot->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.35);' }}">
        {{ $slot->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
    </button>
</form>
```
Menjadi:
```blade
{{-- SESUDAH --}}
<button type="button" 
        class="btn-trigger-toggle-slot {{ $slot->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
        style="{{ $slot->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.35);' }}"
        data-id="{{ $slot->id }}"
        data-instansi="{{ $slot->nama_instansi }}"
        data-jumlah="{{ $slot->jumlah }}"
        data-status-aktif="{{ $slot->aktif ? '1' : '0' }}"
        data-action-url="{{ route('superadmin.slot-khusus.toggle', $slot) }}">
    {{ $slot->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
</button>
```
2. Ganti atribut inline `onclick="document.getElementById('modalTambahSlot').style.display='none'"` pada tombol X dan Batal di `modalTambahSlot` (baris 414 & 449) menjadi: `data-modal-dismiss="modalTambahSlot"`.

#### Langkah 3.2: Tambahkan Markup Modal `#modalKonfirmasiToggleSlot` di Akhir Blade
```blade
<!-- MODAL KONFIRMASI STATUS SLOT KHUSUS INTERNAL -->
<div id="modalKonfirmasiToggleSlot" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleSlotTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleSlotIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleSlotTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Konfirmasi Status Slot Khusus
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500);">
                        Slot Magang Kemitraan & Penugasan Khusus
                    </span>
                </div>
            </div>
            <button type="button" data-modal-dismiss="modalKonfirmasiToggleSlot" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1; padding: 0.25rem;" title="Tutup dialog">&times;</button>
        </div>

        <form id="formToggleSlot" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')
            <div id="toggleSlotBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleSlotMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin menonaktifkan alokasi slot khusus ini?
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;" id="toggleSlotInstansi">Instansi Mitra</div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600);" id="toggleSlotDetail">Alokasi: 0 Kuota</div>
                </div>
                <p id="toggleSlotConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    • Slot yang dinonaktifkan tidak dapat digunakan untuk input penempatan peserta jalur kemitraan baru.
                </p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalKonfirmasiToggleSlot" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitToggleSlot" style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer;">
                    <span id="btnSubmitToggleSlotText">Ya, Nonaktifkan Slot</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

#### Langkah 3.3: Perbarui `slot-khusus.js`
Buka `resources/js/pages/superadmin/slot-khusus.js`. Tambahkan modal listener dan handler toggle slot:
```javascript
// Tambahkan modal opening/closing dan trigger toggle slot ke dalam event listener klik:
document.addEventListener('click', (event) => {
    // 1. Pemicu Buka Modal Tambah Slot
    const triggerTambah = event.target.closest('#btnOpenTambahSlot') || event.target.closest('.btn-trigger-tambah-slot');
    if (triggerTambah) {
        event.preventDefault();
        const m = document.getElementById('modalTambahSlot');
        if (m) m.style.display = 'flex';
        return;
    }

    // 2. Pemicu Buka Modal Konfirmasi Toggle Slot
    const triggerToggle = event.target.closest('.btn-trigger-toggle-slot');
    if (triggerToggle) {
        event.preventDefault();
        const instansi = triggerToggle.dataset.instansi || 'Instansi Mitra';
        const jumlah = triggerToggle.dataset.jumlah || '0';
        const isAktif = triggerToggle.dataset.statusAktif === '1';
        const actionUrl = triggerToggle.dataset.actionUrl;

        const modalToggle = document.getElementById('modalKonfirmasiToggleSlot');
        const formToggle = document.getElementById('formToggleSlot');
        const instansiEl = document.getElementById('toggleSlotInstansi');
        const detailEl = document.getElementById('toggleSlotDetail');
        const titleEl = document.getElementById('modalToggleSlotTitle');
        const msgEl = document.getElementById('toggleSlotMessage');
        const consequenceEl = document.getElementById('toggleSlotConsequence');
        const boxAlert = document.getElementById('toggleSlotBoxAlert');
        const iconWrap = document.getElementById('toggleSlotIconWrap');
        const submitBtn = document.getElementById('btnSubmitToggleSlot');
        const submitText = document.getElementById('btnSubmitToggleSlotText');

        if (formToggle && actionUrl) formToggle.action = actionUrl;
        if (instansiEl) instansiEl.textContent = instansi;
        if (detailEl) detailEl.textContent = `Alokasi Kuota: ${jumlah} Kursi`;

        if (isAktif) {
            if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Slot';
            if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan alokasi slot khusus ini?';
            if (consequenceEl) consequenceEl.textContent = '• Slot yang dinonaktifkan tidak dapat digunakan untuk pembuatan akun peserta jalur kemitraan baru.';
            if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
            if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
            if (submitBtn) submitBtn.style.background = '#DC2626';
            if (submitText) submitText.textContent = 'Ya, Nonaktifkan Slot';
        } else {
            if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Slot';
            if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali slot khusus ini?';
            if (consequenceEl) consequenceEl.textContent = '• Alokasi kuota mitra ini akan kembali aktif dan siap menerima penempatan peserta khusus.';
            if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
            if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
            if (submitBtn) submitBtn.style.background = '#16A34A';
            if (submitText) submitText.textContent = 'Ya, Aktifkan Slot';
        }

        if (modalToggle) modalToggle.style.display = 'flex';
        return;
    }

    // 3. Pemicu Tutup Modal via data-modal-dismiss
    const dismissBtn = event.target.closest('[data-modal-dismiss]');
    if (dismissBtn) {
        event.preventDefault();
        const targetId = dismissBtn.dataset.modalDismiss;
        const targetModal = document.getElementById(targetId);
        if (targetModal) targetModal.style.display = 'none';
        return;
    }

    // 4. Klik Backdrop
    const modalTambah = document.getElementById('modalTambahSlot');
    const modalToggleSlot = document.getElementById('modalKonfirmasiToggleSlot');
    if (event.target === modalTambah) modalTambah.style.display = 'none';
    if (event.target === modalToggleSlot) modalToggleSlot.style.display = 'none';
});

// ESC Key Listener
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        const m1 = document.getElementById('modalTambahSlot');
        const m2 = document.getElementById('modalKonfirmasiToggleSlot');
        if (m1) m1.style.display = 'none';
        if (m2) m2.style.display = 'none';
    }
});
```

---

### TASK 4: Pop-up Konfirmasi "Toggle Status Aktif/Nonaktif" pada Manajemen Bulan Magang

#### Lokasi Berkas:
- View: `resources/views/superadmin/bulan/index.blade.php` (baris ~201)
- Script: `resources/js/pages/superadmin/bulan-index.js`

#### Langkah 4.1: Ubah Tombol di Tabel Bulan Magang
Ganti:
```blade
{{-- SEBELUM --}}
<form method="POST" action="{{ route('superadmin.bulan.toggle', $bulan) }}" style="display: inline-block; margin: 0;" onsubmit="return confirm('Ubah status aktif bulan magang ini?');">
    @csrf
    @method('PATCH')
    <button type="submit" class="{{ $bulan->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" style="{{ $bulan->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}">
        {{ $bulan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
    </button>
</form>
```
Menjadi:
```blade
{{-- SESUDAH --}}
<button type="button" 
        class="btn-trigger-toggle-bulan {{ $bulan->aktif ? 'sa-btn-action-danger' : 'sa-btn-action-outline' }}" 
        style="{{ $bulan->aktif ? '' : 'color: var(--green-700); border-color: rgba(22, 163, 74, 0.25);' }}"
        data-id="{{ $bulan->id }}"
        data-nama="{{ $bulan->nama_bulan ?? $bulan->bulan_mulai?->format('F Y') }}"
        data-status-aktif="{{ $bulan->aktif ? '1' : '0' }}"
        data-action-url="{{ route('superadmin.bulan.toggle', $bulan) }}">
    {{ $bulan->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
</button>
```

#### Langkah 4.2: Tambahkan Markup Modal `#modalKonfirmasiToggleBulan` di Akhir Blade
```blade
<!-- MODAL KONFIRMASI STATUS BULAN MAGANG -->
<div id="modalKonfirmasiToggleBulan" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalToggleBulanTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid var(--neutral-300, #CBD5E1);">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div id="toggleBulanIconWrap" style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalToggleBulanTitle" style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Ubah Status Bulan Magang
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500);">
                        Pengendalian Gelombang Pendaftaran Peserta
                    </span>
                </div>
            </div>
            <button type="button" data-modal-dismiss="modalKonfirmasiToggleBulan" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1; padding: 0.25rem;" title="Tutup dialog">&times;</button>
        </div>

        <form id="formToggleBulan" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('PATCH')
            <div id="toggleBulanBoxAlert" style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p id="toggleBulanMessage" style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Apakah Anda yakin ingin menonaktifkan periode bulan magang ini?
                </p>
                <div style="background: rgba(255, 255, 255, 0.7); border-radius: 6px; padding: 0.5rem 0.75rem; border: 1px solid rgba(220, 38, 38, 0.15);">
                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.875rem;" id="toggleBulanNama">Periode Bulan</div>
                </div>
                <p id="toggleBulanConsequence" style="font-size: 0.75rem; color: #BE123C; margin: 0.65rem 0 0 0; line-height: 1.4;">
                    • Menonaktifkan bulan ini akan menutup akses pendaftaran calon peserta baru pada siklus terkait.
                </p>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalKonfirmasiToggleBulan" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" id="btnSubmitToggleBulan" style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer;">
                    <span id="btnSubmitToggleBulanText">Ya, Nonaktifkan Bulan</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

#### Langkah 4.3: Perbarui `bulan-index.js`
Buka `resources/js/pages/superadmin/bulan-index.js`. Tambahkan penanganan klik `.btn-trigger-toggle-bulan` dan daftarkan ke penutupan modal:
```javascript
// Trigger Buka Modal Konfirmasi Toggle Bulan Magang
const triggerToggle = event.target.closest('.btn-trigger-toggle-bulan');
if (triggerToggle) {
    event.preventDefault();
    const nama = triggerToggle.dataset.nama || 'Periode Bulan';
    const isAktif = triggerToggle.dataset.statusAktif === '1';
    const actionUrl = triggerToggle.dataset.actionUrl;

    const modalToggle = document.getElementById('modalKonfirmasiToggleBulan');
    const formToggle = document.getElementById('formToggleBulan');
    const namaEl = document.getElementById('toggleBulanNama');
    const titleEl = document.getElementById('modalToggleBulanTitle');
    const msgEl = document.getElementById('toggleBulanMessage');
    const consequenceEl = document.getElementById('toggleBulanConsequence');
    const boxAlert = document.getElementById('toggleBulanBoxAlert');
    const iconWrap = document.getElementById('toggleBulanIconWrap');
    const submitBtn = document.getElementById('btnSubmitToggleBulan');
    const submitText = document.getElementById('btnSubmitToggleBulanText');

    if (formToggle && actionUrl) formToggle.action = actionUrl;
    if (namaEl) namaEl.textContent = nama;

    if (isAktif) {
        if (titleEl) titleEl.textContent = 'Konfirmasi Penonaktifan Bulan';
        if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin menonaktifkan periode bulan magang ini?';
        if (consequenceEl) consequenceEl.textContent = '• Menonaktifkan bulan ini akan menutup akses pendaftaran calon peserta baru pada siklus terkait.';
        if (boxAlert) { boxAlert.style.background = '#FFF1F2'; boxAlert.style.borderColor = '#FECDD3'; }
        if (iconWrap) { iconWrap.style.background = '#FEE2E2'; iconWrap.style.color = '#DC2626'; }
        if (submitBtn) submitBtn.style.background = '#DC2626';
        if (submitText) submitText.textContent = 'Ya, Nonaktifkan Bulan';
    } else {
        if (titleEl) titleEl.textContent = 'Konfirmasi Pengaktifan Bulan';
        if (msgEl) msgEl.textContent = 'Apakah Anda yakin ingin mengaktifkan kembali periode bulan magang ini?';
        if (consequenceEl) consequenceEl.textContent = '• Periode bulan ini akan aktif dan siap menerima pengajuan berkas pendaftaran peserta.';
        if (boxAlert) { boxAlert.style.background = '#F0FDF4'; boxAlert.style.borderColor = '#BBF7D0'; }
        if (iconWrap) { iconWrap.style.background = '#DCFCE7'; iconWrap.style.color = '#16A34A'; }
        if (submitBtn) submitBtn.style.background = '#16A34A';
        if (submitText) submitText.textContent = 'Ya, Aktifkan Bulan';
    }

    if (modalToggle) modalToggle.style.display = 'flex';
    return;
}
```

---

### TASK 5: Pop-up Konfirmasi "Hapus Berkas Snapshot" pada Pusat Cadangan & Pemulihan Basis Data

#### Lokasi Berkas:
- View: `resources/views/superadmin/backup/index.blade.php` (baris ~196 & ~224)
- Script Baru: `resources/js/pages/superadmin/backup-index.js` (BUAT BARU)
- Bundler Config: `vite.config.js`

#### Masalah Khusus di Modul Backup:
Di berkas `backup/index.blade.php` baris 224, terdapat salah tautan:
`@vite(['resources/js/pages/superadmin/staf-index.js'])`! Modul backup secara keliru memuat JS milik staf. Kita harus membuat berkas `resources/js/pages/superadmin/backup-index.js` tersendiri.

#### Langkah 5.1: Ubah Tombol Hapus di Tabel Backup
Di baris 196–203 `resources/views/superadmin/backup/index.blade.php`, ganti:
```blade
{{-- SEBELUM --}}
<form method="POST" action="{{ route('superadmin.backup.destroy', $item['filename']) }}" style="display: inline-block;" onsubmit="return confirm('Hapus berkas snapshot ini secara permanen?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="sa-btn-action-danger">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
        <span>Hapus</span>
    </button>
</form>
```
Menjadi tombol pemicu modal konfirmasi:
```blade
{{-- SESUDAH --}}
<button type="button" 
        class="sa-btn-action-danger btn-trigger-delete-backup" 
        data-filename="{{ $item['filename'] }}"
        data-size="{{ $item['size_mb'] }}"
        data-created="{{ $item['created_at'] }}"
        data-action-url="{{ route('superadmin.backup.destroy', $item['filename']) }}">
    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
    <span>Hapus</span>
</button>
```

#### Langkah 5.2: Tambahkan Markup Modal `#modalKonfirmasiHapusBackup` di Akhir Blade Backup
Tambahkan sebelum `@push('scripts')`:
```blade
<!-- MODAL KONFIRMASI HAPUS SNAPSHOT CADANGAN BASIS DATA (HIGH DANGER) -->
<div id="modalKonfirmasiHapusBackup" 
     style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.65); backdrop-filter: blur(4px); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;"
     role="dialog" 
     aria-modal="true" 
     aria-labelledby="modalHapusBackupTitle">
    <div style="background: var(--neutral-0, #FFFFFF); border-radius: 16px; width: 100%; max-width: 480px; padding: 2rem; box-shadow: var(--shadow-modal); position: relative; border: 1px solid #FECDD3;">
        
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="width: 42px; height: 42px; border-radius: 10px; background: #FEE2E2; color: #DC2626; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <polyline points="3 6 5 6 21 6"></polyline>
                        <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                        <line x1="10" y1="11" x2="10" y2="17"></line>
                        <line x1="14" y1="11" x2="14" y2="17"></line>
                    </svg>
                </div>
                <div>
                    <h3 id="modalHapusBackupTitle" style="font-size: 1.125rem; font-weight: 800; color: #991B1B; margin: 0;">
                        Hapus Snapshot Basis Data?
                    </h3>
                    <span style="font-size: 0.8125rem; color: var(--neutral-500);">
                        Pusat Cadangan & Pemulihan Sistem (Disaster Recovery)
                    </span>
                </div>
            </div>
            <button type="button" data-modal-dismiss="modalKonfirmasiHapusBackup" style="background: none; border: none; font-size: 1.5rem; cursor: pointer; color: var(--neutral-400); line-height: 1; padding: 0.25rem;" title="Tutup dialog">&times;</button>
        </div>

        <form id="formHapusBackup" method="POST" style="display: flex; flex-direction: column; gap: 1.25rem; margin: 0;">
            @csrf
            @method('DELETE')
            <div style="background: #FFF1F2; border: 1px solid #FECDD3; border-radius: 10px; padding: 1rem 1.25rem;">
                <p style="font-size: 0.875rem; color: #9F1239; margin: 0 0 0.5rem 0; line-height: 1.5;">
                    Anda akan menghapus berkas snapshot arsip database berikut secara permanen dari server:
                </p>
                <div style="background: #FFFFFF; border-radius: 6px; padding: 0.65rem 0.85rem; border: 1px solid rgba(225, 29, 72, 0.2); font-family: monospace;">
                    <div style="font-weight: 700; color: #881337; font-size: 0.8125rem; word-break: break-all;" id="hapusBackupFilename">backup-2026.sql.gz</div>
                    <div style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; font-family: inherit;" id="hapusBackupMeta">Ukuran: 0 MB &bull; Waktu: -</div>
                </div>
                <div style="margin-top: 0.75rem; font-size: 0.75rem; color: #BE123C; line-height: 1.4;">
                    <strong>PERINGATAN PEMULIHAN SISTEM:</strong>
                    <ul style="margin: 0.25rem 0 0 1rem; padding: 0;">
                        <li>Berkas ini tidak dapat dipulihkan kembali setelah dihapus.</li>
                        <li>Pastikan Anda telah memiliki salinan cadangan sekunder di media penyimpanan eksternal yang aman.</li>
                    </ul>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" data-modal-dismiss="modalKonfirmasiHapusBackup" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: var(--neutral-700); cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: #DC2626; border: none; border-radius: 8px; font-weight: 700; font-size: 0.875rem; color: #FFFFFF; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                    <span>Ya, Hapus Snapshot Permanen</span>
                </button>
            </div>
        </form>
    </div>
</div>
```

#### Langkah 5.3: Buat Berkas `resources/js/pages/superadmin/backup-index.js`
Buat berkas baru ini dengan kode bersih standar:
```javascript
/**
 * Modul Pusat Cadangan & Pemulihan Basis Data SuperAdmin
 * - Penanganan modal konfirmasi hapus snapshot database
 * - Peringatan privasi UU PDP & Disaster Recovery
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();

    const modalHapus = document.getElementById('modalKonfirmasiHapusBackup');
    const formHapus = document.getElementById('formHapusBackup');
    const filenameEl = document.getElementById('hapusBackupFilename');
    const metaEl = document.getElementById('hapusBackupMeta');

    document.addEventListener('click', (event) => {
        // Trigger Buka Modal Hapus Snapshot
        const triggerDelete = event.target.closest('.btn-trigger-delete-backup');
        if (triggerDelete) {
            event.preventDefault();
            const filename = triggerDelete.dataset.filename || 'snapshot.sql.gz';
            const size = triggerDelete.dataset.size || '0';
            const created = triggerDelete.dataset.created || '-';
            const actionUrl = triggerDelete.dataset.actionUrl;

            if (formHapus && actionUrl) formHapus.action = actionUrl;
            if (filenameEl) filenameEl.textContent = filename;
            if (metaEl) metaEl.textContent = `Ukuran: ${size} MB • Dibuat: ${created}`;
            if (modalHapus) modalHapus.style.display = 'flex';
            return;
        }

        // Trigger Tutup Modal via data-modal-dismiss
        const dismissBtn = event.target.closest('[data-modal-dismiss]');
        if (dismissBtn) {
            event.preventDefault();
            const targetId = dismissBtn.dataset.modalDismiss;
            const targetModal = document.getElementById(targetId);
            if (targetModal) targetModal.style.display = 'none';
            return;
        }

        // Klik Backdrop
        if (event.target === modalHapus) {
            modalHapus.style.display = 'none';
        }
    });

    // Keyboard ESC
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            if (modalHapus) modalHapus.style.display = 'none';
        }
    });
});
```

#### Langkah 5.4: Daftarkan ke `vite.config.js` & Perbaiki Blade
1. Buka `vite.config.js`, tambahkan baris `'resources/js/pages/superadmin/backup-index.js',` pada array `input: [...]`.
2. Di baris 224 `resources/views/superadmin/backup/index.blade.php`, ubah:
   `@vite(['resources/js/pages/superadmin/staf-index.js'])`
   menjadi:
   `@vite(['resources/js/pages/superadmin/backup-index.js'])`.

---

## 4. URUTAN EKSEKUSI & CHECKLIST UNTUK JUNIOR DEVELOPER / AI

Jalankan pekerjaan secara berurutan agar terhindar dari regresi:

```
[Task 1: Sandi Baru Staf] ──> [Task 2: Master Bidang] ──> [Task 3: Slot Khusus] ──> [Task 4: Bulan Magang] ──> [Task 5: Backup DB]
```

### Checklist Verifikasi Setiap Modul:
- [ ] Atribut inline `onclick="return confirm(...)"` dan `onsubmit="return confirm(...)"` sudah dibersihkan total.
- [ ] Tombol pemicu memiliki class `btn-trigger-*` dan atribut HTML5 `data-*` yang lengkap.
- [ ] Modal dialog memiliki `role="dialog"`, `aria-modal="true"`, dan tombol dismiss dengan `data-modal-dismiss`.
- [ ] Form submit di dalam modal memuat token `@csrf` dan directive `@method(...)` yang sesuai (`PATCH`/`DELETE`).
- [ ] Modal dapat ditutup dengan 4 cara:
  1. Tombol "Batal"
  2. Tombol silang "&times;"
  3. Mengklik area luar (backdrop)
  4. Menekan tombol `Escape` pada keyboard.
- [ ] Tidak ada error di browser console saat tombol diklik.

---

## 5. QUALITY GATES & COMMAND VERIFIKASI AKHIR

Setelah menyelesaikan seluruh langkah di atas, jalankan perintah uji verifikasi berikut:

### 1. Verifikasi Tidak Ada Lagi `confirm(` di Seluruh Views SuperAdmin:
Jalankan di terminal:
```bash
git grep "confirm(" resources/views/superadmin/
```
*Hasil yang diharapkan: KOSONG / Zero matches.*

### 2. Format & Standar Kode:
```bash
./vendor/bin/pint --test
```

### 3. Static Code Analysis:
```bash
./vendor/bin/phpstan analyse --memory-limit=1G
```

### 4. Build Aset Frontend Vite:
```bash
npm run build
```
*Pastikan tidak ada error import atau missing entry point pada Vite build output.*

### 5. Automated Feature Test:
```bash
php artisan test --filter=SuperAdmin
```

---

## 6. DEFINITION OF DONE (DoD)

Task ini dinyatakan **SELESAI & LULUS REVIEW** jika:
1. Tidak ada satupun native popup `localhost:8000 says ...` yang muncul saat menguji kelima fitur di atas.
2. Seluruh form request berhasil dieksekusi ke backend controller dengan status HTTP 302 redirect dan pesan toast sukses.
3. Kode Blade tetap murni markup (bebas tag `<script>` dan styling inline acak).
4. `npm run build` sukses tanpa warning atau error.
