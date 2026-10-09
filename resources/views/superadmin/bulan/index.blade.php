@extends('layouts.app')

@section('title', 'Kelola Periode Bulan Magang - SuperAdmin MagKom')

@section('content')
<div class="container" style="padding: 40px 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge" style="background: var(--blue-tint); color: var(--blue-700); font-weight: 700; padding: 4px 10px; border-radius: 9999px;">
                ● SuperAdmin MagKom
            </span>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--neutral-800); margin-top: 6px;">
                Manajemen Periode Bulan Magang
            </h1>
            <p style="font-size: 14px; color: var(--neutral-600); margin-top: 2px;">
                Atur jadwal buka/tutup pendaftaran, status ketersediaan publik, dan alokasi kuota magang.
            </p>
        </div>

        <div style="display: flex; gap: 12px; align-items: center;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn" style="background: var(--neutral-200); color: var(--neutral-800); border: 1px solid var(--neutral-400); padding: 8px 16px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer;">
                    Keluar (Logout)
                </button>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div style="padding: 14px 18px; background: var(--green-100); border: 1px solid var(--green-600); color: var(--green-700); border-radius: 8px; margin-bottom: 20px; font-size: 14px;">
            {{ session('success') }}
        </div>
    @endif

    <div style="background: #ffffff; border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400);">
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">ID</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Periode Bulan</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Jadwal Pendaftaran</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Status Override</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Status Aktif</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bulanList as $bulan)
                    <tr style="border-bottom: 1px solid var(--neutral-200);">
                        <td style="padding: 16px 18px; font-weight: 700; color: var(--blue-700);">#{{ $bulan->id }}</td>
                        <td style="padding: 16px 18px;">
                            <strong>{{ $bulan->nama_bulan ?? \Carbon\Carbon::parse($bulan->bulan_mulai)->translatedFormat('F Y') }}</strong>
                            <div style="font-size: 12px; color: var(--neutral-600);">
                                Tanggal Mulai: {{ \Carbon\Carbon::parse($bulan->bulan_mulai)->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td style="padding: 16px 18px; font-size: 13px; color: var(--neutral-700);">
                            Buka: <strong>{{ \Carbon\Carbon::parse($bulan->tgl_buka)->translatedFormat('d M Y') }}</strong><br>
                            Tutup: <strong>{{ \Carbon\Carbon::parse($bulan->tgl_tutup)->translatedFormat('d M Y') }}</strong>
                        </td>
                        <td style="padding: 16px 18px;">
                            @if($bulan->override_status)
                                <span style="background: var(--blue-tint); color: var(--blue-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                                    {{ ucfirst($bulan->override_status->value ?? (string)$bulan->override_status) }}
                                </span>
                            @else
                                <span style="color: var(--neutral-500); font-size: 12px;">Otomatis (Sesuai Jadwal)</span>
                            @endif
                        </td>
                        <td style="padding: 16px 18px;">
                            @if($bulan->aktif)
                                <span style="background: var(--green-100); color: var(--green-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                                    Aktif
                                </span>
                            @else
                                <span style="background: var(--neutral-200); color: var(--neutral-600); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 32px; text-align: center; color: var(--neutral-600);">
                            Belum ada periode bulan magang yang terdaftar.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bulanList->hasPages())
        <div style="margin-top: 20px;">
            {{ $bulanList->links() }}
        </div>
    @endif
</div>
@endsection
