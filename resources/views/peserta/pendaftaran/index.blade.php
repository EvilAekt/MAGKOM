@extends('layouts.dashboard')

@section('title', 'Pendaftaran Magang - Peserta MagKom Sleman')
@section('page_title', 'Pendaftaran & Riwayat Magang')
@section('page_description', 'Kelola pengajuan pendaftaran, lengkapi berkas persyaratan, dan pantau status seleksi magang Anda.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Pendaftaran Magang</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- 1. KARTU PENDAFTARAN AKTIF (Jika sudah ada pengajuan aktif) -->
    @if($pendaftaranAktif)
        <div style="background: var(--neutral-0); border: 2px solid var(--blue-600); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; margin-bottom: 1rem;">
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.375rem;">
                        <span style="font-size: 0.8125rem; font-weight: 700; color: var(--blue-700); text-transform: uppercase; letter-spacing: 0.05em;">Pengajuan Aktif</span>
                        @php
                            $status = $pendaftaranAktif->status;
                            $badgeClass = match($status->value) {
                                'berkas_belum_lengkap' => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-800)'],
                                'diajukan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-700)'],
                                'perlu_perbaikan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-700)'],
                                'waiting_list' => ['bg' => 'var(--blue-tint)', 'color' => 'var(--blue-700)'],
                                'diterima' => ['bg' => 'var(--green-100)', 'color' => 'var(--green-700)'],
                                default => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-800)'],
                            };
                        @endphp
                        <span class="role-chip-badge" style="background: {{ $badgeClass['bg'] }}; color: {{ $badgeClass['color'] }}; font-weight: 700;">
                            {{ $status->label() }}
                        </span>
                    </div>
                    <h2 style="font-size: 1.25rem; font-weight: 800; color: var(--neutral-800); margin: 0 0 0.25rem 0;">
                        {{ $pendaftaranAktif->nomor_pendaftaran }}
                    </h2>
                    <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0;">
                        Periode Mulai: <strong>{{ $pendaftaranAktif->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}</strong> &bull; 
                        Jurusan: <strong>{{ $pendaftaranAktif->kuota?->jurusan?->nama_jurusan }}</strong> &bull; 
                        Durasi: <strong>{{ $pendaftaranAktif->durasi_bulan }} Bulan</strong>
                    </p>
                </div>

                <a href="{{ route('peserta.pendaftaran.detail', $pendaftaranAktif) }}" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; padding: 0.625rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; box-shadow: var(--shadow-subtle);">
                    <span>Buka Detail & Kelola Berkas</span>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                </a>
            </div>

            @if($status->value === 'berkas_belum_lengkap')
                <div style="background: var(--neutral-100); border-left: 4px solid var(--amber-500); border-radius: 4px; padding: 0.75rem 1rem; font-size: 0.8125rem; color: var(--neutral-700);">
                    Pendaftaran Anda masih berstatus draf. Silakan unggah seluruh dokumen persyaratan wajib sebelum batas waktu penutupan pendaftaran.
                </div>
            @elseif($status->value === 'perlu_perbaikan')
                <div style="background: var(--amber-100); border-left: 4px solid var(--amber-600); border-radius: 4px; padding: 0.75rem 1rem; font-size: 0.8125rem; color: var(--amber-800);">
                    <strong>Catatan Tim Verifikator:</strong> {{ $pendaftaranAktif->catatan ?? 'Terdapat berkas yang perlu diperbaiki. Silakan periksa detail pengajuan.' }}
                </div>
            @elseif($status->value === 'waiting_list')
                <div style="background: var(--blue-tint); border-left: 4px solid var(--blue-600); border-radius: 4px; padding: 0.75rem 1rem; font-size: 0.8125rem; color: var(--blue-700);">
                    Pengajuan Anda berada di antrean <strong>Waiting List</strong> karena kuota reguler jurusan telah penuh.
                </div>
            @elseif($status->value === 'diterima')
                <div style="background: var(--green-100); border-left: 4px solid var(--green-600); border-radius: 4px; padding: 0.75rem 1rem; font-size: 0.8125rem; color: var(--green-700);">
                    Selamat! Anda telah <strong>Diterima</strong> magang di Dinas Kominfo Sleman. Silakan buka halaman detail untuk mengunduh Surat Keterangan Diterima Magang resmi.
                </div>
            @endif
        </div>

    <!-- 2. KARTU PENDAFTARAN BARU (Jika belum ada pendaftaran aktif) -->
    @else
        @if(!$profil || !$profil->institusi || !$profil->nim_nisn)
            <!-- Peringatan: Profil belum lengkap -->
            <div style="background: var(--amber-100); border: 1px solid var(--amber-600); border-radius: 12px; padding: 1.25rem 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.875rem;">
                    <div style="width: 40px; height: 40px; background: var(--amber-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: var(--amber-800); flex-shrink: 0;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                            <line x1="12" y1="9" x2="12" y2="13"></line>
                            <line x1="12" y1="17" x2="12.01" y2="17"></line>
                        </svg>
                    </div>
                    <div>
                        <h3 style="font-size: 1rem; font-weight: 700; color: var(--amber-900); margin: 0 0 0.25rem 0;">Lengkapi Biodata Profil Terlebih Dahulu</h3>
                        <p style="font-size: 0.8125rem; color: var(--amber-800); margin: 0;">
                            Sebelum mengajukan pendaftaran magang, Anda wajib melengkapi data institusi, jurusan, dan NIM/NISN pada profil biodata.
                        </p>
                    </div>
                </div>
                <a href="{{ route('peserta.profil.show') }}" style="background: var(--blue-600); color: #FFFFFF; text-decoration: none; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; white-space: nowrap;">
                    Lengkapi Profil Sekarang
                </a>
            </div>
        @else
            <!-- Form Pemilihan Periode & Kuota Baru -->
            <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
                <div style="border-bottom: 1px solid var(--neutral-400); padding-bottom: 1rem; margin-bottom: 1.25rem;">
                    <h2 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.25rem 0;">Mulai Pendaftaran Magang Baru</h2>
                    <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0;">
                        Pilih periode magang dan jurusan yang sesuai dengan keahlian Anda untuk memulai pengajuan berkas.
                    </p>
                </div>

                @if($bulanAktifList->isEmpty())
                    <div style="text-align: center; padding: 2rem 1rem; color: var(--neutral-600);">
                        <p style="margin: 0; font-size: 0.9375rem;">Saat ini belum ada periode magang yang membuka pendaftaran baru. Silakan pantau secara berkala.</p>
                    </div>
                @else
                    <form method="POST" action="{{ route('peserta.pendaftaran.store') }}" style="display: flex; flex-direction: column; gap: 1.25rem;">
                        @csrf
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1rem;">
                            <!-- Pilih Periode Bulan -->
                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="bulan_magang_id" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Periode Bulan Magang <span style="color: var(--red-600); font-weight: 700;">*</span>
                                </label>
                                <select id="bulan_magang_id" name="bulan_magang_id" required style="height: 44px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);" onchange="updateKuotaOptions()">
                                    <option value="">-- Pilih Periode Bulan --</option>
                                    @foreach($bulanAktifList as $b)
                                        <option value="{{ $b->id }}" {{ old('bulan_magang_id') == $b->id ? 'selected' : '' }}>
                                            {{ $b->bulan_mulai?->translatedFormat('F Y') }} (Buka s/d {{ \Carbon\Carbon::parse($b->tgl_tutup)->translatedFormat('d M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Pilih Kuota Jurusan -->
                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="kuota_id" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Jurusan / Bidang Keahlian <span style="color: var(--red-600); font-weight: 700;">*</span>
                                </label>
                                <select id="kuota_id" name="kuota_id" required style="height: 44px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                                    <option value="">-- Pilih Periode Terlebih Dahulu --</option>
                                </select>
                            </div>

                            <!-- Pilih Durasi -->
                            <div style="display: flex; flex-direction: column; gap: 0.375rem;">
                                <label for="durasi_bulan" style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">
                                    Rencana Durasi Magang <span style="color: var(--red-600); font-weight: 700;">*</span>
                                </label>
                                <select id="durasi_bulan" name="durasi_bulan" required style="height: 44px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                                    <option value="1" {{ old('durasi_bulan') == 1 ? 'selected' : '' }}>1 Bulan</option>
                                    <option value="2" {{ old('durasi_bulan') == 2 ? 'selected' : '' }}>2 Bulan</option>
                                    <option value="3" {{ old('durasi_bulan', 3) == 3 ? 'selected' : '' }}>3 Bulan (Standar)</option>
                                    <option value="4" {{ old('durasi_bulan') == 4 ? 'selected' : '' }}>4 Bulan</option>
                                    <option value="5" {{ old('durasi_bulan') == 5 ? 'selected' : '' }}>5 Bulan</option>
                                    <option value="6" {{ old('durasi_bulan') == 6 ? 'selected' : '' }}>6 Bulan</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: flex; justify-content: flex-end; margin-top: 0.5rem;">
                            <button type="submit" style="height: 44px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s; display: inline-flex; align-items: center; gap: 0.5rem;">
                                <span>Buat Draf & Unggah Berkas</span>
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="5" y1="12" x2="19" y2="12"></line>
                                    <polyline points="12 5 19 12 12 19"></polyline>
                                </svg>
                            </button>
                        </div>
                    </form>

                    <script>
                        const bulanData = @json($bulanAktifList);
                        const oldKuotaId = "{{ old('kuota_id') }}";

                        function updateKuotaOptions() {
                            const bulanSelect = document.getElementById('bulan_magang_id');
                            const kuotaSelect = document.getElementById('kuota_id');
                            const selectedBulanId = bulanSelect.value;

                            kuotaSelect.innerHTML = '<option value="">-- Pilih Jurusan / Kuota --</option>';

                            if (!selectedBulanId) {
                                kuotaSelect.innerHTML = '<option value="">-- Pilih Periode Terlebih Dahulu --</option>';
                                return;
                            }

                            const bulan = bulanData.find(b => b.id == selectedBulanId);
                            if (bulan && bulan.kuota) {
                                bulan.kuota.forEach(k => {
                                    const sisa = Math.max(0, k.jumlah - k.terisi);
                                    const opt = document.createElement('option');
                                    opt.value = k.id;
                                    opt.text = `${k.jurusan ? k.jurusan.nama_jurusan : 'Umum'} (Sisa Kuota: ${sisa} kursi)`;
                                    if (oldKuotaId && oldKuotaId == k.id) {
                                        opt.selected = true;
                                    }
                                    kuotaSelect.appendChild(opt);
                                });
                            }
                        }

                        document.addEventListener('DOMContentLoaded', function() {
                            if (document.getElementById('bulan_magang_id').value) {
                                updateKuotaOptions();
                            }
                        });
                    </script>
                @endif
            </div>
        @endif
    @endif

    <!-- 3. TABEL RIWAYAT PENDAFTARAN -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">Riwayat Pengajuan Pendaftaran</h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">Total: {{ $pendaftaranList->count() }} Pengajuan</span>
        </div>

        @if($pendaftaranList->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--blue-tint); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--blue-700);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                        <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Riwayat Magang</h4>
                <p style="font-size: 0.875rem; max-width: 480px; margin: 0 auto 1.5rem auto;">
                    Anda belum memiliki riwayat pendaftaran magang di Dinas Kominfo Sleman.
                </p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Periode Bulan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Durasi</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Detail</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendaftaranList as $item)
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 600; color: var(--blue-700);">
                                    {{ $item->nomor_pendaftaran }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800);">
                                    {{ $item->bulanMagang?->bulan_mulai?->translatedFormat('F Y') }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $item->kuota?->jurusan?->nama_jurusan }}
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-700);">
                                    {{ $item->durasi_bulan }} Bulan
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    @php
                                        $s = $item->status;
                                        $badgeStyle = match($s->value) {
                                            'berkas_belum_lengkap' => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-800)'],
                                            'diajukan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-700)'],
                                            'perlu_perbaikan' => ['bg' => 'var(--amber-200)', 'color' => 'var(--amber-700)'],
                                            'waiting_list' => ['bg' => 'var(--blue-tint)', 'color' => 'var(--blue-700)'],
                                            'diterima' => ['bg' => 'var(--green-100)', 'color' => 'var(--green-700)'],
                                            'ditolak' => ['bg' => 'var(--red-200)', 'color' => 'var(--red-700)'],
                                            default => ['bg' => 'var(--neutral-200)', 'color' => 'var(--neutral-700)'],
                                        };
                                    @endphp
                                    <span class="role-chip-badge" style="background: {{ $badgeStyle['bg'] }}; color: {{ $badgeStyle['color'] }}; font-weight: 600;">
                                        {{ $s->label() }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <a href="{{ route('peserta.pendaftaran.detail', $item) }}" style="padding: 0.375rem 0.75rem; background: var(--blue-600); color: #FFFFFF; text-decoration: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem;">
                                        Lihat Berkas
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection
