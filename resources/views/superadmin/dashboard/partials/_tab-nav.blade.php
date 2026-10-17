<div class="dashboard-tab-bar" role="tablist" aria-label="Area dashboard">
    <button type="button" class="tab-btn active" id="btn-tab-ringkasan" data-tab="ringkasan" role="tab" aria-selected="true" aria-controls="tab-ringkasan">
        <x-sa.icon name="activity" :size="15" /><span>Ringkasan Utama</span>
    </button>
    <button type="button" class="tab-btn" id="btn-tab-seleksi" data-tab="seleksi" role="tab" aria-selected="false" aria-controls="tab-seleksi" tabindex="-1">
        <x-sa.icon name="file-text" :size="15" /><span>Seleksi & Kuota</span>
        @if($menungguVerifikasi > 0)<span class="tab-badge">{{ $menungguVerifikasi }}</span>@endif
    </button>
    <button type="button" class="tab-btn" id="btn-tab-bimbingan" data-tab="bimbingan" role="tab" aria-selected="false" aria-controls="tab-bimbingan" tabindex="-1">
        <x-sa.icon name="users" :size="15" /><span>Pelaksanaan & Bimbingan</span>
        @if($totalLogbookPending > 0)<span class="tab-badge">{{ $totalLogbookPending }}</span>@endif
    </button>
    <button type="button" class="tab-btn" id="btn-tab-sistem" data-tab="sistem" role="tab" aria-selected="false" aria-controls="tab-sistem" tabindex="-1">
        <x-sa.icon name="settings" :size="15" /><span>Tata Kelola Sistem & Laporan</span>
    </button>
</div>
