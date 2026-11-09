# Rencana Kerja: Fitur Batalkan Pengiriman (Unsubmit) Tugas Peserta Ala Google Classroom

Dokumen ini berisi spesifikasi teknis dan rencana implementasi terstruktur untuk fitur **Batalkan Pengiriman (*Unsubmit*) Tugas** pada modul tugas bimbingan peserta, mengacu pada alur standar antarmuka Google Classroom dan tangkapan layar referensi yang disediakan pengguna.

---

## 1. Tujuan
1. **Fitur Batalkan Pengiriman (*Unsubmit*)**: Memberikan fleksibilitas bagi peserta magang untuk membatalkan pengiriman tugas yang telah diserahkan (*turned in*), selama pengiriman tersebut masih berstatus **Menunggu Peninjauan (*pending review*)** dan belum diverifikasi/diselesaikan oleh pembimbing.
2. **Paritas UX Google Classroom**:
   - Menampilkan status *"Diserahkan"* dengan daftar kartu lampiran yang telah diunggah.
   - Menyediakan tombol *outlined pill* **"Batalkan pengiriman"** di bawah daftar berkas lampiran.
   - Menampilkan modal dialog konfirmasi konseptual: *"Batalkan pengiriman untuk menambahkan atau mengubah lampiran. Jangan lupa menyerahkannya kembali setelah Anda selesai."*
   - Mengembalikan status tugas ke mode aktif/draf pengerjaan sehingga peserta dapat menambah, mengganti, atau menghapus lampiran sebelum menyerahkan kembali (*resubmit*).
3. **Integritas Audit & Keamanan Sistem**:
   - Menegakkan otorisasi ketat (`TugasPolicy@submitProgres`) dan proteksi CSRF.
   - Memastikan pembatalan pengiriman dicegah jika tugas sudah berstatus `Selesai` atau sudah dinilai/diterima oleh pembimbing (*immutable evaluation*).
   - Mencatat seluruh riwayat pembatalan pengiriman ke dalam tabel `audit_log` (*audit trail*).

---

## 2. Asumsi & Pertanyaan Terbuka

### Asumsi Kerja:
1. **Jendela Batalkan Pengiriman**:
   - Fitur batalkan pengiriman hanya berlaku apabila progres tugas terakhir berstatus `menunggu` (*StatusReviewProgresEnum::Menunggu*) dan status tugas belum `selesai`.
   - Jika pembimbing telah menyetujui/menyelesaikan tugas (`status = 'selesai'`), tombol "Batalkan pengiriman" tidak dapat digunakan dan form terkunci.
   - Jika pembimbing memberikan catatan revisi (`status_review = 'perlu_revisi'`), antarmuka sudah secara otomatis menyediakan alur *"Serahkan Ulang Revisi"*.
2. **Penanganan Lampiran Saat Dibatalkan**:
   - Berkas fisik di storage private tidak langsung dihapus permanen agar peserta tidak kehilangan pekerjaannya secara tiba-tiba jika tidak sengaja membatalkan pengiriman.
   - Nilai file dan link yang baru saja dibatalkan diteruskan kembali ke form draf pengumpulan via sesi (*flash session*), sehingga lampiran tetap tersedia di area draf peserta dan dapat dihapus/diganti sesuka hati menggunakan tombol `✕`.
3. **Status Tugas Utama (`tugas.status`)**:
   - Jika tugas sebelumnya berstatus `proses`, status tetap `proses` karena peserta memang sedang mengerjakan tugas tersebut.

---

## 3. Evaluasi Opsi Arsitektur

