<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\KuotaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PenempatanController as AdminPenempatanController;
use App\Http\Controllers\Admin\PengaturanController as AdminPengaturanController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\SertifikatController as AdminSertifikatController;
use App\Http\Controllers\Admin\WaitingListController as AdminWaitingListController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\VerificationController;
use App\Http\Controllers\SuperAdmin\DashboardController as SuperAdminDashboardController;
use App\Http\Controllers\SuperAdmin\MonitoringPesertaController;
use App\Http\Controllers\Pembimbing\AgendaBimbinganController as PembimbingAgendaBimbinganController;
use App\Http\Controllers\Pembimbing\DashboardController as PembimbingDashboardController;
use App\Http\Controllers\Pembimbing\LaporanKinerjaController as PembimbingLaporanKinerjaController;
use App\Http\Controllers\Pembimbing\LogbookReviewController as PembimbingLogbookReviewController;
use App\Http\Controllers\Pembimbing\PengaturanController as PembimbingPengaturanController;
use App\Http\Controllers\Pembimbing\PenilaianController as PembimbingPenilaianController;
use App\Http\Controllers\Pembimbing\PesertaController as PembimbingPesertaController;
use App\Http\Controllers\Pembimbing\ProfilController as PembimbingProfilController;
use App\Http\Controllers\Pembimbing\TugasController as PembimbingTugasController;
use App\Http\Controllers\Pembimbing\TugasTemplateController as PembimbingTugasTemplateController;
use App\Http\Controllers\Peserta\BerkasController;
use App\Http\Controllers\Peserta\DashboardController as PesertaDashboardController;
use App\Http\Controllers\Peserta\LogbookController as PesertaLogbookController;
use App\Http\Controllers\Peserta\NilaiController as PesertaNilaiController;
use App\Http\Controllers\Peserta\PendaftaranController as PesertaPendaftaranController;
use App\Http\Controllers\Peserta\ProfilController as PesertaProfilController;
use App\Http\Controllers\Peserta\PengaturanController as PesertaPengaturanController;
use App\Http\Controllers\Peserta\TugasController as PesertaTugasController;
use App\Http\Controllers\Auth\SocialiteController;
use App\Http\Controllers\Public\HealthController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\SuperAdmin\AkunController as SuperAdminAkunController;
use App\Http\Controllers\SuperAdmin\ArsipPesertaController;
use App\Http\Controllers\SuperAdmin\AuditLogController;
use App\Http\Controllers\SuperAdmin\BackupController;
use App\Http\Controllers\SuperAdmin\BidangController;
use App\Http\Controllers\SuperAdmin\BulanController;
use App\Http\Controllers\SuperAdmin\HealthMonitorController;
use App\Http\Controllers\SuperAdmin\JurusanController;
use App\Http\Controllers\SuperAdmin\PengaturanController;
use App\Http\Controllers\SuperAdmin\ProfilController as SuperAdminProfilController;
use App\Http\Controllers\SuperAdmin\RoleController;
use App\Http\Controllers\SuperAdmin\RubrikController;
use App\Http\Controllers\SuperAdmin\SlotKhususController;
use App\Http\Controllers\SuperAdmin\StafController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Portal MagKom (Magang Kominfo)
|--------------------------------------------------------------------------
*/

// Rute Publik (F-01, NFR-O1, NFR-R1, PRD 4.3 Poin 1)
Route::get('/', LandingController::class)->name('landing');
Route::get('/health', HealthController::class)->name('health');
Route::view('/kebijakan-privasi', 'public.kebijakan-privasi')->name('kebijakan-privasi');
Route::get('/verifikasi-dokumen/{kode}', \App\Http\Controllers\Public\VerifikasiDokumenController::class)
    ->name('dokumen.verifikasi')
    ->middleware('throttle:60,1');


