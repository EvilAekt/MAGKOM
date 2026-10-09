@extends('layouts.app')

@section('title', 'Manajemen Tugas Bimbingan - Pembimbing MagKom')

@section('content')
<div class="container" style="padding: 40px 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge" style="background: var(--blue-tint); color: var(--blue-700); font-weight: 700; padding: 4px 10px; border-radius: 9999px;">
                ● Pembimbing Lapangan MagKom
            </span>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--neutral-800); margin-top: 6px;">
                Daftar Tugas & Progres Peserta Bimbingan
            </h1>
            <p style="font-size: 14px; color: var(--neutral-600); margin-top: 2px;">
                Pantau progres penugasan, berikan feedback hasil pengerjaan, dan evaluasi capaian peserta.
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
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Judul Tugas</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Peserta Magang</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Tenggat Waktu</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Status</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tugasList as $tugas)
                    <tr style="border-bottom: 1px solid var(--neutral-200);">
                        <td style="padding: 16px 18px;">
                            <strong>{{ $tugas->judul }}</strong>
                            <div style="font-size: 12px; color: var(--neutral-600); max-width: 300px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $tugas->deskripsi }}
                            </div>
                        </td>
                        <td style="padding: 16px 18px;">
                            <strong>{{ $tugas->penempatan?->peserta?->nama ?? $tugas->penempatan?->peserta?->name }}</strong>
                            <div style="font-size: 12px; color: var(--neutral-600);">
                                Bidang: {{ $tugas->penempatan?->bidang?->nama_bidang ?? '-' }}
                            </div>
                        </td>
                        <td style="padding: 16px 18px; font-size: 13px;">
                            {{ \Carbon\Carbon::parse($tugas->deadline)->translatedFormat('d M Y') }}
                        </td>
                        <td style="padding: 16px 18px;">
                            @php
                                $val = $tugas->status->value ?? (string)$tugas->status;
                            @endphp
                            @if($val === 'selesai')
                                <span style="background: var(--green-100); color: var(--green-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Selesai</span>
                            @elseif($val === 'proses')
                                <span style="background: var(--blue-tint); color: var(--blue-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Sedang Dikerjakan</span>
                            @else
                                <span style="background: var(--neutral-200); color: var(--neutral-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Belum Dimulai</span>
                            @endif
                        </td>
                        <td style="padding: 16px 18px; text-align: right;">
                            <a href="{{ route('pembimbing.tugas.show', $tugas) }}" style="display: inline-block; padding: 6px 14px; background: var(--blue-600); color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600;">
                                Detail & Review →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 32px; text-align: center; color: var(--neutral-600);">
                            Belum ada tugas magang yang diberikan kepada peserta bimbingan.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($tugasList->hasPages())
        <div style="margin-top: 20px;">
            {{ $tugasList->links() }}
        </div>
    @endif
</div>
@endsection