| Aspek | Opsi A: Hapus Record Progres Pending & Restore ke Draf (Rekomendasi) | Opsi B: Tambah Enum Status `dibatalkan` ke Database |
| :--- | :--- | :--- |
| **Dampak Skema Database** | **Nol migrasi**: Menggunakan tabel dan enum yang sudah ada tanpa risiko *schema drift* atau perubahan DDL. | **Memerlukan migrasi**: Mengubah definisi `enum('menunggu', 'diterima', 'perlu_revisi', 'dibatalkan')` pada kolom `status_review` tabel `progres_log`. |
| **Kesesuaian dengan Aturan Repositori (`AGENTS.md`)** | **Sangat Sesuai**: Mematuhi aturan nomor 7 ("*Jangan mengubah schema/database migration tanpa alasan dan persetujuan eksplisit*"). | Berisiko melanggar aturan integritas skema dan berpotensi memengaruhi query pembimbing yang mengharapkan enum existing. |
| **Pengalaman Pengguna (UX)** | Peserta langsung mendapatkan form pengumpulan yang kembali terbuka dengan file/link lama tetap terisi sebagai draf yang siap diedit atau diserahkan ulang. | Perlu logika khusus untuk membedakan antara progres aktif, progres revisi, dan progres dibatalkan di berbagai query controller. |
| **Audit Trail & Akuntabilitas** | Riwayat pembatalan tercatat lengkap di tabel `audit_log` dengan detail data lama sebelum dihapus. | Riwayat tercatat di audit log dan record tabel. |

> **Rekomendasi**: Dipilih **Opsi A** karena tidak mengubah struktur skema database (*zero migration*), aman dari *breaking changes* terhadap query pembimbing (dashboard metrics, monitoring, arsip), dan sepenuhnya sesuai dengan arsitektur Laravel yang stabil di repositori MagKom.

---

## 4. Pendekatan Teknis

### A. Alur Backend (`app/Http/Controllers/Peserta/TugasController.php`)
1. **Endpoint Baru**:
   - Route: `POST /peserta/tugas/{tugas}/batal-kirim`
   - Name: `peserta.tugas.batal-kirim`
2. **Logika Eksekusi `batalkanPengiriman`**:
   ```php
   public function batalkanPengiriman(Request $request, Tugas $tugas): mixed
   {
       Gate::authorize('submitProgres', $tugas);

       // 1. Validasi tugas belum selesai
       if ($tugas->status === StatusTugasEnum::Selesai) {
           return back()->with('error', 'Tugas ini telah selesai dan tidak dapat dibatalkan.');
       }

       // 2. Ambil progres terakhir yang sedang menunggu review
       $latestProgres = $tugas->progresLogs()->latest('id')->first();
       if (! $latestProgres || $latestProgres->status_review !== StatusReviewProgresEnum::Menunggu) {
           return back()->with('error', 'Pengiriman tidak dapat dibatalkan karena tugas belum dikumpulkan atau sudah ditinjau oleh pembimbing.');
       }

       // 3. Simpan data lama untuk audit dan simpan ke flash session agar tetap terisi di form draf
       $nilaiLama = $latestProgres->only(['tugas_id', 'file', 'link', 'status_review', 'catatan']);
       $oldFile = $latestProgres->file;
       $oldLink = $latestProgres->link;

       // 4. Hapus record progres pending
       $latestProgres->delete();

       // 5. Catat ke Audit Trail
       $this->auditService->catat(
           tipe: TipeAuditEnum::Aksi,
           tabel: 'progres_log',
           idData: $latestProgres->id,
           nilaiLama: $nilaiLama,
           detail: ['aksi' => 'batalkan_pengiriman_tugas']
       );

       // 6. Notifikasi SSE ke Pembimbing (Update counter antrean review berkurang)
       // Mengirim event pembatalan bila pembimbing sedang memantau antrean

       if ($request->expectsJson() || $request->wantsJson()) {
           return response()->json([
               'success' => true,
               'message' => 'Pengiriman tugas berhasil dibatalkan. Anda dapat mengedit lampiran kembali.',
           ]);
       }

       return redirect()->route('peserta.tugas.show', $tugas)
           ->with('success', 'Pengiriman tugas berhasil dibatalkan. Silakan ubah atau lengkapi lampiran Anda.')
           ->with('unsubmitted_file', $oldFile)
           ->with('unsubmitted_link', $oldLink);
   }
   ```

