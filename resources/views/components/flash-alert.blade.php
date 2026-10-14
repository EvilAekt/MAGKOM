@php
    $successMessage = session('success') ?? session('status_keberhasilan');
    $statusMessage = session('status');
    $errorMessage = session('error');
    $warningMessage = session('warning');
    $infoMessage = session('info');
    $hasErrors = isset($errors) && $errors->any();

    // Deteksi judul dinamis berbasis rute aktif
    $isSettingOrProfile = request()->routeIs('*.profil.*') || request()->routeIs('*.pengaturan.*') || request()->routeIs('*.akun.*');
    $isTugas = request()->routeIs('*tugas*');
    $isLogbook = request()->routeIs('*logbook*');
    $isPenilaian = request()->routeIs('*penilaian*');
    $isPenempatan = request()->routeIs('*penempatan*');
    $isBackup = request()->routeIs('*backup*');
    $isHealth = request()->routeIs('*health*');

    $defaultSuccessTitle = 'Perubahan Berhasil Disimpan';
    if ($isSettingOrProfile) {
        $defaultSuccessTitle = 'Pengaturan Tersimpan';
    } elseif ($isTugas) {
        $defaultSuccessTitle = 'Data Tugas Berhasil Disimpan';
    } elseif ($isLogbook) {
        $defaultSuccessTitle = 'Logbook Berhasil Diperbarui';
    } elseif ($isPenilaian) {
        $defaultSuccessTitle = 'Penilaian Magang Tersimpan';
    } elseif ($isPenempatan) {
        $defaultSuccessTitle = 'Data Penempatan Berhasil Diperbarui';
    } elseif ($isBackup) {
        $defaultSuccessTitle = 'Operasi Cadangan Berhasil';
    } elseif ($isHealth) {
        $defaultSuccessTitle = 'Uji Sistem Berhasil';
    }

    $toastSuccessTitle = session('success_title') ?? $defaultSuccessTitle;
@endphp

@if($successMessage)
    <div class="app-toast app-alert alert-success" role="alert">
        <div class="toast-icon-badge alert-icon-badge success">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
            </svg>
        </div>
        <div class="toast-content alert-body">
            <div class="toast-title alert-title">{{ $toastSuccessTitle }}</div>
            <div class="toast-desc alert-desc">{{ $successMessage }}</div>
        </div>
        <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
@endif

@php
    $statusTitle = session('status_title') ?? 'Informasi Sistem';
    if ($statusMessage === 'verification-link-sent') {
        $statusMessage = 'Tautan verifikasi baru telah berhasil dikirimkan ke alamat email Anda. Silakan periksa kotak masuk atau folder spam.';
        $statusTitle = 'Tautan Verifikasi Terkirim';
    }
@endphp
@if($statusMessage && $statusMessage !== $successMessage)
    <div class="app-toast app-alert alert-info" role="alert">
        <div class="toast-icon-badge alert-icon-badge info">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div class="toast-content alert-body">
            <div class="toast-title alert-title">{{ $statusTitle }}</div>
            <div class="toast-desc alert-desc">{{ $statusMessage }}</div>
        </div>
        <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
@endif

@if($errorMessage)
    <div class="app-toast app-alert alert-error alert-danger" role="alert">
        <div class="toast-icon-badge alert-icon-badge error">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>
        <div class="toast-content alert-body">
            <div class="toast-title alert-title">{{ session('error_title') ?? 'Terjadi Kesalahan' }}</div>
            <div class="toast-desc alert-desc">{{ $errorMessage }}</div>
        </div>
        <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
@endif

@if($warningMessage)
    <div class="app-toast app-alert alert-warning" role="alert">
        <div class="toast-icon-badge alert-icon-badge warning">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
        </div>
        <div class="toast-content alert-body">
            <div class="toast-title alert-title">{{ session('warning_title') ?? 'Perhatian' }}</div>
            <div class="toast-desc alert-desc">{{ $warningMessage }}</div>
        </div>
        <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
@endif

@if($infoMessage)
    <div class="app-toast app-alert alert-info" role="alert">
        <div class="toast-icon-badge alert-icon-badge info">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="16" x2="12" y2="12"></line>
                <line x1="12" y1="8" x2="12.01" y2="8"></line>
            </svg>
        </div>
        <div class="toast-content alert-body">
            <div class="toast-title alert-title">{{ session('info_title') ?? 'Informasi Sistem' }}</div>
            <div class="toast-desc alert-desc">{{ $infoMessage }}</div>
        </div>
        <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
@endif

@if($hasErrors)
    <div class="app-toast app-alert alert-error alert-danger" role="alert">
        <div class="toast-icon-badge alert-icon-badge error">
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
        </div>
        <div class="toast-content alert-body">
            <div class="toast-title alert-title">Periksa Kembali Isian Formulir:</div>
            <div class="toast-desc alert-desc">
                <ul style="margin: 4px 0 0 16px; padding: 0; list-style-type: disc;">
                    @foreach($errors->all() as $error)
                        <li style="margin-bottom: 2px;">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        <button type="button" class="toast-close-btn alert-close-btn" onclick="dismissToast(this)" aria-label="Tutup notifikasi">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
        </button>
    </div>
@endif
