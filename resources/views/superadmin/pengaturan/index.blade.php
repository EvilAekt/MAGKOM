@extends('layouts.dashboard')

@section('title', 'Konfigurasi Sistem - SuperAdmin MagKom')
@section('page_title', 'Parameter Konfigurasi Sistem')
@section('page_description', 'Kelola batasan kebijakan teknis dan aturan operasional portal MagKom (AC-11-7).')

@section('breadcrumbs')
    <span class="breadcrumb-current">Pengaturan Sistem</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 920px;">
    <!-- Form Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 2rem; box-shadow: var(--shadow-subtle);">
        <form action="{{ route('superadmin.pengaturan.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 2rem;">
            @csrf
            @method('PUT')

            <!-- Section 1: Kebijakan Kuota & Pendaftaran -->
            <div>
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                    <h2 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Kebijakan Kuota & Kapasitas Magang
                    </h2>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Batas Kuota Umum Publik Per Bulan (Orang) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="number" name="kuota_umum_maks_per_bulan" min="1" max="100" required value="{{ old('kuota_umum_maks_per_bulan', $pengaturan['kuota_umum_maks_per_bulan'] ?? 10) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Maksimal alokasi kuota reguler per bulan pendaftaran.</span>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Default Alokasi Slot Khusus (Orang) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="number" name="slot_khusus_default" min="0" max="50" required value="{{ old('slot_khusus_default', $pengaturan['slot_khusus_default'] ?? 2) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Kapasitas cadangan kemitraan khusus internal (F-06).</span>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Beban Maksimal Mentee Per Pembimbing (Orang)
                        </label>
                        <input type="number" name="maks_peserta_aktif_per_pembimbing" min="1" max="50" value="{{ old('maks_peserta_aktif_per_pembimbing', $pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 5) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Penegakan beban kerja pembimbing (BR-13). Kosongkan jika tanpa batas.</span>
                    </div>
                </div>
            </div>

            <!-- Section 2: Kebijakan Pelaksanaan & Berkas -->
            <div>
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                    </div>
                    <h2 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Aturan Pelaksanaan, Logbook & Dokumen
                    </h2>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Batas Mundur Pengisian Logbook (Hari) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="number" name="logbook_maks_mundur_hari" min="1" max="30" required value="{{ old('logbook_maks_mundur_hari', $pengaturan['logbook_maks_mundur_hari'] ?? 3) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Maksimal hari mundur pengisian logbook peserta (BR-09).</span>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Batas Ukuran Maksimal Berkas Upload (MB) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="number" name="berkas_maks_mb" min="1" max="20" required value="{{ old('berkas_maks_mb', $pengaturan['berkas_maks_mb'] ?? 2) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Ukuran batas file berkas persyaratan dan lampiran (BR-04).</span>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Peringatan Mendekati Deadline Tugas (Hari) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="number" name="deadline_mendekati_hari" min="1" max="14" required value="{{ old('deadline_mendekati_hari', $pengaturan['deadline_mendekati_hari'] ?? 3) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Masa aktif indikator mendekati batas waktu penugasan (BR-16).</span>
                    </div>
                </div>
            </div>

            <!-- Section 3: Masa Tenggang Akun -->
            <div>
                <div style="display: flex; align-items: center; gap: 0.625rem; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
                    <div style="width: 32px; height: 32px; border-radius: 8px; background: var(--blue-50); color: var(--blue-600); display: flex; align-items: center; justify-content: center;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                    <h2 style="font-size: 1.0625rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                        Kebijakan Retensi & Masa Tenggang Akun Alumni
                    </h2>
                </div>

                <div style="max-width: 360px;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Durasi Masa Tenggang Akun Pasca Magang (Bulan) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="masa_tenggang_bulan" min="1" max="12" required value="{{ old('masa_tenggang_bulan', $pengaturan['masa_tenggang_bulan'] ?? 1) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Rentang waktu akun peserta tetap aktif untuk mengunduh sertifikat dan lembar nilai (BR-10).</span>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; border-top: 1px solid var(--neutral-300); padding-top: 1.5rem;">
                <button type="submit" style="height: 44px; padding: 0 2rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s; box-shadow: 0 2px 4px rgba(2, 132, 199, 0.2);">
                    Simpan Perubahan Konfigurasi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