### B. Alur Antarmuka & Markup (`resources/views/peserta/tugas/show.blade.php`)
1. **Kondisi Tampilan Saat Tugas Berstatus Diserahkan (`$hasSubmitted && $latestReviewStatus === 'menunggu'`):**
   - Menampilkan status *"Diserahkan"* (warna hijau/teal `#16A34A`).
   - Menampilkan kartu berkas lampiran dengan visual thumbnail/preview ala Google Classroom.
   - Menampilkan tombol outlined pill:
     ```html
     <button type="button" class="btn-batalkan-pengiriman" id="btnBukaModalBatalKirim">
         Batalkan pengiriman
     </button>
     ```
   - Catatan di bawah tombol: *"Batalkan pengiriman untuk menambahkan atau mengubah lampiran"*.
2. **Modal Dialog Konfirmasi Batalkan Pengiriman**:
   ```html
   <div class="tugas-modal-backdrop classroom-modal-backdrop" id="modalKonfirmasiBatalKirim" role="dialog" aria-modal="true" aria-labelledby="modalBatalKirimTitle">
       <div class="tugas-dialog tugas-dialog--batal-kirim">
           <h3 class="tugas-dialog-title" id="modalBatalKirimTitle">Batalkan pengiriman?</h3>
           <p class="tugas-dialog-desc">
               Batalkan pengiriman untuk menambahkan atau mengubah lampiran. Jangan lupa menyerahkannya kembali setelah Anda selesai.
           </p>
           <form action="{{ route('peserta.tugas.batal-kirim', $tugas) }}" method="POST" id="formBatalKirimTugas">
               @csrf
               <div class="tugas-dialog-actions">
                   <button type="button" class="tugas-dialog-btn-text" data-action="close-modal">Batal</button>
                   <button type="submit" class="tugas-dialog-btn-pill btn-confirm-unsubmit">Batalkan pengiriman</button>
               </div>
           </form>
       </div>
   </div>
   ```
3. **Restorasi Lampiran ke Draf**:
   - Jika terdapat `session('unsubmitted_file')` atau `session('unsubmitted_link')`, komponen form pengerjaan otomatis menampilkan kartu lampiran terpilih dengan tombol silang `✕`, siap untuk diedit atau diserahkan ulang tanpa perlu mengunggah ulang dari awal.

### C. Desain & Styling (`resources/css/pages/peserta/tugas-detail.css`)
- `.btn-batalkan-pengiriman`:
  - Outlined pill button: `border: 1.5px solid #01347C; color: #01347C; background: #FFFFFF; border-radius: 9999px; width: 100%; padding: 10px 18px; font-size: 0.875rem; font-weight: 600; cursor: pointer; transition: all 0.2s ease; margin-top: 12px;`
  - Hover: `background: #EFF6FF; border-color: #00224F; color: #00224F;`
- `.tugas-dialog--batal-kirim`:
  - Lebar modal ringkas (`max-width: 440px`), padding `24px`, border-radius `12px`.
  - Judul modal tebal (`font-size: 1.125rem; font-weight: 700; color: #111827;`).
  - Teks deskripsi informatif (`font-size: 0.875rem; color: #4B5563; line-height: 1.5;`).
  - Tombol aksi: `tugas-dialog-btn-text` (Batal) dan `btn-confirm-unsubmit` (Navy `#01347C` pill).
- Mode Gelap (`[data-theme="dark"]`):
  - `.btn-batalkan-pengiriman`: `background: transparent !important; border-color: #38BDF8 !important; color: #38BDF8 !important;`
  - `.btn-batalkan-pengiriman:hover`: `background: rgba(56, 189, 248, 0.12) !important;`
  - Modal dialog: background `#182234`, border `#26354D`, teks `#F8FAFC`.

