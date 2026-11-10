{{-- SECTION 2: STANDAR GENTELELLA TILE-STAT 4-GRID (DS-MagKom) --}}
@php
    /** @var int $total24h */
    /** @var float|int|string $growth24h */
    /** @var int $mutasiData */
    /** @var float|int|string $mutasiPercentage */
    /** @var int $sesiAuth */
    /** @var int $peringatanKritis */

    $growthNum = (float) $growth24h;
    $growthPositive = $growthNum >= 0;
    $growthLabel = rtrim(rtrim(number_format(abs($growthNum), 1, '.', ''), '0'), '.');
@endphp
<section class="tiles-grid" style="margin-bottom: 1.5rem;" aria-label="Ringkasan Audit Log">
    <!-- 1. Total Aktivitas 24H -->
    <x-gentelella.tile-stat
        title="Total Log (24 Jam)"
        value="{{ number_format($total24h) }}"
        color="blue"
        trendType="{{ $growthPositive ? 'success' : 'danger' }}"
        trendDirection="{{ $growthPositive ? 'up' : 'down' }}"
        trendText="{{ $growthPositive ? '+' : '-' }}{{ $growthLabel }}%"
        subText="Aliran Aktivitas 24 Jam Terakhir"
        :sparkBars="[30, 45, 60, 75, 85, 95]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>

    <!-- 2. Mutasi Data (CRUD Changes) -->
    <x-gentelella.tile-stat
        title="Mutasi Data"
        value="{{ number_format($mutasiData) }}"
        color="teal"
        trendType="success"
        trendDirection="up"
        trendText="{{ $mutasiPercentage }}%"
        subText="Perubahan Nilai & Data State"
        :sparkBars="[25, 40, 55, 70, 80, 90]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <ellipse cx="12" cy="5" rx="9" ry="3"></ellipse>
                <path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path>
                <path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>

    <!-- 3. Sesi Autentikasi -->
    <x-gentelella.tile-stat
        title="Sesi Autentikasi"
        value="{{ number_format($sesiAuth) }}"
        color="amber"
        trendType="warning"
        trendDirection="up"
        trendText="Auth"
        subText="Login, Logout & Sesi Akun"
        :sparkBars="[20, 35, 50, 65, 80, 85]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M16 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"></path>
                <circle cx="8.5" cy="7" r="4"></circle>
                <polyline points="17 11 19 13 23 9"></polyline>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>

    <!-- 4. Peringatan Kritis -->
    <x-gentelella.tile-stat
        title="Peringatan Kritis"
        value="{{ number_format($peringatanKritis) }}"
        color="{{ $peringatanKritis > 0 ? 'red' : 'purple' }}"
        trendType="{{ $peringatanKritis > 0 ? 'danger' : 'info' }}"
        trendDirection="{{ $peringatanKritis > 0 ? 'up' : 'down' }}"
        trendText="{{ $peringatanKritis > 0 ? 'Perlu Aksi' : 'Aman' }}"
        subText="{{ $peringatanKritis > 0 ? 'Perlu investigasi segera' : 'Zero unresolved alert' }}"
        :sparkBars="[15, 20, 25, 30, 35, 40]">
        <x-slot:icon>
            <svg width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"></path>
                <line x1="12" y1="9" x2="12" y2="13"></line>
                <line x1="12" y1="17" x2="12.01" y2="17"></line>
            </svg>
        </x-slot:icon>
    </x-gentelella.tile-stat>
</section>
