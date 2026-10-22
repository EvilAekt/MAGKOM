@extends('layouts.peserta')

@section('title', 'Survei Kepuasan & Evaluasi Magang - MagKom Sleman')
@section('page_title', 'Survei Evaluasi Mutu Magang')

@push('styles')
<style>
    .survei-label-q {
        font-size: 0.9375rem;
        font-weight: 800;
        color: var(--neutral-900, #0F172A);
    }
    .survei-desc-q {
        font-size: 0.8125rem;
        color: var(--neutral-600, #64748B);
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
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: 8px;
        background: var(--neutral-100, #F8FAFC);
        transition: all 0.15s ease;
        color: var(--neutral-800, #1E293B);
    }
    .survei-option-label:hover {
        border-color: #0641CC;
        background: var(--blue-tint, #EFF6FF);
    }
    .survei-nps-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 0.625rem 0.25rem;
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: 8px;
        background: var(--neutral-100, #F8FAFC);
        cursor: pointer;
        text-align: center;
        transition: all 0.15s ease;
    }
    .survei-nps-label:hover {
        border-color: #0641CC;
        background: var(--blue-tint, #EFF6FF);
    }
    .survei-nps-score {
        font-weight: 800;
        font-size: 0.9375rem;
        color: var(--neutral-800, #1E293B);
        margin-top: 0.25rem;
    }
    .survei-textarea {
        width: 100%;
        border: 1px solid var(--neutral-300, #CBD5E1);
        border-radius: 8px;
        padding: 0.75rem;
        font-size: 0.875rem;
        font-family: inherit;
        background: var(--neutral-0, #FFFFFF);
        color: var(--neutral-900, #0F172A);
        box-sizing: border-box;
        transition: border-color 0.15s ease;
    }
    .survei-textarea:focus {
        outline: none;
        border-color: #0641CC;
        box-shadow: 0 0 0 2px rgba(6, 65, 204, 0.1);
    }
    [data-theme="dark"] .survei-label-q {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .survei-desc-q {
        color: #94A3B8 !important;
    }
    [data-theme="dark"] .survei-option-label,
    [data-theme="dark"] .survei-nps-label {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #CBD5E1 !important;
    }
    [data-theme="dark"] .survei-nps-score {
        color: #F8FAFC !important;
    }
    [data-theme="dark"] .survei-textarea {
        background: #0F172A !important;
        border-color: #26354D !important;
        color: #F8FAFC !important;
    }
</style>
@endpush

@section('content')
<div style="max-width: 860px; margin: 0 auto; display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- HERO HEADER -->
    <div style="background: linear-gradient(135deg, #0641CC 0%, #0751FF 100%); border-radius: 12px; padding: 2rem; color: #ffffff; box-shadow: 0 4px 12px rgba(6, 65, 204, 0.15);">
        <span style="font-size: 0.75rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1.5px; background: rgba(255, 255, 255, 0.2); padding: 0.25rem 0.75rem; border-radius: 999px;">
            Evaluasi Mutu Layanan Dinas
        </span>
        <h2 style="font-size: 1.5rem; font-weight: 800; margin: 0.75rem 0 0.5rem 0; color: #ffffff;">Kuesioner Evaluasi &amp; Kepuasan Peserta Magang</h2>
        <p style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.9); margin: 0; line-height: 1.6;">
            Masukan Anda sangat bernilai bagi Dinas Komunikasi dan Informatika Kabupaten Sleman untuk terus meningkatkan kualitas kurikulum penugasan, sarana prasarana, dan bimbingan lapangan di masa mendatang.
        </p>
    </div>

    @if($survei)
        <x-gentelella.card variant="default">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    <x-gentelella.badge variant="success">Terdata di Sistem</x-gentelella.badge>
                    <p style="margin: 0.25rem 0 0 0; font-size: 0.8125rem; color: var(--neutral-600, #64748B);">
                        Terakhir diperbarui pada: <strong>{{ $survei->updated_at->translatedFormat('d F Y, H:i') }} WIB</strong>. Anda dapat memperbarui penilaian ini kapan saja di bawah.
                    </p>
                </div>
                <div style="font-size: 0.875rem; font-weight: 800; color: #0641CC;">
                    Skor NPS Anda: {{ $survei->skor_nps }}/10
                </div>
            </div>
        </x-gentelella.card>
    @endif

    <!-- FORM KUESIONER -->
    <x-gentelella.x-panel title="Instrumen Penilaian Kepuasan Magang" subtitle="Mohon berikan penilaian objektif pada setiap aspek pelaksanaan magang" :toolbox="false">
        <form method="POST" action="{{ route('peserta.survei.store') }}" style="display: flex; flex-direction: column; gap: 2rem;">
            @csrf

            <!-- 1. Bimbingan Mentor -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <label class="survei-label-q">
                    1. Kualitas Pendampingan &amp; Komunikasi Pembimbing Lapangan
                </label>
                <p class="survei-desc-q">
                    Seberapa jelas arahan, ketersediaan konsultasi, dan umpan balik yang diberikan mentor ({{ $penempatan->pembimbing?->nama ?? 'Pembimbing' }}) selama masa bimbingan?
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.25rem;">
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
                @error('skor_mentoring') <span style="color: var(--red-600, #DC2626); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <!-- 2. Relevansi Penugasan -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <label class="survei-label-q">
                    2. Relevansi Penugasan dengan Kompetensi Kejuruan / Studi Anda
                </label>
                <p class="survei-desc-q">
                    Seberapa sesuai tugas-tugas proyek magang yang diberikan dengan bidang ilmu dan pengembangan keahlian praktis Anda?
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.25rem;">
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
                @error('skor_tugas') <span style="color: var(--red-600, #DC2626); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <!-- 3. Fasilitas & Lingkungan Kerja -->
            <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                <label class="survei-label-q">
                    3. Sarana, Prasarana &amp; Iklim Kerja Dinas
                </label>
                <p class="survei-desc-q">
                    Penilaian terhadap fasilitas ruangan kerja, akses jaringan internet dinas, dan keramahan pegawai lingkungan Kominfo Sleman:
                </p>
                <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.25rem;">
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
                @error('skor_fasilitas') <span style="color: var(--red-600, #DC2626); font-size: 0.75rem;">{{ $message }}</span> @enderror
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
                <div style="display: flex; justify-content: space-between; font-size: 0.6875rem; color: var(--neutral-500, #64748B); font-weight: 600;">
                    <span>0 = Sangat Tidak Mungkin</span>
                    <span>10 = Sangat Yakin Merekomendasikan</span>
                </div>
                @error('skor_nps') <span style="color: var(--red-600, #DC2626); font-size: 0.75rem;">{{ $message }}</span> @enderror
            </div>

            <!-- 5. Umpan Balik Positif -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="umpan_balik" style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Pengalaman Paling Berkesan &amp; Nilai Tambah yang Diperoleh (Opsional):
                </label>
                <textarea name="umpan_balik" id="umpan_balik" rows="3" placeholder="Ceritakan pengalaman belajar atau ilmu praktis yang paling bermanfaat bagi Anda..." class="survei-textarea">{{ old('umpan_balik', $survei->umpan_balik ?? '') }}</textarea>
            </div>

            <!-- 6. Saran Perbaikan -->
            <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                <label for="saran" style="font-size: 0.875rem; font-weight: 700; color: var(--neutral-800, #1E293B);">
                    Saran &amp; Masukan Konstruktif untuk Kemajuan Magang Kominfo (Opsional):
                </label>
                <textarea name="saran" id="saran" rows="3" placeholder="Tuliskan saran untuk penyempurnaan sarana, proses administrasi, atau topik bimbingan berikutnya..." class="survei-textarea">{{ old('saran', $survei->saran ?? '') }}</textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; padding-top: 1rem; border-top: 1px solid var(--neutral-200, #E2E8F0);">
                <x-gentelella.button type="submit" variant="primary">
                    {{ $survei ? 'Perbarui Survei Evaluasi' : 'Kirimkan Evaluasi Magang' }}
                </x-gentelella.button>
            </div>
        </form>
    </x-gentelella.x-panel>

</div>
@endsection