### D. Interaksi JavaScript (`resources/js/pages/peserta/tugas-detail.js`)
- Mengikat klik `#btnBukaModalBatalKirim` untuk membuka `#modalKonfirmasiBatalKirim`.
- Menutup modal saat klik tombol `data-action="close-modal"`, klik area backdrop luar, atau menekan tombol `Escape`.
- Menambahkan *loading state* pada tombol submit modal untuk mencegah klik ganda (*double submission protection*).

---

## 5. Perubahan File

| File | Aksi | Ringkasan Perubahan |
| :--- | :---: | :--- |
| `routes/web.php` | **Modify** | Menambahkan route `POST /peserta/tugas/{tugas}/batal-kirim` (`peserta.tugas.batal-kirim`). |
| `app/Http/Controllers/Peserta/TugasController.php` | **Modify** | Menambahkan method `batalkanPengiriman` dengan validasi state, penghapusan progres pending, flash session draft, dan pencatatan audit log. |
| `resources/views/peserta/tugas/show.blade.php` | **Modify** | Menambahkan tombol "Batalkan pengiriman" saat status diserahkan, menambahkan modal dialog konfirmasi batal kirim, dan menangani restorasi berkas/link unsubmitted ke draf form. |
| `resources/css/pages/peserta/tugas-detail.css` | **Modify** | Menambahkan styling `.btn-batalkan-pengiriman`, modal `.tugas-dialog--batal-kirim`, dan integrasi tema gelap. |
| `resources/js/pages/peserta/tugas-detail.js` | **Modify** | Menambahkan handler modal konfirmasi batalkan pengiriman dan proteksi tombol submit ganda. |
| `tests/Feature/PesertaTugasBatalKirimTest.php` | **Create** | Feature test lengkap untuk skenario batal kirim (happy path, unauthorized IDOR, penolakan saat tugas selesai, dan verifikasi audit log). |

---

## 6. Langkah Implementasi Bertahap

### Fase 1: Routing & Backend Controller
- [ ] 1.1 Daftarkan route `POST /peserta/tugas/{tugas}/batal-kirim` pada grup route peserta di `routes/web.php`.
- [ ] 1.2 Implementasikan method `batalkanPengiriman(Request $request, Tugas $tugas)` di `app/Http/Controllers/Peserta/TugasController.php`:
  - Verifikasi autorisasi `submitProgres`.
  - Validasi bahwa tugas belum `selesai`.
  - Ambil progres terakhir dan pastikan `status_review === 'menunggu'`.
  - Hapus record progres pending dan catat riwayat ke `AuditService`.
  - Redirect kembali ke halaman detail tugas dengan flash pesan sukses dan data lampiran unsubmitted.

### Fase 2: Tampilan & Modal Konfirmasi (Blade)
- [ ] 2.1 Buka `resources/views/peserta/tugas/show.blade.php`, sesuaikan blok kondisi saat tugas berstatus diserahkan agar menampilkan tombol *"Batalkan pengiriman"*.
- [ ] 2.2 Tambahkan elemen `#modalKonfirmasiBatalKirim` berisi pesan standar Google Classroom dan tombol aksi konfirmasi.
- [ ] 2.3 Tambahkan logika restorasi nilai file/link dari flash session (`unsubmitted_file`, `unsubmitted_link`) ke dalam form draf agar peserta tidak perlu mengunggah ulang dari nol bila hanya ingin mengedit tautan.

### Fase 3: Desain & Interaksi Frontend (CSS & JS)
- [ ] 3.1 Buka `resources/css/pages/peserta/tugas-detail.css`, tambahkan rule `.btn-batalkan-pengiriman`, `.tugas-dialog--batal-kirim`, dan styling tema gelap `[data-theme="dark"]`.
- [ ] 3.2 Buka `resources/js/pages/peserta/tugas-detail.js`, tambahkan event listener untuk memicu pembukaan dan penutupan modal konfirmasi batal kirim.
- [ ] 3.3 Jalankan kompilasi aset: `npm run build`.

