<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\KuotaController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;
use App\Http\Controllers\Admin\PenempatanController as AdminPenempatanController;
use App\Http\Controllers\Admin\WaitingListController as AdminWaitingListController;
use App\Http\Controllers\Pembimbing\LogbookReviewController as PembimbingLogbookReviewController;
use App\Http\Controllers\Pembimbing\PenilaianController as PembimbingPenilaianController;
use App\Http\Controllers\Pembimbing\TugasController as PembimbingTugasController;
use App\Http\Controllers\Peserta\BerkasController;
use App\Http\Controllers\Peserta\LogbookController as PesertaLogbookController;
use App\Http\Controllers\Peserta\NilaiController as PesertaNilaiController;
use App\Http\Controllers\Peserta\PendaftaranController as PesertaPendaftaranController;
use App\Http\Controllers\Peserta\ProfilController as PesertaProfilController;
use App\Http\Controllers\Peserta\TugasController as PesertaTugasController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Public\LandingController;
use App\Http\Controllers\SuperAdmin\BulanController;
use App\Http\Controllers\SuperAdmin\SlotKhususController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Portal MagKom (Magang Kominfo)
|--------------------------------------------------------------------------
*/

// Rute Publik (F-01: Landing Page Kuota & Informasi Magang)
Route::get('/', LandingController::class)->name('landing');

// Rute Autentikasi Publik (F-02, F-14)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');

    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:pendaftaran');
});
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Unduh Dokumen Privat dengan Temporary Signed URL (AC-03-9)
Route::get('/berkas/{dokumen}/download', [BerkasController::class, 'unduh'])
    ->name('berkas.download')
    ->middleware(['signed', 'auth']);

