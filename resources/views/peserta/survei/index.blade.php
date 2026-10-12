@extends('layouts.peserta')

@section('title', 'Survei Kepuasan & Evaluasi Magang - MagKom Sleman')
@section('page_title', 'Survei Evaluasi Mutu Magang')

@push('styles')
<style>
    .survei-card {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-300);
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: var(--shadow-subtle);
    }

    .survei-form {
        background: var(--neutral-0);
        border: 1px solid var(--neutral-400);
        border-radius: 16px;
        padding: 2rem;
        box-shadow: var(--shadow-subtle);
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .survei-label-q {
        font-size: 0.9375rem;
        font-weight: 800;
        color: var(--neutral-900);
    }

    .survei-desc-q {
        font-size: 0.8125rem;
        color: var(--neutral-600);
        margin: 0;
    }

    .survei-option-label {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8125rem;
        font-weight: 600;
        cursor: pointer;
        padding: 0.5rem 0.875rem;
        border: 1px solid var(--neutral-300);
        border-radius: 8px;
        background: var(--neutral-50);
        transition: all 0.15s ease;
        color: var(--neutral-800);
    }

    .survei-option-label:hover {
        border-color: var(--blue-600);
        background: var(--blue-tint, #EFF6FF);
    }

    .survei-nps-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.625rem 0.25rem;
        border: 1px solid var(--neutral-300);
        border-radius: 8px;
        background: var(--neutral-50);
        cursor: pointer;
        text-align: center;
        transition: all 0.15s ease;
    }

    .survei-nps-label:hover {
        border-color: var(--blue-600);
        background: var(--blue-tint, #EFF6FF);
    }

    .survei-nps-score {
        font-weight: 800;
        font-size: 0.9375rem;
        color: var(--neutral-800);
        margin-top: 0.25rem;
    }

    .survei-textarea {
        width: 100%;
        border: 1px solid var(--neutral-400);
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 0.875rem;
        font-family: inherit;
        background: var(--neutral-0);
        color: var(--neutral-900);
        box-sizing: border-box;
        transition: border-color 0.15s ease;
    }

    .survei-textarea:focus {
        outline: none;
        border-color: var(--blue-600);
    }

    /* Dark Mode Harmonization */
    [data-theme="dark"] .survei-card,
    [data-theme="dark"] .survei-form {
        background: #182234 !important;
        border-color: #26354D !important;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.25) !important;
    }

    [data-theme="dark"] .survei-label-q {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .survei-desc-q {
        color: #94A3B8 !important;
    }

    [data-theme="dark"] .survei-option-label {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }

    [data-theme="dark"] .survei-option-label:hover {
        background: #1E293B !important;
        border-color: #3B82F6 !important;
        color: #60A5FA !important;
    }

    [data-theme="dark"] .survei-nps-label {
        background: #0F172A !important;
        border-color: #26354D !important;
    }

    [data-theme="dark"] .survei-nps-label:hover {
        background: #1E293B !important;
        border-color: #3B82F6 !important;
    }

    [data-theme="dark"] .survei-nps-score {
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .survei-textarea {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }

    [data-theme="dark"] .survei-textarea:focus {
        border-color: #2563EB !important;
        box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.25) !important;
    }
</style>
@endpush

@section('content')
<div style="max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- HERO HEADER -->
    <div style="background: linear-gradient(135deg, var(--blue-700) 0%, var(--blue-600) 100%); border-radius: 16px; padding: 2rem; color: #ffffff; box-shadow: var(--shadow-subtle);">
        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.75rem; border-radius: 999px;">
            Evaluasi Mutu Layanan Dinas
        </span>
        <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0.75rem 0 0.5rem 0;">Kuesioner Evaluasi & Kepuasan Peserta Magang</h2>
        <p style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.9); margin: 0; line-height: 1.6;">
            Masukan Anda sangat bernilai bagi Dinas Komunikasi dan Informatika Kabupaten Sleman untuk terus meningkatkan kualitas kurikulum penugasan, sarana prasarana, dan bimbingan lapangan di masa mendatang.
        </p>
    </div>

    @if(session('success'))
        <div style="background: var(--green-50, #F0FDF4); border: 1px solid var(--green-300, #86EFAC); border-radius: 12px; padding: 1rem 1.25rem; color: var(--green-800, #166534); font-size: 0.875rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if($survei)
        <div class="survei-card" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <span class="badge badge-success" style="font-size: 0.75rem;">Terdata di Sistem</span>
                <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600);">
                    Terakhir diperbarui pada: <strong>{{ $survei->updated_at->translatedFormat('d F Y, H:i') }} WIB</strong>. Anda dapat memperbarui penilaian ini kapan saja di bawah.
                </p>
            </div>
            <div style="font-size: 0.875rem; font-weight: 800; color: var(--blue-600);">
                Skor NPS Anda: {{ $survei->skor_nps }}/10
            </div>
        </div>
    @endif

    <!-- FORM KUESIONER -->
    <form method="POST" action="{{ route('peserta.survei.store') }}" class="survei-form">
        @csrf

        <!-- 1. Bimbingan Mentor -->
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <label class="survei-label-q">
                1. Kualitas Pendampingan & Komunikasi Pembimbing Lapangan
            </label>
            <p class="survei-desc-q">
                Seberapa jelas arahan, ketersediaan konsultasi, dan umpan balik yang diberikan mentor ({{ $penempatan->pembimbing?->nama ?? 'Pembimbing' }}) selama masa bimbingan?
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.25rem;">
                @foreach([
                    1 => '1 - Sangat Kurang',
                    2 => '2 - Kurang',
                    3 => '3 - Cukup',
                    4 => '4 - Baik',
                    5 => '5 - Sangat Memuaskan'
                ] as $val => $lbl)
                    <label class="survei-option-label">
                        <input type="radio" name="skor_mentoring" value="{{ $val }}" {{ old('skor_mentoring', $survei->skor_mentoring ?? 5) == $val ? 'checked' : '' }} required>
                        <span>{{ $lbl }}</span>
                    </label>
                @endforeach
            </div>
            @error('skor_mentoring') <span style="color: var(--red-600); font-size: 0.75rem;">{{ $message }}</span> @enderror
        </div>

        <!-- 2. Relevansi Penugasan -->
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <label class="survei-label-q">
                2. Relevansi Penugasan dengan Kompetensi Kejuruan / Studi Anda
            </label>
            <p class="survei-desc-q">
                Seberapa sesuai tugas-tugas proyek magang yang diberikan dengan bidang ilmu dan pengembangan keahlian praktis Anda?
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.25rem;">
                @foreach([
                    1 => '1 - Tidak Relevan',
                    2 => '2 - Kurang Relevan',
                    3 => '3 - Cukup Relevan',
                    4 => '4 - Relevan',
                    5 => '5 - Sangat Relevan & Bermanfaat'
                ] as $val => $lbl)
                    <label class="survei-option-label">
                        <input type="radio" name="skor_tugas" value="{{ $val }}" {{ old('skor_tugas', $survei->skor_tugas ?? 5) == $val ? 'checked' : '' }} required>
                        <span>{{ $lbl }}</span>
                    </label>
                @endforeach
            </div>
            @error('skor_tugas') <span style="color: var(--red-600); font-size: 0.75rem;">{{ $message }}</span> @enderror
        </div>

        <!-- 3. Fasilitas & Lingkungan Kerja -->
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <label class="survei-label-q">
                3. Sarana, Prasarana & Iklim Kerja Dinas
            </label>
            <p class="survei-desc-q">
                Penilaian terhadap fasilitas ruangan kerja, akses jaringan internet dinas, dan keramahan pegawai lingkungan Kominfo Sleman:
            </p>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 0.25rem;">
                @foreach([
                    1 => '1 - Sangat Kurang',
                    2 => '2 - Kurang Memadai',
                    3 => '3 - Cukup Nyaman',
                    4 => '4 - Memadai & Kondusif',
                    5 => '5 - Sangat Nyaman & Lengkap'
                ] as $val => $lbl)
                    <label class="survei-option-label">
                        <input type="radio" name="skor_fasilitas" value="{{ $val }}" {{ old('skor_fasilitas', $survei->skor_fasilitas ?? 5) == $val ? 'checked' : '' }} required>
                        <span>{{ $lbl }}</span>
                    </label>
                @endforeach
            </div>
            @error('skor_fasilitas') <span style="color: var(--red-600); font-size: 0.75rem;">{{ $message }}</span> @enderror
        </div>

        <!-- 4. Net Promoter Score (NPS) -->
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <label class="survei-label-q">
                4. Rekomendasi Magang (Net Promoter Score)
            </label>
            <p class="survei-desc-q">
                Seberapa besar kemungkinan Anda merekomendasikan program magang di Dinas Komunikasi dan Informatika Sleman kepada rekan kampus / sekolah Anda?
            </p>
            <div style="display: grid; grid-template-columns: repeat(11, 1fr); gap: 0.375rem; margin-top: 0.5rem;">
                @for($n = 0; $n <= 10; $n++)
                    <label class="survei-nps-label">
                        <input type="radio" name="skor_nps" value="{{ $n }}" {{ old('skor_nps', $survei->skor_nps ?? 10) == $n ? 'checked' : '' }} required>
                        <span class="survei-nps-score">{{ $n }}</span>
                    </label>
                @endfor
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.6875rem; color: var(--neutral-500); font-weight: 600;">
                <span>0 = Sangat Tidak Mungkin</span>
                <span>10 = Sangat Yakin Merekomendasikan</span>
            </div>
            @error('skor_nps') <span style="color: var(--red-600); font-size: 0.75rem;">{{ $message }}</span> @enderror
        </div>

        <!-- 5. Umpan Balik Positif -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label for="umpan_balik" style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">
                Pengalaman Paling Berkesan & Nilai Tambah yang Diperoleh (Opsional):
            </label>
            <textarea name="umpan_balik" id="umpan_balik" rows="3" placeholder="Ceritakan pengalaman belajar atau ilmu praktis yang paling bermanfaat bagi Anda..." class="survei-textarea">{{ old('umpan_balik', $survei->umpan_balik ?? '') }}</textarea>
        </div>

        <!-- 6. Saran Perbaikan -->
        <div style="display: flex; flex-direction: column; gap: 0.5rem;">
            <label for="saran" style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800);">
                Saran & Masukan Konstruktif untuk Kemajuan Magang Kominfo (Opsional):
            </label>
            <textarea name="saran" id="saran" rows="3" placeholder="Tuliskan saran untuk penyempurnaan sarana, proses administrasi, atau topik bimbingan berikutnya..." class="survei-textarea">{{ old('saran', $survei->saran ?? '') }}</textarea>
        </div>

        <div style="display: flex; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--neutral-200);">
            <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2rem; font-weight: 800; font-size: 0.9375rem;">
                {{ $survei ? 'Perbarui Survei Evaluasi' : 'Kirimkan Evaluasi Magang' }}
            </button>
        </div>
    </form>

</div>
@endsection
