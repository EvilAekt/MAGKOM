# 📋 SENIOR DEVELOPER TECHNICAL DIRECTIVE & PRD
## Task: Perbaikan Logika Pemilihan Tanggal Kalender (Instant Date Selection & Reactive Input Commit) pada Form Tugas Pembimbing

- **Author**: Senior Lead Developer (Antigravity Architecture Team)
- **Assignee**: Junior Developer / AI Model (Gemini 3.8 Flash / Autonomous Code Agent)
- **Dokumen Referensi Bukti**: `media_1794563232869_4a80f2b5.png` (Pengguna Mengklik Tanggal 20 Nov namun Form Tidak Otomatis Terpilih)
- **Target File yang Perlu Dimodifikasi**:
  1. [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js) (Logika Pemilihan Tanggal Tenggat & Jadwal Publikasi)
  2. [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js) (Logika Pemilihan Tanggal Form Edit Tugas)
- **Status Dokumen**: ⏳ **DRAFT PLAN (Menunggu Persetujuan User Sebelum Eksekusi)**
- **Tanggal**: 13 November 2026
- **Klasifikasi**: Usability Defect / Event Handling & State Synchronization

---

## 🎯 1. Ringkasan Eksekutif (TL;DR 60 Detik untuk Junior Developer / AI)

| Parameter | Keterangan Teknis |
|---|---|
| **Gejala Masalah (User Report)** | Pengguna mengklik tanggal pada kalender (contoh: tanggal 20 November 2026), lingkaran biru aktif berpindah, namun **tanggal tersebut tidak otomatis terpilih pada input form**, tulisan di tombol trigger tidak berubah, dan popup tidak menutup. Pengguna merasa kalender "macet" atau "rusak". |
| **Akar Masalah (RCA)** | Event listener click pada tombol sel kalender (`.tugas-cal-date-btn`) hanya memperbarui variabel lokal JavaScript `selectedDate = dateObj;` dan memanggil `renderCalendar()`. Kode **TIDAK memperbarui** `hiddenInput.value`, **TIDAK memperbarui** teks `displayText.textContent`, dan **TIDAK menutup popup**. Pembaruan input form baru dijalankan jika pengguna menekan tombol "Oke" (`#calBtnOk`) di footer popup, yang seringkali tersembunyi atau terpotong di layar laptop. |
| **Solusi yang Dibutuhkan** | Terapkan standar industri modern (**Instant One-Click Date Selection**): Saat tombol tanggal diklik, seketika itu juga nilai `hiddenInput.value` di-set ke format `YYYY-MM-DD`, event `change` & `input` di-dispatch, teks pada tombol trigger diperbarui ke bahasa Indonesia (contoh: `Jumat, 20 Nov 2026`), dan popup menutup otomatis setelah jeda visual halus (*micro-delay 180ms*). |
| **Cakupan File** | 2 file JavaScript: [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js) dan [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js). Tidak memerlukan perubahan schema database, route, atau backend controller. |
| **Estimasi Eksekusi** | ~5 menit (Modifikasi event handler + kompilasi `npm run build` + automated test). |

---

## 🔬 2. Deep Root-Cause Analysis (RCA)

Mari kita bedah alur eksekusi saat ini pada [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js):

### 2.1 Kondisi Cacat Saat Ini
```javascript
// Baris ~815 pada tugas-create.js
btn.addEventListener('click', () => {
    selectedDate = dateObj; // ❌ HANYA MENGUBAH VARIABEL IN-MEMORY
    renderCalendar();       // ❌ HANYA MERENDER ULANG TAMPILAN BULAT BIRU
    // TIDAK ADA KOMIT KE hiddenInput!
    // TIDAK ADA KOMIT KE displayText!
    // POPUP TIDAK MENUTUP!
});
```

Pembaruan form justru ditaruh di tombol "Oke" (`#calBtnOk`):
```javascript
// Baris ~754 pada tugas-create.js
if (btnOk) {
    btnOk.addEventListener('click', () => {
        if (selectedDate && hiddenInput) {
            const yyyy = selectedDate.getFullYear();
            const mm = String(selectedDate.getMonth() + 1).padStart(2, '0');
            const dd = String(selectedDate.getDate()).padStart(2, '0');
            hiddenInput.value = `${yyyy}-${mm}-${dd}`; // Komit baru terjadi di sini!

            if (displayText) {
                const dayName = daysIndo[selectedDate.getDay()];
                const mName = months[selectedDate.getMonth()];
                displayText.textContent = `${dayName}, ${dd} ${mName} ${yyyy}`;
            }
        }
        popup.classList.remove('is-active');
    });
}
```

