@php
    $selJurusan = $selectedJurusan ?? null;
    $selBulan = $selectedBulan ?? null;
    $selBidang = $selectedBidang ?? null;
    $tipe = $initialTipe ?? request('tipe', 'mandiri');
    $jumlah = $initialJumlah ?? (int) request('jumlah', 1);

    $jurusanNama = $selJurusan?->nama_jurusan;
    $jurusanKategori = $selJurusan ? strtoupper((string) ($selJurusan->kategori?->value ?? $selJurusan->kategori)) : null;
    $bidangNama = $selBidang?->nama_bidang;
    if (! $bidangNama && $selJurusan && $selJurusan->bidang && $selJurusan->bidang->isNotEmpty()) {
        $bidangNama = $selJurusan->bidang->pluck('nama_bidang')->implode(', ');
    }

    if (! $jurusanNama && request('jurusan')) {
        $jurModel = \App\Models\Jurusan::find(request('jurusan'));
        if ($jurModel) {
            $jurusanNama = $jurModel->nama_jurusan;
            $jurusanKategori = strtoupper((string) ($jurModel->kategori?->value ?? $jurModel->kategori));
            if (! $bidangNama && $jurModel->bidang && $jurModel->bidang->isNotEmpty()) {
                $bidangNama = $jurModel->bidang->pluck('nama_bidang')->implode(', ');
            }
        }
    }

    if (! $bidangNama && request('bidang')) {
        $bidModel = \App\Models\Bidang::find(request('bidang'));
        if ($bidModel) {
            $bidangNama = $bidModel->nama_bidang;
        }
    }

    $bulanLabel = $selBulan
        ? \Carbon\CarbonImmutable::parse($selBulan->bulan_mulai)->locale('id')->translatedFormat('F Y')
        : null;

    $sisaKursi = $selBulan ? (int) ($selBulan->kuota_tersedia ?? 4) : 4;

    if (! $bulanLabel && request('bulan')) {
        $bulanModel = \App\Models\BulanMagang::find(request('bulan'));
        if ($bulanModel) {
            $bulanLabel = \Carbon\CarbonImmutable::parse($bulanModel->bulan_mulai)->locale('id')->translatedFormat('F Y');
            $sisaKursi = (int) ($bulanModel->kuota_tersedia ?? 4);
        } else {
            $bulanLabel = 'Bulan ' . request('bulan');
        }
    }

    $bulanLabel = $bulanLabel ?? 'Sesuai Pilihan';
    $isTersedia = $sisaKursi > 0;
    $tipeText = ($tipe === 'kelompok') ? 'Tim (' . $jumlah . ' Orang)' : 'Individu (1 Orang)';
@endphp

<div class="landing-pilihan-card" role="region" aria-label="Ringkasan Pilihan Magang Anda">
  {{-- Header: Icon + Title + Type Pill --}}
  <div class="lpc-header">
    <div class="lpc-title-group">
      <span class="lpc-icon-box" aria-hidden="true">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
          <circle cx="12" cy="12" r="10"></circle>
          <polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"></polygon>
        </svg>
      </span>
      <span class="lpc-title">Pilihan Magang Anda</span>
    </div>
    <span class="lpc-pill-type">
      {{ $tipeText }}
    </span>
  </div>

  {{-- Baris Jurusan & Penempatan (Terpisah Bawah) --}}
  <div class="lpc-jurusan-title">
    @if($jurusanNama)
      <span>{{ $jurusanNama }}</span>
      @if($jurusanKategori)
        <span>({{ $jurusanKategori }})</span>
      @endif
    @elseif(request('jurusan'))
      <span>Jurusan Terpilih</span>
    @endif
  </div>
  @if($bidangNama)
    <div class="lpc-penempatan-sub">
      <span class="lpc-penempatan-label">Penempatan:</span>
      <span class="lpc-penempatan-value">{{ $bidangNama }}</span>
    </div>
  @endif

  {{-- Baris Periode Mulai & Status Kursi --}}
  <div class="lpc-period-row">
    <div class="lpc-period-label">
      Mulai Periode: <strong class="lpc-period-value">{{ $bulanLabel }}</strong>
    </div>
    @if($isTersedia)
      <span class="lpc-badge-available">
        <span class="lpc-dot-available" aria-hidden="true"></span>
        <span>Tersedia ({{ $sisaKursi }} Kursi)</span>
      </span>
    @else
      <span class="lpc-badge-waiting">
        <span class="lpc-dot-waiting" aria-hidden="true"></span>
        <span>Waiting List</span>
      </span>
    @endif
  </div>

  {{-- Dashed Separator --}}
  <hr class="lpc-dashed-divider" aria-hidden="true">

  {{-- Tombol Ubah Pilihan --}}
  <div class="lpc-action-row">
    <a href="{{ url('/#kuota') }}" class="lpc-btn-change lpc-link-change" role="button">
      <span>Ubah Pilihan</span>
      <span aria-hidden="true">&#x21bb;</span>
    </a>
  </div>
</div>