### Fase 4: Quality Gate & Pengujian Otomatis
- [ ] 4.1 Buat test suite baru di `tests/Feature/PesertaTugasBatalKirimTest.php`.
- [ ] 4.2 Jalankan PHPUnit: `php artisan test tests/Feature/PesertaTugasBatalKirimTest.php tests/Feature/PesertaTugasClassroomUploadTest.php`.
- [ ] 4.3 Jalankan Laravel Pint: `./vendor/bin/pint --test`.
- [ ] 4.4 Jalankan PHPStan: `./vendor/bin/phpstan analyse --memory-limit=1G`.

---

## 7. Strategi Testing

1. **Skenario Happy Path**:
   - Peserta mengumpulkan tugas (status review: `menunggu`).
   - Peserta melihat tombol "Batalkan pengiriman".
   - Peserta menekan tombol batalkan pengiriman dan mengonfirmasi pada modal.
   - Sistem berhasil membatalkan pengiriman:
     - Record `progres_log` pending terhapus.
     - Status tugas kembali ke mode aktif/draf.
     - Form pengumpulan kembali terbuka dengan tombol "+ Tambah berkas" dan "Serahkan".
     - Catatan audit log tercatat dengan detail aksi `batalkan_pengiriman_tugas`.
2. **Skenario Sad Path & Security Guard**:
   - **Tugas Sudah Selesai**: Peserta mencoba membatalkan pengiriman tugas yang sudah berstatus `selesai` -> Sistem menolak (HTTP 422 / Error session: "Tugas ini telah selesai dan tidak dapat dibatalkan").
   - **Tugas Sudah Direview Pembimbing**: Status review sudah berubah menjadi `diterima` -> Sistem menolak pembatalan pengiriman.
   - **Proteksi IDOR**: Peserta A mencoba membatalkan tugas milik Peserta B -> Ditolak dengan HTTP 403 Forbidden via `TugasPolicy`.
   - **Guest / Unauthenticated**: Akses route tanpa login -> Dialihkan ke halaman login (HTTP 302).

---

## 8. Risiko & Mitigasi

- **Risiko**: Peserta tidak sengaja memencet tombol batalkan pengiriman saat hanya ingin melihat tugasnya.
  - **Mitigasi**: Diberikan modal konfirmasi eksplisit (*confirmation dialog*) sebelum aksi pembatalan benar-benar dieksekusi.
- **Risiko**: Peserta kehilangan berkas yang sudah diunggah setelah membatalkan pengiriman.
  - **Mitigasi**: Berkas fisik di storage private tetap dipertahankan, dan path/link lamanya langsung dipulihkan ke form pengerjaan draf peserta.
- **Risiko**: Inkonsistensi data dengan antrean evaluasi pembimbing.
  - **Mitigasi**: Penghapusan record pending otomatis menyinkronkan counter `perlu_review` pembimbing secara realtime, tanpa meninggalkan data *stale* di database.

---

## 9. Kriteria Selesai (Definition of Done)
- [ ] Tombol "Batalkan pengiriman" tampil saat tugas diserahkan dan menunggu review pembimbing.
- [ ] Modal dialog konfirmasi tampil dengan pesan informatif ala Google Classroom.
- [ ] Pengiriman berhasil dibatalkan dan form tugas kembali ke status pengerjaan aktif.
- [ ] Riwayat pembatalan tercatat rapi pada `audit_log`.
- [ ] Pembatalan dicegah saat tugas sudah selesai atau sudah dinilai pembimbing.
- [ ] Seluruh uji otomatis (`npm run build`, PHPUnit, PHPStan, Pint) berstatus **HIJAU (100% Passed)**.