// Rute Peserta Magang (F-02, F-03, F-08, F-09, F-10, F-12)
Route::middleware(['auth', 'verified', 'role:peserta'])->prefix('peserta')->name('peserta.')->group(function () {
    Route::get('/profil', [PesertaProfilController::class, 'show'])->name('profil.show');
    Route::post('/profil', [PesertaProfilController::class, 'simpan'])->name('profil.simpan');

    Route::get('/pendaftaran', [PesertaPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::post('/pendaftaran', [PesertaPendaftaranController::class, 'store'])->name('pendaftaran.store');
    Route::get('/pendaftaran/{pendaftaran}', [PesertaPendaftaranController::class, 'detail'])->name('pendaftaran.detail');
    Route::post('/pendaftaran/{pendaftaran}/ajukan', [PesertaPendaftaranController::class, 'ajukan'])->name('pendaftaran.ajukan');
    Route::post('/pendaftaran/{pendaftaran}/berkas', [BerkasController::class, 'unggah'])->name('berkas.unggah');
    Route::get('/penempatan/{penempatan}/surat-diterima', [PesertaPendaftaranController::class, 'unduhSuratDiterima'])->name('penempatan.surat-diterima');

    // Pelaksanaan: Tugas & Progres (F-08)
    Route::get('/tugas', [PesertaTugasController::class, 'index'])->name('tugas.index');
    Route::get('/tugas/{tugas}', [PesertaTugasController::class, 'show'])->name('tugas.show');
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

// Rute Pembimbing Magang (F-08, F-09, F-10, F-12)
Route::middleware(['auth', 'verified', 'role:pembimbing,admin'])->prefix('pembimbing')->name('pembimbing.')->group(function () {
    // Tugas & Evaluasi Progres
    Route::get('/tugas', [PembimbingTugasController::class, 'index'])->name('tugas.index');
    Route::post('/tugas', [PembimbingTugasController::class, 'store'])->name('tugas.store');
    Route::get('/tugas/{tugas}', [PembimbingTugasController::class, 'show'])->name('tugas.show');
    Route::put('/tugas/{tugas}', [PembimbingTugasController::class, 'update'])->name('tugas.update');
    Route::post('/progres/{progres}/review', [PembimbingTugasController::class, 'reviewProgres'])->name('progres.review');

    // Review Logbook Harian
    Route::get('/logbook', [PembimbingLogbookReviewController::class, 'index'])->name('logbook.index');
    Route::post('/logbook/{logbook}/review', [PembimbingLogbookReviewController::class, 'review'])->name('logbook.review');

    // Penilaian Rubrik & Penyelesaian Magang
    Route::get('/penilaian', [PembimbingPenilaianController::class, 'index'])->name('penilaian.index');
    Route::get('/penilaian/{penempatan}', [PembimbingPenilaianController::class, 'show'])->name('penilaian.show');
    Route::post('/penilaian/{penempatan}', [PembimbingPenilaianController::class, 'store'])->name('penilaian.store');
    Route::post('/penempatan/{penempatan}/selesai', [PembimbingPenilaianController::class, 'selesai'])->name('penempatan.selesai');
    Route::get('/penilaian/{penempatan}/unduh', [PembimbingPenilaianController::class, 'unduhPdf'])->name('penilaian.unduh');
});

// Rute Admin & SuperAdmin (Manajemen Kuota, Verifikasi Pendaftaran & Penempatan - F-04, F-05, F-07)
Route::middleware(['auth', 'verified', 'role:admin,superadmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/kuota', [KuotaController::class, 'index'])->name('kuota.index');
    Route::post('/kuota', [KuotaController::class, 'store'])->name('kuota.store');
    Route::put('/kuota/{kuota}', [KuotaController::class, 'update'])->name('kuota.update');
    Route::patch('/kuota/{kuota}/toggle', [KuotaController::class, 'toggleAktif'])->name('kuota.toggle');

    Route::get('/pendaftaran', [AdminPendaftaranController::class, 'index'])->name('pendaftaran.index');
    Route::get('/pendaftaran/{pendaftaran}', [AdminPendaftaranController::class, 'show'])->name('pendaftaran.show');
    Route::post('/pendaftaran/{pendaftaran}/putuskan', [AdminPendaftaranController::class, 'putuskan'])->name('pendaftaran.putuskan');

    Route::get('/waiting-list', [AdminWaitingListController::class, 'index'])->name('waiting-list.index');

    Route::get('/penempatan', [AdminPenempatanController::class, 'index'])->name('penempatan.index');
    Route::post('/penempatan/manual', [AdminPenempatanController::class, 'storeManual'])->name('penempatan.manual');
    Route::get('/penempatan/{penempatan}/surat-diterima', [AdminPenempatanController::class, 'unduhSuratDiterima'])->name('penempatan.surat-diterima');

    // Laporan & Ekspor Data CSV (F-17)
    Route::get('/laporan/pendaftaran', [LaporanController::class, 'pendaftaran'])->name('laporan.pendaftaran');
    Route::get('/laporan/penempatan', [LaporanController::class, 'penempatan'])->name('laporan.penempatan');
    Route::get('/laporan/kuota', [LaporanController::class, 'kuota'])->name('laporan.kuota');
    Route::get('/laporan/nilai', [LaporanController::class, 'nilai'])->name('laporan.nilai');
});

// Rute Khusus SuperAdmin (Bulan Magang & Slot Khusus Internal - F-06, F-11)
Route::middleware(['auth', 'verified', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/bulan', [BulanController::class, 'index'])->name('bulan.index');
    Route::post('/bulan', [BulanController::class, 'store'])->name('bulan.store');
    Route::put('/bulan/{bulan}', [BulanController::class, 'update'])->name('bulan.update');
    Route::patch('/bulan/{bulan}/toggle', [BulanController::class, 'toggleAktif'])->name('bulan.toggle');

    Route::get('/slot-khusus', [SlotKhususController::class, 'index'])->name('slot-khusus.index');
    Route::post('/slot-khusus', [SlotKhususController::class, 'store'])->name('slot-khusus.store');
    Route::put('/slot-khusus/{slot_khusus}', [SlotKhususController::class, 'update'])->name('slot-khusus.update');
    Route::patch('/slot-khusus/{slot_khusus}/toggle', [SlotKhususController::class, 'toggleAktif'])->name('slot-khusus.toggle');
});