### 2.2 Dampak Buruk terhadap UX
1. **Ekspektasi Pengguna**: Pada datepicker modern (seperti Google Calendar, Airbnb, Flatpickr, browser native datepicker), memilih tanggal dilakukan dengan **1 kali klik**. Begitu tanggal disentuh/diklik, formulir langsung mencatat tanggal tersebut.
2. **Data Hilang jika Pengguna Klik di Luar**: Jika pengguna mengklik tanggal 20, lalu mengklik area kosong lain untuk menutup kalender, event listener dokumen `document.addEventListener('click')` langsung membuang pemilihan tanggal karena tombol "Oke" belum ditekan!
3. **Footer Terpotong**: Di monitor dengan resolusi atau zoom tertentu, tombol "Oke" di bagian bawah kalender bisa tertutup atau berada di luar area pandang, sehingga pengguna terjebak tidak bisa mengonfirmasi tanggal.

---

## 📐 3. Diagram Alur Logika (Before vs After)

```mermaid
sequenceDiagram
    autonumber
    actor User as Pengguna (Pembimbing)
    participant UI as Tombol Tanggal (Cell 20 Nov)
    participant JS as Event Handler (JS)
    participant Input as Hidden Input (#realDeadlineInput)
    participant Label as Display Text (#txtSelectedDeadlineDisplay)
    participant Popup as Modal Popup (.tugas-calendar-popup)

    Note over User, Popup: ❌ ALUR LAMA (BERMASALAH - MEMBINGUNGKAN PENGGUNA)
    User->>UI: Klik tanggal 20 Nov
    UI->>JS: Trigger click
    JS->>JS: selectedDate = dateObj; renderCalendar();
    Note right of JS: Form TIDAK diperbarui! Pengguna bingung kenapa belum terpilih!
    User->>User: Bingung / Klik di luar kalender
    JS->>Popup: Tutup popup (State pemilihan HILANG!)

    Note over User, Popup: ✅ ALUR BARU (INSTANT ONE-CLICK SELECTION)
    User->>UI: Klik tanggal 20 Nov
    UI->>JS: Trigger click
    JS->>Input: hiddenInput.value = "2026-11-20" + dispatchEvent('change')
    JS->>Label: displayText.textContent = "Jumat, 20 Nov 2026"
    JS->>UI: Tambah class .is-selected (Visual feedback instant)
    JS-->>Popup: Tunggu 180ms (micro-delay animasi)
    JS->>Popup: popup.classList.remove('is-active') (Otomatis Menutup!)
    Note over User, Popup: Tanggal 100% tersimpan aman & form terisi sempurna!
```

---

## 🛠️ 4. Spesifikasi Perubahan File (File Modification Table)

| No | File Target | Aksi | Ringkasan Modifikasi Teknis |
| :---: | :--- | :---: | :--- |
| 1 | [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js) | `Modify` | Pada fungsi `initDeadlineCalendar()` dan `initJadwalCalendar()`, ubah event listener klik tanggal agar langsung meng-commit nilai ke `hiddenInput`, memperbarui teks display label, memicu event `change`, dan menutup popup secara otomatis setelah 180ms. |
| 2 | [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js) | `Modify` | Pada fungsi `initDeadlineCalendar()`, terapkan logika commit seketika (*instant commit*) yang identik. |

---

## 📝 5. Langkah-Langkah Implementasi Detail (Actionable Checklist)

