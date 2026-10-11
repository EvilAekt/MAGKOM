@extends('layouts.dashboard')

@section('title', 'Konfigurasi Sistem - SuperAdmin MagKom')
@section('page_title', 'Parameter Konfigurasi Sistem')
@section('page_description', 'Kelola batasan kebijakan teknis dan aturan operasional portal MagKom (AC-11-7).')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Pengaturan Sistem</span>
@endsection

@section('page_actions')
    <button type="submit" form="formPengaturanSistem" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 42px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s ease; box-shadow: var(--shadow-subtle);">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
            <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
            <polyline points="17 21 17 13 7 13 7 21"></polyline>
            <polyline points="7 3 7 8 15 8"></polyline>
        </svg>
        <span>Simpan Perubahan</span>
    </button>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1080px;">

    <form id="formPengaturanSistem" action="{{ route('superadmin.pengaturan.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.5rem;">
        @csrf
        @method('PUT')

        <!-- KARTU 1: KEBIJAKAN KUOTA & KAPASITAS MAGANG -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem 2rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Kebijakan Kuota & Kapasitas Magang
                    </h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">
                        Batasan daya tampung reguler, slot cadangan kemitraan, dan batas rasio pembimbingan.
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Kuota Umum Publik Per Bulan (Orang) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="kuota_umum_maks_per_bulan" min="1" max="100" required value="{{ old('kuota_umum_maks_per_bulan', $pengaturan['kuota_umum_maks_per_bulan'] ?? 10) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                        Maksimal alokasi kuota reguler per bulan pendaftaran.
                    </span>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Default Alokasi Slot Khusus (Orang) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="slot_khusus_default" min="0" max="50" required value="{{ old('slot_khusus_default', $pengaturan['slot_khusus_default'] ?? 2) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                        Kapasitas cadangan kemitraan khusus internal (F-06).
                    </span>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Beban Maksimal Mentee Per Pembimbing (Orang)
                    </label>
                    <input type="number" name="maks_peserta_aktif_per_pembimbing" min="1" max="50" value="{{ old('maks_peserta_aktif_per_pembimbing', $pengaturan['maks_peserta_aktif_per_pembimbing'] ?? 5) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                        Penegakan beban kerja pembimbing (BR-13). Kosongkan jika tanpa batas.
                    </span>
                </div>
            </div>
        </div>

        <!-- KARTU 2: ATURAN PELAKSANAAN & BERKAS -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem 2rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Aturan Pelaksanaan, Logbook & Dokumen
                    </h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">
                        Ketentuan batas mundur entri jurnal harian, ukuran file dokumen, dan peringatan deadline tugas.
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Mundur Pengisian Logbook (Hari) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="logbook_maks_mundur_hari" min="1" max="30" required value="{{ old('logbook_maks_mundur_hari', $pengaturan['logbook_maks_mundur_hari'] ?? 3) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                        Maksimal hari mundur pengisian logbook peserta (BR-09).
                    </span>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Batas Ukuran Maksimal Berkas Upload (MB) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="berkas_maks_mb" min="1" max="20" required value="{{ old('berkas_maks_mb', $pengaturan['berkas_maks_mb'] ?? 2) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                        Ukuran batas file berkas persyaratan dan lampiran (BR-04).
                    </span>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Peringatan Mendekati Deadline Tugas (Hari) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="deadline_mendekati_hari" min="1" max="14" required value="{{ old('deadline_mendekati_hari', $pengaturan['deadline_mendekati_hari'] ?? 3) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                        Masa aktif indikator mendekati batas waktu penugasan (BR-16).
                    </span>
                </div>
            </div>
        </div>

        <!-- KARTU 3: KEBIJAKAN RETENSI & MASA TENGGANG -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem 2rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Kebijakan Retensi & Masa Tenggang Akun Alumni
                    </h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">
                        Masa aktif akun peserta setelah dinyatakan selesai magang sebelum dinonaktifkan permanen.
                    </p>
                </div>
            </div>

            <div style="max-width: 420px;">
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Durasi Masa Tenggang Akun Pasca Magang (Bulan) <span style="color: var(--red-600);">*</span>
                </label>
                <input type="number" name="masa_tenggang_bulan" min="1" max="12" required value="{{ old('masa_tenggang_bulan', $pengaturan['masa_tenggang_bulan'] ?? 1) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
                    Rentang waktu akun peserta tetap aktif untuk mengunduh sertifikat dan lembar nilai (BR-10).
                </span>
            </div>
        </div>

        <!-- KARTU 4: SKALA AMBANG BATAS PREDIKAT NILAI (PRD 8.3 & 8.6) -->
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem 2rem; box-shadow: var(--shadow-subtle);">
            <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
                <div style="width: 38px; height: 38px; border-radius: 8px; background: var(--blue-tint); color: var(--blue-700); display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path></svg>
                </div>
                <div>
                    <h2 style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin: 0;">
                        Skala Ambang Batas Predikat Nilai (PRD 8.3 & 8.6)
                    </h2>
                    <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0.15rem 0 0 0;">
                        Konfigurasi ambang skor minimum untuk predikat A, B, C, dan D (Di bawah ambang D otomatis predikat E).
                    </p>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
                <div style="background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.25rem; border-top: 4px solid var(--green-600);">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--green-700); margin-bottom: 0.375rem;">
                        Batas Minimum Predikat A <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="batas_predikat_a" min="1" max="100" required value="{{ old('batas_predikat_a', $predikatBatas['A'] ?? 85) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 1rem; font-weight: 800; background: #FFFFFF; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block;">Nilai Sangat Baik (Default: &ge; 85)</span>
                </div>

                <div style="background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.25rem; border-top: 4px solid var(--blue-600);">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--blue-700); margin-bottom: 0.375rem;">
                        Batas Minimum Predikat B <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="batas_predikat_b" min="1" max="100" required value="{{ old('batas_predikat_b', $predikatBatas['B'] ?? 70) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 1rem; font-weight: 800; background: #FFFFFF; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block;">Nilai Baik (Default: &ge; 70)</span>
                </div>

                <div style="background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.25rem; border-top: 4px solid var(--amber-500);">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--amber-700); margin-bottom: 0.375rem;">
                        Batas Minimum Predikat C <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="batas_predikat_c" min="1" max="100" required value="{{ old('batas_predikat_c', $predikatBatas['C'] ?? 55) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 1rem; font-weight: 800; background: #FFFFFF; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block;">Nilai Cukup (Default: &ge; 55)</span>
                </div>

                <div style="background: var(--neutral-100); border: 1px solid var(--neutral-300); border-radius: 10px; padding: 1.25rem; border-top: 4px solid var(--red-600);">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--red-700); margin-bottom: 0.375rem;">
                        Batas Minimum Predikat D <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="number" name="batas_predikat_d" min="1" max="100" required value="{{ old('batas_predikat_d', $predikatBatas['D'] ?? 40) }}" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 1rem; font-weight: 800; background: #FFFFFF; font-family: inherit;">
                    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block;">Nilai Kurang (Default: &ge; 40)</span>
                </div>
            </div>
        </div>

        <!-- Tombol Aksi Bawah Form -->
        <div style="display: flex; justify-content: flex-end; padding-top: 0.5rem;">
            <button type="submit" style="height: 44px; padding: 0 2rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: all 0.15s; box-shadow: var(--shadow-subtle);">
                Simpan Perubahan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection
