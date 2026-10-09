@extends('layouts.app')

@section('title', 'Verifikasi Pendaftaran Magang - Admin MagKom')

@section('content')
<div class="container" style="padding: 40px 1.5rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
        <div>
            <span class="badge" style="background: var(--blue-tint); color: var(--blue-700); font-weight: 700; padding: 4px 10px; border-radius: 9999px;">
                ● Admin Verifikator MagKom
            </span>
            <h1 style="font-size: 26px; font-weight: 800; color: var(--neutral-800); margin-top: 6px;">
                Antrean Verifikasi Pendaftaran Magang
            </h1>
            <p style="font-size: 14px; color: var(--neutral-600); margin-top: 2px;">
                Tinjau berkas pendaftaran calon peserta magang dan tetapkan keputusan penerimaan/perbaikan.
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
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">No. Pendaftaran</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Nama Peserta & Asal Sekolah</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Periode & Jurusan</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800);">Status</th>
                    <th style="padding: 14px 18px; font-size: 13px; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pendaftaranList as $item)
                    <tr style="border-bottom: 1px solid var(--neutral-200);">
                        <td style="padding: 16px 18px;">
                            <strong style="color: var(--blue-700);">#REG-{{ str_pad((string)$item->id, 5, '0', STR_PAD_LEFT) }}</strong>
                            <div style="font-size: 11px; color: var(--neutral-500);">
                                {{ $item->created_at->translatedFormat('d M Y') }}
                            </div>
                        </td>
                        <td style="padding: 16px 18px;">
                            <strong>{{ $item->user?->nama ?? $item->user?->name }}</strong>
                            <div style="font-size: 12px; color: var(--neutral-600);">
                                {{ $item->user?->profil?->institusi ?? 'Institusi Belum Diisi' }}
                            </div>
                        </td>
                        <td style="padding: 16px 18px;">
                            <div>{{ $item->bulanMagang?->nama_bulan ?? 'Periode Bulan' }}</div>
                            <div style="font-size: 12px; color: var(--neutral-600);">
                                {{ $item->kuota?->jurusan?->nama_jurusan }} ({{ $item->durasi_bulan }} Bln)
                            </div>
                        </td>
                        <td style="padding: 16px 18px;">
                            @php
                                $val = $item->status->value ?? (string)$item->status;
                            @endphp
                            @if($val === 'diterima')
                                <span style="background: var(--green-100); color: var(--green-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Diterima</span>
                            @elseif($val === 'diajukan')
                                <span style="background: var(--blue-tint); color: var(--blue-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Menunggu Verifikasi</span>
                            @elseif($val === 'waiting_list')
                                <span style="background: var(--amber-100); color: var(--amber-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Waiting List</span>
                            @elseif($val === 'perlu_perbaikan')
                                <span style="background: var(--red-100); color: var(--red-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">Perlu Perbaikan</span>
                            @else
                                <span style="background: var(--neutral-200); color: var(--neutral-700); padding: 4px 10px; border-radius: 9999px; font-size: 12px; font-weight: 600;">{{ $val }}</span>
                            @endif
                        </td>
                        <td style="padding: 16px 18px; text-align: right;">
                            <a href="{{ route('admin.pendaftaran.show', $item) }}" style="display: inline-block; padding: 6px 14px; background: var(--blue-600); color: #ffffff; text-decoration: none; border-radius: 6px; font-size: 13px; font-weight: 600;">
                                Verifikasi Berkas →
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding: 32px; text-align: center; color: var(--neutral-600);">
                            Belum ada berkas pendaftaran magang yang masuk untuk diverifikasi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($pendaftaranList->hasPages())
        <div style="margin-top: 20px;">
            {{ $pendaftaranList->links() }}
        </div>
    @endif
</div>
@endsection