### Fase 1: Perbaikan Logika Kalender pada `tugas-create.js`
- [ ] **1.1. Ekstraksi Fungsi Pembantu `commitDateSelection()`**:
  Di dalam fungsi `initDeadlineCalendar()` pada [`resources/js/pages/pembimbing/tugas-create.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-create.js):
  Buat fungsi terpusat untuk menyimpan tanggal ke DOM formulir:
  ```javascript
  function commitDateSelection(dateObj, shouldClose = true) {
      if (!dateObj) return;
      selectedDate = dateObj;

      const yyyy = dateObj.getFullYear();
      const mm = String(dateObj.getMonth() + 1).padStart(2, '0');
      const dd = String(dateObj.getDate()).padStart(2, '0');
      const isoDate = `${yyyy}-${mm}-${dd}`;

      if (hiddenInput) {
          hiddenInput.value = isoDate;
          hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
          hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
      }

      if (displayText) {
          const dayName = daysIndo[dateObj.getDay()];
          const mName = months[dateObj.getMonth()];
          displayText.textContent = `${dayName}, ${dd} ${mName} ${yyyy}`;
      }

      // Tandai container trigger button dengan class has-value jika ada
      triggerBtn?.closest('.tugas-field-filled')?.classList.add('has-value');

      renderCalendar();

      if (shouldClose) {
          setTimeout(() => {
              popup.classList.remove('is-active');
          }, 180);
      }
  }
  ```

- [ ] **1.2. Panggil `commitDateSelection` saat Sel Tanggal Diklik**:
  Pada loop pembuatan tanggal di `renderCalendar()`:
  ```javascript
  btn.addEventListener('click', () => {
      commitDateSelection(dateObj, true);
  });
  ```

- [ ] **1.3. Perbarui Tombol "Oke" dan "Hapus"**:
  Tombol "Oke" tetap memanggil `commitDateSelection(selectedDate, true)`.
  Tombol "Hapus" mengosongkan nilai:
  ```javascript
  if (btnClear) {
      btnClear.addEventListener('click', () => {
          selectedDate = null;
          if (hiddenInput) {
              hiddenInput.value = '';
              hiddenInput.dispatchEvent(new Event('change', { bubbles: true }));
          }
          if (displayText) displayText.textContent = 'Pilih batas tanggal';
          popup.classList.remove('is-active');
          renderCalendar();
      });
  }
  ```

- [ ] **1.4. Terapkan Logika yang Sama pada `initJadwalCalendar()`**:
  Lakukan hal serupa pada kalender jadwal publikasi (`#popupCalendarJadwal`).

---

### Fase 2: Perbaikan Logika Kalender pada `tugas-edit.js`
- [ ] **2.1. Implementasikan `commitDateSelection()` pada `tugas-edit.js`**:
  Buka [`resources/js/pages/pembimbing/tugas-edit.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-edit.js) pada fungsi `initDeadlineCalendar()`.
  Terapkan helper `commitDateSelection(dateObj, shouldClose)` yang sama persis sehingga klik pada tanggal tenggat tugas saat mengedit langsung tersimpan ke input `#realDeadlineInput` dan memperbarui teks label tanpa harus mencari tombol "Oke".

---

### Fase 3: Quality Gate & Verifikasi Kompilasi
- [ ] **3.1. Kompilasi Aset dengan Vite**:
  Jalankan `npm run build`. Pastikan file `tugas-create-*.js` dan `tugas-edit-*.js` ter-bundle tanpa galat sintaksis.
- [ ] **3.2. Formatter Check**:
  Jalankan `./vendor/bin/pint --test` untuk memastikan kode PHP di repositori tetap rapi.
- [ ] **3.3. Regression Testing**:
  Jalankan `php artisan test --filter="PesertaClassroomTugasLogbookTest|TugasLogbookTest"` untuk memastikan semua skenario penugasan tugas tetap lolos.
- [ ] **3.4. Browser Automation & DOM Test via Playwright**:
  - Buka halaman form buat tugas.
  - Klik tombol trigger `#btnTriggerDeadline`.
  - Klik salah satu tanggal yang tersedia (misal tanggal 25).
  - Verifikasi:
    1. Nilai `#realDeadlineInput.value` berubah seketika menjadi `YYYY-MM-25`.
    2. Teks `#txtSelectedDeadlineDisplay.textContent` berubah menampilkan tanggal dan hari tersebut.
    3. Popup kalender `.tugas-calendar-popup` otomatis tertutup (`is-active` hilang).

---

## 🛡️ 6. Analisis Risiko & Mitigasi (Risk & Mitigation)

| Potensi Risiko | Tingkat Risiko | Mitigasi yang Ditetapkan |
| :--- | :---: | :--- |
| **Pengguna Mengklik Tanggal Masa Lalu** | Rendah | Tanggal lampau (`dateObj < today`) sudah memiliki `btn.disabled = true`, sehingga event klik tidak akan terpicu pada tanggal terlarang. |
| **Ketergantungan Tombol Oke oleh User Lama** | Nihil | Tombol "Oke" (`#calBtnOk`) tetap dipertahankan dan berfungsi normal jika pengguna memilih menekannya secara manual. |
| **Validasi Form Laravel Menerima Nilai Kosong jika Dihapus** | Terkendali | Form Request backend sudah memvalidasi `'deadline' => ['required', 'date', 'after_or_equal:today']`. Jika tombol "Hapus" diklik, form akan menampilkan pesan validasi resmi saat submit. |

---

## ✅ 7. Kriteria Selesai (Definition of Done)

- [ ] Saat pengguna mengklik tanggal mana pun pada kalender tenggat, tanggal tersebut langsung tersimpan ke input form (`#realDeadlineInput`) dan teks tombol trigger langsung ter-update (contoh: "Jumat, 20 Nov 2026").
- [ ] Popup kalender otomatis menutup secara mulus setelah 180ms tanpa mengharuskan pengguna mencari tombol "Oke".
- [ ] Tombol "Batal" dan "Hapus" tetap berfungsi sebagaimana mestinya.
- [ ] Logika yang sama bekerja identik pada kalender jadwal publikasi tugas dan form edit tugas.
- [ ] `npm run build` sukses tanpa error.
- [ ] `php artisan test` tetap 100% PASS (926 test cases).
- [ ] Tidak ada commit atau merge yang dijalankan tanpa instruksi user.

---

> ⚠️ **PERHATIAN UNTUK IMPLEMENTOR**: 
> Dilarang menulis implementasi kode sebelum Pengguna (**User**) menyetujui dokumen perencanaan ini. Tunggu konfirmasi persetujuan resmi.

