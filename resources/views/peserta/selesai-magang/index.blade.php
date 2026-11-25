@extends('layouts.peserta')

@section('title', __('Selesai Magang - MagKom Sleman'))
@section('page_title', __('Selesai Magang'))

@section('content')
<div class="peserta-selesai-container" style="max-width: 1040px; margin: 0 auto; padding-bottom: 2.5rem;">
    
    {{-- Header Banner Info Program --}}
    <div style="background: linear-gradient(135deg, #01347C 0%, #003566 100%); border-radius: 16px; padding: 28px 32px; color: #FFFFFF; margin-bottom: 1.75rem; box-shadow: 0 4px 20px rgba(0, 53, 102, 0.15);">
        <div style="display: flex; align-items: center; gap: 8px; font-size: 0.8rem; font-weight: 800; letter-spacing: 0.05em; text-transform: uppercase; color: #FFC300; margin-bottom: 8px;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="8" r="7"></circle><polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline></svg>
            <span>PROGRAM KEDINASAN MAGKOM SLEMAN</span>
        </div>
        <h1 class="peserta-selesai-hero-title" style="margin: 0 0 8px; font-size: 1.5rem; font-weight: 800; line-height: 1.3;">Status Kelulusan &amp; Surat Selesai Magang</h1>
        <p style="margin: 0; font-size: 0.925rem; color: #E2E8F0; max-width: 680px; line-height: 1.5;">
            Pantau status penyelesaian program magang Anda dan unduh dokumen resmi Surat Keterangan Selesai Magang yang diterbitkan oleh Dinas Komunikasi dan Informatika Kabupaten Sleman.
        </p>
    </div>

    {{-- Grid 2 Kolom: Detail Kedinasan & Status Dokumen --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem;">
        
        {{-- Kolom 1: Kartu Rincian Peserta & Penempatan --}}
        <div style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #EFF6FF; color: #1D4ED8; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #003566;">Data Diri &amp; Penempatan</h3>
            </div>

            <div style="display: flex; flex-direction: column; gap: 14px;">
                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Nama Lengkap Peserta</span>
                    <strong style="font-size: 0.95rem; color: #0F172A;">{{ auth()->user()->nama }}</strong>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Institusi / Universitas / Sekolah</span>
                    <span style="font-size: 0.9rem; color: #1E293B;">{{ auth()->user()->profil?->institusi ?? 'Institusi Belum Dilengkapi' }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Unit Kerja / Bidang Magang</span>
                    <span style="font-size: 0.9rem; color: #1E293B;">{{ $penempatan?->bidang?->nama_bidang ?? 'Belum Ditentukan' }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Pembimbing Lapangan</span>
                    <span style="font-size: 0.9rem; color: #1E293B;">{{ $penempatan?->pembimbing?->nama ?? 'Belum Ditentukan' }}</span>
                </div>

                <div>
                    <span style="display: block; font-size: 0.75rem; font-weight: 700; color: #64748B; text-transform: uppercase;">Status Program Kedinasan</span>
                    @if($isSelesai)
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; background: #ECFDF5; border: 1px solid #A7F3D0; color: #047857; font-size: 0.825rem; font-weight: 800; margin-top: 4px;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>PROGRAM MAGANG TUNTAS (SELESAI)</span>
                        </span>
                    @elseif($penempatan)
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; background: #EFF6FF; border: 1px solid #BFDBFE; color: #1D4ED8; font-size: 0.825rem; font-weight: 800; margin-top: 4px;">
                            <span>SEDANG BERLANGSUNG</span>
                        </span>
                    @else
                        <span style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 12px; border-radius: 9999px; background: #F1F5F9; border: 1px solid #CBD5E1; color: #64748B; font-size: 0.825rem; font-weight: 800; margin-top: 4px;">
                            <span>BELUM ADA PENEMPATAN</span>
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kolom 2: Kartu Unduh Dokumen Surat Selesai Magang --}}
        <div style="background: #FFFFFF; border: 1.5px solid #E2E8F0; border-radius: 14px; padding: 24px; box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04); display: flex; flex-direction: column;">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 16px; padding-bottom: 12px; border-bottom: 1px solid #F1F5F9;">
                <div style="width: 36px; height: 36px; border-radius: 10px; background: #FEF3C7; color: #B45309; display: flex; align-items: center; justify-content: center;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #003566;">Surat Keterangan Selesai Magang</h3>
            </div>

            @if($isSelesai && $hasSurat)
                {{-- Status Tersedia --}}
                <div style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                    <div style="padding: 16px; border-radius: 12px; background: #F0FDF4; border: 1.5px solid #BBF7D0; margin-bottom: 20px;">
                        <div style="display: flex; align-items: center; gap: 8px; color: #166534; font-size: 0.95rem; font-weight: 800; margin-bottom: 4px;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span>Dokumen Resmi Telah Terbit</span>
                        </div>
                        <p style="margin: 0 0 8px; font-size: 0.85rem; color: #15803D; line-height: 1.4;">
                            Surat keterangan selesai magang individual Anda telah diunggah dan diverifikasi secara resmi oleh administrator dinas.
                        </p>
                        <div style="font-size: 0.775rem; color: #166534; font-weight: 600;">
                            Tanggal Diterbitkan: <strong>{{ $penempatan->tgl_upload_surat_selesai?->translatedFormat('d F Y, H:i') ?? '-' }} WIB</strong>
                        </div>
                    </div>

                    <a href="{{ route('peserta.selesai-magang.unduh') }}" 
                       class="btn-create-task"
                       style="display: flex; align-items: center; justify-content: center; gap: 10px; padding: 14px 20px; font-size: 0.95rem; font-weight: 800; text-decoration: none; border-radius: 10px; background: #003566; color: #FFFFFF; box-shadow: 0 4px 12px rgba(0, 53, 102, 0.2);">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                        <span>Unduh Surat Keterangan Selesai (PDF)</span>
                    </a>
                </div>
            @elseif($isSelesai && !$hasSurat)
                {{-- Status Selesai tetapi Admin Belum Upload File --}}
                <div style="flex: 1; padding: 24px 16px; text-align: center; background: #FFFBEB; border: 1.5px dashed #FCD34D; border-radius: 12px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #FEF3C7; color: #D97706; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 14 14"></polyline></svg>
                    </div>
                    <h4 style="margin: 0 0 6px; font-size: 0.95rem; font-weight: 800; color: #92400E;">Dokumen Sedang Dalam Proses Penerbitan</h4>
                    <p style="margin: 0; font-size: 0.85rem; color: #B45309; line-height: 1.5;">
                        Program magang Anda telah selesai. Berkas Surat Keterangan Selesai Magang individual sedang dalam proses penandatanganan dan pengunggahan oleh administrator Dinas Kominfo Sleman.
                    </p>
                </div>
            @else
                {{-- Status Belum Selesai --}}
                <div style="flex: 1; padding: 24px 16px; text-align: center; background: #F8FAFC; border: 1.5px dashed #CBD5E1; border-radius: 12px;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; margin: 0 auto 12px;">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                    </div>
                    <h4 style="margin: 0 0 6px; font-size: 0.95rem; font-weight: 800; color: #0F172A;">Belum Memenuhi Ketentuan Selesai</h4>
                    <p style="margin: 0; font-size: 0.85rem; color: #64748B; line-height: 1.5;">
                        Dokumen Surat Keterangan Selesai Magang hanya dapat diterbitkan dan diunduh setelah seluruh rangkaian program magang dan evaluasi bimbingan Anda dinyatakan selesai oleh pembimbing dan administrator.
                    </p>
                </div>
            @endif

        </div>

    </div>
</div>
@endsection