// Rute Autentikasi Publik & Reset Password (F-02, F-14, F-18)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:pendaftaran');

    // OAuth Social Login (F-18: Google khusus peserta)
    Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('oauth.redirect');
    Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('oauth.callback');

    // Reset Password
    Route::get('/forgot-password', [ResetPasswordController::class, 'showForgot'])->name('password.request');
    Route::post('/forgot-password', [ResetPasswordController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:5,1');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showReset'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Verifikasi Email Calon Peserta (F-02 AC-02-4)
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [VerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [VerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [VerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    // Penegakan Ganti Password Wajib Staf (BR-15, AC-11-5)
    Route::get('/password/ganti', [PasswordController::class, 'showGanti'])->name('password.ganti');
    Route::post('/password/ganti', [PasswordController::class, 'updateGanti'])->name('password.ganti.update');
});

// Unduh & Pratinjau Dokumen Privat dengan Temporary Signed URL (AC-03-9)
Route::get('/berkas/{dokumen}/download', [BerkasController::class, 'unduh'])
    ->name('berkas.download')
    ->middleware(['signed:relative', 'auth']);

Route::get('/berkas/{dokumen}/pratinjau', [BerkasController::class, 'pratinjau'])
    ->name('berkas.pratinjau')
    ->middleware(['signed:relative', 'auth']);

// Rute Peserta Magang (F-02, F-03, F-08, F-09, F-10, F-12)
Route::middleware(['auth', 'verified', 'must_change_password', 'role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/dashboard', [PesertaDashboardController::class, 'index'])->name('dashboard');

    Route::get('/profil', [PesertaProfilController::class, 'show'])->name('profil.show');
    Route::post('/profil', [PesertaProfilController::class, 'simpan'])->name('profil.simpan');

    // Pengaturan Akun & Preferensi Peserta
    Route::get('/pengaturan', [PesertaPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/password', [PesertaPengaturanController::class, 'updatePassword'])->name('pengaturan.password.update');
    Route::post('/pengaturan/preferensi', [PesertaPengaturanController::class, 'updatePreferensi'])->name('pengaturan.preferensi.update');

    Route::get('/pendaftaran', [PesertaPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('/pendaftaran', [PesertaPendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::get('/pendaftaran/{pendaftaran}', [PesertaPendaftaranController::class, 'detail'])->name('pendaftaran.detail');
    Route::post('/pendaftaran/{pendaftaran}/ajukan', [PesertaPendaftaranController::class, 'ajukan'])->name('pendaftaran.ajukan');
    Route::post('/pendaftaran/{pendaftaran}/batalkan', [PesertaPendaftaranController::class, 'batalkan'])->name('pendaftaran.batalkan');
    Route::post('/pendaftaran/{pendaftaran}/berkas', [BerkasController::class, 'unggah'])->name('berkas.unggah');
    Route::get('/penempatan/{penempatan}/surat-diterima', [PesertaPendaftaranController::class, 'unduhSuratDiterima'])->name('penempatan.surat-diterima');
    Route::get('/penempatan/{penempatan}/surat-diterima/pratinjau', [PesertaPendaftaranController::class, 'pratinjauSuratDiterima'])->name('penempatan.surat-diterima.pratinjau');

    // Pelaksanaan Magang: Khusus Peserta yang Telah Resmi Diterima & Ditempatkan
    Route::middleware('peserta.diterima')->group(function () {
        // Pelaksanaan: Tugas & Progres (F-08)
        Route::get('/tugas', [PesertaTugasController::class, 'index'])->name('tugas.index');
        Route::get('/tugas/{tugas}', [PesertaTugasController::class, 'show'])->name('tugas.show');
        Route::get('/tugas/{tugas}/lampiran', [PesertaTugasController::class, 'unduhLampiran'])->name('tugas.lampiran');
        Route::post('/tugas/{tugas}/progres', [PesertaTugasController::class, 'unggahProgres'])->name('tugas.progres');

        // Pelaksanaan: Logbook Harian (F-09, F-15)
        Route::get('/logbook', [PesertaLogbookController::class, 'index'])->name('logbook.index');
        Route::post('/logbook', [PesertaLogbookController::class, 'store'])->name('logbook.store');
        Route::put('/logbook/{logbook}', [PesertaLogbookController::class, 'update'])->name('logbook.update');
        Route::get('/logbook/{logbook}/lampiran', [PesertaLogbookController::class, 'unduhLampiran'])->name('logbook.lampiran');

        // Penilaian & Unduh Lembar Nilai PDF (F-10, F-12)
        Route::get('/nilai', [PesertaNilaiController::class, 'show'])->name('nilai.show');
        Route::get('/nilai/unduh', [PesertaNilaiController::class, 'unduhPdf'])->name('nilai.unduh');
    });

    // Evaluasi & Survei Kepuasan Magang (PRD 4.3 Poin 4)
    Route::get('/survei', [\App\Http\Controllers\Peserta\SurveiController::class, 'show'])->name('survei.show');
    Route::post('/survei', [\App\Http\Controllers\Peserta\SurveiController::class, 'store'])->name('survei.store');
});

// Rute Pembimbing Magang (F-08, F-09, F-10, F-12, SEC-03, SEC-12)
Route::middleware(['auth', 'verified', 'must_change_password', 'account.active', 'role:pembimbing,admin,superadmin', 'superadmin.readonly'])->prefix('pembimbing')->name('pembimbing.')->group(function () {
    Route::get('/dashboard', [PembimbingDashboardController::class, 'index'])->name('dashboard');

    Route::get('/peserta', [PembimbingPesertaController::class, 'index'])->name('peserta.index');
    Route::get('/peserta/{penempatan}', [PembimbingPesertaController::class, 'show'])->name('peserta.show');

    // Template Tugas Mandiri & API Picker (PRD F-08)
    Route::get('/tugas/template', [PembimbingTugasTemplateController::class, 'index'])->name('tugas.template.index');
    Route::post('/tugas/template', [PembimbingTugasTemplateController::class, 'store'])->name('tugas.template.store');
    Route::get('/tugas/template-picker', [PembimbingTugasTemplateController::class, 'pickerJson'])->name('tugas.template.picker');
    Route::put('/tugas/template/{template}', [PembimbingTugasTemplateController::class, 'update'])->name('tugas.template.update');
    Route::delete('/tugas/template/{template}', [PembimbingTugasTemplateController::class, 'destroy'])->name('tugas.template.destroy');

    Route::get('/tugas', [PembimbingTugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/create', [PembimbingTugasController::class, 'create'])->name('tugas.create');
    Route::post('/tugas', [PembimbingTugasController::class, 'store'])->name('tugas.store');
    Route::get('/tugas/{tugas}', [PembimbingTugasController::class, 'show'])->name('tugas.show');
    Route::get('/tugas/{tugas}/edit', [PembimbingTugasController::class, 'edit'])->name('tugas.edit');
    Route::put('/tugas/{tugas}', [PembimbingTugasController::class, 'update'])->name('tugas.update');
    Route::get('/tugas/{tugas}/lampiran', [PembimbingTugasController::class, 'unduhLampiran'])->name('tugas.lampiran');
    Route::post('/tugas/{tugas}/selesai', [PembimbingTugasController::class, 'selesai'])->name('tugas.selesai');
    Route::post('/progres/{progres}/review', [PembimbingTugasController::class, 'reviewProgres'])->name('progres.review');

    Route::get('/logbook', [PembimbingLogbookReviewController::class, 'index'])->name('logbook.index');
    Route::post('/logbook/bulk-review', [PembimbingLogbookReviewController::class, 'bulkReview'])->name('logbook.bulk-review');
    Route::post('/logbook/{logbook}/review', [PembimbingLogbookReviewController::class, 'review'])->name('logbook.review');

    Route::get('/penilaian', [PembimbingPenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/ekspor-bidang', [PembimbingPenilaianController::class, 'exportRekapBidang'])->name('penilaian.ekspor-bidang');
    Route::get('/penilaian/{penempatan}', [PembimbingPenilaianController::class, 'show'])->name('penilaian.show');
    Route::post('/penilaian/{penempatan}', [PembimbingPenilaianController::class, 'store'])->name('penilaian.store');
    Route::post('/penempatan/{penempatan}/selesai', [PembimbingPenilaianController::class, 'selesai'])->name('penempatan.selesai');
    Route::get('/penilaian/{penempatan}/unduh', [PembimbingPenilaianController::class, 'unduhPdf'])->name('penilaian.unduh');

    // Agenda Penjadwalan Mentoring & Notulensi Bimbingan Lapangan
    Route::get('/agenda', [PembimbingAgendaBimbinganController::class, 'index'])->name('agenda.index');
    Route::post('/agenda', [PembimbingAgendaBimbinganController::class, 'store'])->name('agenda.store');
    Route::put('/agenda/{agenda}', [PembimbingAgendaBimbinganController::class, 'update'])->name('agenda.update');
    Route::delete('/agenda/{agenda}', [PembimbingAgendaBimbinganController::class, 'destroy'])->name('agenda.destroy');

    // Laporan Kinerja Mentoring ASN Sleman
    Route::get('/laporan-kinerja', [PembimbingLaporanKinerjaController::class, 'index'])->name('laporan-kinerja.index');
    Route::get('/laporan-kinerja/cetak', [PembimbingLaporanKinerjaController::class, 'cetak'])->name('laporan-kinerja.cetak');

    // Profil & Pengaturan Akun Pembimbing
    Route::get('/profil', [PembimbingProfilController::class, 'show'])->name('profil.show');
    Route::match(['post', 'put'], '/profil', [PembimbingProfilController::class, 'update'])->name('profil.update');
    Route::get('/pengaturan', [PembimbingPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/password', [PembimbingPengaturanController::class, 'updatePassword'])->name('pengaturan.password.update');
    Route::post('/pengaturan/preferensi', [PembimbingPengaturanController::class, 'updatePreferensi'])->name('pengaturan.preferensi.update');
});

// Rute Admin & SuperAdmin (F-04, F-05, F-07, F-17)
Route::middleware(['auth', 'verified', 'must_change_password', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/kuota', [KuotaController::class, 'index'])->name('kuota.index');
    Route::post('/kuota', [KuotaController::class, 'store'])->name('kuota.store');
    Route::put('/kuota/{kuota}', [KuotaController::class, 'update'])->name('kuota.update');
    Route::patch('/kuota/{kuota}/toggle', [KuotaController::class, 'toggleAktif'])->name('kuota.toggle');

    Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/download-zip', [AdminPendaftaranController::class, 'downloadBundelZip'])->name('pendaftaran.download-zip');
    Route::get('/pendaftaran/{pendaftaran}', [AdminPendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::post('/pendaftaran/{pendaftaran}/putuskan', [AdminPendaftaranController::class, 'putuskan'])->name('pendaftaran.putuskan');

    Route::get('/waiting-list', [AdminWaitingListController::class, 'index'])->name('waiting-list.index');
    Route::post('/waiting-list/promote', [AdminWaitingListController::class, 'promoteFifo'])->name('waiting-list.promote');
    Route::post('/waiting-list/{bulanMagang}/tutup', [AdminWaitingListController::class, 'tutupAntrean'])->name('waiting-list.tutup');

    Route::get('/penempatan', [AdminPenempatanController::class, 'index'])->name('penempatan.index');
    Route::get('/penempatan/ekspor-surat-diterima', [AdminPenempatanController::class, 'exportRekapSuratDiterima'])->name('penempatan.ekspor-surat-diterima');
    Route::post('/penempatan/manual', [AdminPenempatanController::class, 'storeManual'])->name('penempatan.manual');
    Route::post('/penempatan/bulk-reassign', [AdminPenempatanController::class, 'bulkReassignPembimbing'])->name('penempatan.bulk-reassign');
    Route::get('/penempatan/{penempatan}', [AdminPenempatanController::class, 'show'])->name('penempatan.show');
    Route::get('/penempatan/{penempatan}/surat-diterima', [AdminPenempatanController::class, 'unduhSuratDiterima'])->name('penempatan.surat-diterima');
    Route::get('/penempatan/{penempatan}/surat-diterima/pratinjau', [AdminPenempatanController::class, 'pratinjauSuratDiterima'])->name('penempatan.surat-diterima.pratinjau');
    Route::post('/penempatan/{penempatan}/pindah-pembimbing', [AdminPenempatanController::class, 'pindahPembimbing'])->name('penempatan.pindah-pembimbing');
    Route::post('/penempatan/{penempatan}/batalkan', [AdminPenempatanController::class, 'batalkan'])->name('penempatan.batalkan');

    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/pendaftaran', [LaporanController::class, 'pendaftaran'])->name('laporan.pendaftaran');
    Route::get('/laporan/penempatan', [LaporanController::class, 'penempatan'])->name('laporan.penempatan');
    Route::get('/laporan/kuota', [LaporanController::class, 'kuota'])->name('laporan.kuota');
    Route::get('/laporan/nilai', [LaporanController::class, 'nilai'])->name('laporan.nilai');
    Route::get('/laporan/tahunan', [\App\Http\Controllers\Admin\AnalitikTahunanController::class, 'index'])->name('laporan.tahunan');
    Route::get('/laporan/tahunan/export', [\App\Http\Controllers\Admin\AnalitikTahunanController::class, 'export'])->name('laporan.tahunan.export');

    // Hub Penerbitan Sertifikat Kelulusan Resmi (AC-12, BR-10)
    Route::get('/sertifikat', [AdminSertifikatController::class, 'index'])->name('sertifikat.index');
    Route::get('/sertifikat/{penempatan}/cetak', [AdminSertifikatController::class, 'cetak'])->name('sertifikat.cetak');

    // Evaluasi Mutu & Survei Kepuasan Alumni (PRD 4.3 Poin 4)
    Route::get('/survei', [\App\Http\Controllers\Admin\SurveiAdminController::class, 'index'])->name('survei.index');
    Route::get('/survei/export', [\App\Http\Controllers\Admin\SurveiAdminController::class, 'export'])->name('survei.export');

    // Profil & Pengaturan Akun Administrator
    Route::get('/profil', [AdminProfilController::class, 'show'])->name('profil.show');
    Route::match(['post', 'put'], '/profil', [AdminProfilController::class, 'update'])->name('profil.update');
    Route::get('/pengaturan', [AdminPengaturanController::class, 'index'])->name('pengaturan.index');
    Route::put('/pengaturan/password', [AdminPengaturanController::class, 'updatePassword'])->name('pengaturan.password.update');
    Route::post('/pengaturan/preferensi', [AdminPengaturanController::class, 'updatePreferensi'])->name('pengaturan.preferensi.update');
});

// Rute Khusus SuperAdmin (F-06, F-11, F-13)
Route::middleware(['auth', 'verified', 'must_change_password', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminDashboardController::class, 'index'])->name('dashboard');

    // Monitoring Pelaksanaan Magang Aktif (PRD 3.3, AC-07-7, AC-08-5)
    Route::get('/monitoring-peserta', [MonitoringPesertaController::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring-peserta/{penempatan}', [MonitoringPesertaController::class, 'show'])->name('monitoring.show');

    // Bulan Magang & Slot Khusus
    Route::get('/bulan', [BulanController::class, 'index'])->name('bulan.index');
    Route::post('/bulan', [BulanController::class, 'store'])->name('bulan.store');
    Route::put('/bulan/{bulan}', [BulanController::class, 'update'])->name('bulan.update');
    Route::patch('/bulan/{bulan}/toggle', [BulanController::class, 'toggleAktif'])->name('bulan.toggle');

    Route::get('/slot-khusus', [SlotKhususController::class, 'index'])->name('slot-khusus.index');
    Route::post('/slot-khusus', [SlotKhususController::class, 'store'])->name('slot-khusus.store');
    Route::put('/slot-khusus/{slot_khusus}', [SlotKhususController::class, 'update'])->name('slot-khusus.update');
    Route::patch('/slot-khusus/{slot_khusus}/toggle', [SlotKhususController::class, 'toggleAktif'])->name('slot-khusus.toggle');

    // Manajemen Staf Internal (F-11)
    Route::get('/staf', [StafController::class, 'index'])->name('staf.index');
    Route::post('/staf', [StafController::class, 'store'])->name('staf.store');
    Route::put('/staf/{staf}', [StafController::class, 'update'])->name('staf.update');
    Route::post('/staf/{staf}/reset-password', [StafController::class, 'resetPassword'])->name('staf.reset-password');
    Route::post('/staf/{staf}/regenerate-password', [StafController::class, 'regenerateTemporaryPassword'])->name('staf.regenerate-password');
    Route::patch('/staf/{staf}/toggle', [StafController::class, 'toggleAktif'])->name('staf.toggle');

    // Matriks Hak Akses dan Role (Figma SA-02, PRD 3.1 & 3.3)
    Route::get('/role', [RoleController::class, 'index'])->name('role.index');

    // Master Data (Jurusan, Bidang, Rubrik) (F-11)
    Route::get('/jurusan', [JurusanController::class, 'index'])->name('jurusan.index');
    Route::post('/jurusan', [JurusanController::class, 'store'])->name('jurusan.store');
    Route::put('/jurusan/{jurusan}', [JurusanController::class, 'update'])->name('jurusan.update');
    Route::patch('/jurusan/{jurusan}/toggle', [JurusanController::class, 'toggleAktif'])->name('jurusan.toggle');

    Route::get('/bidang', [BidangController::class, 'index'])->name('bidang.index');
    Route::post('/bidang', [BidangController::class, 'store'])->name('bidang.store');
    Route::put('/bidang/{bidang}', [BidangController::class, 'update'])->name('bidang.update');
    Route::patch('/bidang/{bidang}/toggle', [BidangController::class, 'toggleAktif'])->name('bidang.toggle');

    Route::get('/rubrik', [RubrikController::class, 'index'])->name('rubrik.index');
    Route::post('/rubrik', [RubrikController::class, 'store'])->name('rubrik.store');
    Route::put('/rubrik/{rubrik}', [RubrikController::class, 'update'])->name('rubrik.update');
    Route::patch('/rubrik/{rubrik}/toggle', [RubrikController::class, 'toggleAktif'])->name('rubrik.toggle');

    // Parameter Pengaturan Sistem (F-11, PRD 10.3 NFR-A5)
    Route::get('/pengaturan', [PengaturanController::class, 'index'])->name('pengaturan.index');
    Route::get('/pengaturan/export-json', [PengaturanController::class, 'exportMasterSnapshotJson'])->name('pengaturan.export-json');
    Route::put('/pengaturan', [PengaturanController::class, 'update'])->name('pengaturan.update');
    Route::post('/pengaturan/purge-audit', [PengaturanController::class, 'triggerPurgeAudit'])->name('pengaturan.purge-audit');

    // Arsip Peserta Magang (F-12 AC-12-4, PRD 9.2)
    Route::get('/arsip-peserta', [ArsipPesertaController::class, 'index'])->name('arsip.index');
    Route::get('/arsip-peserta/{penempatan}', [ArsipPesertaController::class, 'show'])->name('arsip.show');

    // Audit Log Trail (F-13 AC-13-6)
    Route::get('/audit-log/export', [AuditLogController::class, 'export'])->name('audit-log.export');
    Route::get('/audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');

    // Telemetri Kesehatan Sistem & Infrastruktur
    Route::get('/health-monitor', [HealthMonitorController::class, 'index'])->name('health.index');
    Route::post('/health-monitor/security-sweep', [HealthMonitorController::class, 'runSecuritySweep'])->name('health.security-sweep');

    // Hub Cadangan Basis Data (Disaster Recovery & Backup) (SEC-03)
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::post('/backup/snapshot', [BackupController::class, 'triggerSnapshot'])->name('backup.snapshot');
    Route::get('/backup/{filename}/download', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/{filename}', [BackupController::class, 'destroy'])->name('backup.destroy');

    // Profil & Akun / Preferensi Pribadi SuperAdmin
    Route::get('/profil', [SuperAdminProfilController::class, 'show'])->name('profil.show');
    Route::match(['post', 'put'], '/profil', [SuperAdminProfilController::class, 'update'])->name('profil.update');
    Route::get('/akun', [SuperAdminAkunController::class, 'index'])->name('akun.index');
    Route::put('/akun/password', [SuperAdminAkunController::class, 'updatePassword'])->name('akun.password.update');
    Route::post('/akun/preferensi', [SuperAdminAkunController::class, 'updatePreferensi'])->name('akun.preferensi.update');
});
