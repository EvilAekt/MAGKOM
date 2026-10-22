@extends('layouts.dashboard')

@section('title', 'Edit Tugas: ' . $tugas->judul . ' - Portal MagKom')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1400px; margin: 0 auto; width: 100%;">

    <style>
        .classroom-layout-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
            align-items: start;
        }
        @media (min-width: 1024px) {
            .classroom-layout-grid {
                grid-template-columns: 1.8fr 1.2fr;
            }
        }
        .form-label {
            display: block;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--neutral-600, #475569);
            margin-bottom: 0.45rem;
        }
        .form-control-input {
            width: 100%;
            border-radius: 8px;
            border: 1px solid var(--neutral-300, #CBD5E1);
            background: var(--neutral-0, #FFFFFF);
            padding: 0.65rem 0.9rem;
            font-size: 0.875rem;
            color: var(--neutral-900, #0F172A);
            transition: all 0.2s ease;
            box-sizing: border-box;
        }
        .form-control-input:focus {
            outline: none;
            background: var(--neutral-0, #ffffff);
            border-color: #0641CC;
            box-shadow: 0 0 0 3px rgba(6, 65, 204, 0.15);
        }
        .complexity-option {
            cursor: pointer;
            position: relative;
        }
        .complexity-option input[type="radio"] {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }
        .complexity-box {
            padding: 0.6rem 0.5rem;
            text-align: center;
            border-radius: 8px;
            border: 1px solid var(--neutral-300, #CBD5E1);
            font-size: 0.8rem;
            font-weight: 700;
            color: var(--neutral-600, #475569);
            background: var(--neutral-100, #F8FAFC);
            transition: all 0.2s ease;
            user-select: none;
        }
        .complexity-option input[value="rendah"]:checked + .complexity-box {
            background: #ECFDF5;
            border-color: #10B981;
            color: #047857;
            box-shadow: 0 1px 3px rgba(16, 185, 129, 0.2);
        }
        .complexity-option input[value="sedang"]:checked + .complexity-box {
            background: #FFFBEB;
            border-color: #F59E0B;
            color: #B45309;
            box-shadow: 0 1px 3px rgba(245, 158, 11, 0.2);
        }
        .complexity-option input[value="tinggi"]:checked + .complexity-box {
            background: #EEF2FF;
            border-color: #6366F1;
            color: #4338CA;
            box-shadow: 0 1px 3px rgba(99, 102, 241, 0.2);
        }
        .dropzone-box {
            border: 2px dashed var(--neutral-300, #CBD5E1);
            border-radius: 10px;
            padding: 1.5rem 1.25rem;
            text-align: center;
            cursor: pointer;
            background: var(--neutral-100, #F8FAFC);
            transition: all 0.2s ease;
        }
        .dropzone-box:hover {
            border-color: #0641CC;
            background: var(--blue-tint, #EFF6FF);
        }
        [data-theme="dark"] .form-control-input {
            background: #0F172A !important;
            border-color: #26354D !important;
            color: #F8FAFC !important;
        }
        [data-theme="dark"] .form-label {
            color: #94A3B8 !important;
        }
        [data-theme="dark"] .dropzone-box {
            background: #111C2E !important;
            border-color: #26354D !important;
        }
        [data-theme="dark"] .complexity-box {
            background: #111C2E !important;
            border-color: #26354D !important;
            color: #CBD5E1 !important;
        }
    </style>

    {{-- Top Action & Breadcrumb Navigation --}}
    <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--neutral-200, #E2E8F0); padding-bottom: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500, #64748B); margin-bottom: 0.4rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600, #475569); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <a href="{{ route('pembimbing.tugas.index') }}" style="color: var(--neutral-600, #475569); text-decoration: none;">Tugas Peserta</a>
                <span>/</span>
                <a href="{{ route('pembimbing.tugas.show', $tugas) }}" style="color: var(--neutral-600, #475569); text-decoration: none;">{{ Str::limit($tugas->judul, 25) }}</a>
                <span>/</span>
                <span style="color: var(--neutral-900, #0F172A); font-weight: 700;">Edit Tugas</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 800; color: var(--neutral-900, #0F172A); margin: 0;">
                Edit Tugas Mentee
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-500, #64748B); margin: 0.25rem 0 0 0;">
                Perbarui instruksi, target capaian, kategori, batas waktu, atau berkas acuan penugasan.
            </p>
        </div>

        <div style="display: flex; gap: 0.75rem; align-items: center;">
            <a href="{{ route('pembimbing.tugas.show', $tugas) }}" style="padding: 0.55rem 1.15rem; background: var(--neutral-100, #F1F5F9); color: var(--neutral-700, #334155); font-size: 0.875rem; font-weight: 700; border-radius: 8px; text-decoration: none; border: 1px solid var(--neutral-200, #E2E8F0);">
                Batal
            </a>
            <button type="submit" form="form-edit-tugas" style="padding: 0.55rem 1.25rem; background: #0641CC; color: #ffffff; font-size: 0.875rem; font-weight: 700; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.45rem; box-shadow: 0 2px 4px rgba(6, 65, 204, 0.2);">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Simpan Perubahan</span>
            </button>
        </div>
    </div>

    <form id="form-edit-tugas" action="{{ route('pembimbing.tugas.update', $tugas) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="classroom-layout-grid">
            
            {{-- KOLOM UTAMA: DETAIL TUGAS --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                <x-gentelella.x-panel title="Detail Penugasan &amp; Instruksi" subtitle="Perbarui judul, instruksi, dan tautan acuan tugas" :toolbox="false">
                    {{-- Judul Tugas --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label for="judul" class="form-label">
                            Judul Tugas <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" id="judul" name="judul" value="{{ old('judul', $tugas->judul) }}" required maxlength="150" class="form-control-input" placeholder="Contoh: Implementasi Desain UI Portal Sleman" style="font-size: 1rem; font-weight: 600;">
                    </div>

                    {{-- Deskripsi Tugas --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label for="deskripsi" class="form-label">
                            Instruksi &amp; Deskripsi Tugas
                        </label>
                        <textarea id="deskripsi" name="deskripsi" rows="8" class="form-control-input" placeholder="Tuliskan panduan langkah pengerjaan, batasan teknis, dan target hasil yang diharapkan...">{{ old('deskripsi', $tugas->deskripsi) }}</textarea>
                    </div>

                    {{-- Link Referensi Tambahan --}}
                    <div>
                        <label for="link_referensi" class="form-label">
                            Tautan Referensi Eksternal (URL)
                        </label>
                        <input type="url" id="link_referensi" name="link_referensi" value="{{ old('link_referensi', $tugas->link_referensi) }}" class="form-control-input" placeholder="https://github.com/organisasi/repo atau https://figma.com/file/...">
                    </div>
                </x-gentelella.x-panel>

                {{-- Lampiran Dokumen Acuan --}}
                <x-gentelella.x-panel title="Berkas Lampiran Acuan Tugas" subtitle="Dokumen TOR, materi kerja, atau lembar spesifikasi" :toolbox="false">
                    @if($tugas->file_lampiran)
                        <div style="padding: 0.85rem 1rem; background: var(--blue-tint, #EFF6FF); border: 1px solid var(--blue-tint-line, #BFDBFE); border-radius: 8px; margin-bottom: 1rem; display: flex; justify-content: space-between; align-items: center;">
                            <div style="display: flex; align-items: center; gap: 0.65rem;">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#0641CC" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                <div>
                                    <span style="font-size: 0.875rem; font-weight: 700; color: #1E40AF; display: block;">{{ $tugas->nama_file_lampiran ?: 'Berkas Lampiran Saat Ini' }}</span>
                                    <small style="color: var(--neutral-500, #64748B);">{{ $tugas->ukuran_file_lampiran ? number_format($tugas->ukuran_file_lampiran / 1024, 1) . ' KB' : '' }}</small>
                                </div>
                            </div>
                            <a href="{{ route('pembimbing.tugas.lampiran', $tugas) }}" class="btn-link" style="font-size: 0.8125rem; font-weight: 700; color: #0641CC; text-decoration: none;">
                                Unduh Lampiran &darr;
                            </a>
                        </div>
                    @endif

                    <div class="dropzone-box" onclick="document.getElementById('file_lampiran').click()">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#64748B" stroke-width="2" style="margin: 0 auto 0.5rem auto;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="17 8 12 3 7 8"></polyline><line x1="12" y1="3" x2="12" y2="15"></line></svg>
                        <p style="margin: 0; font-size: 0.875rem; font-weight: 700; color: var(--neutral-700, #334155);">
                            Klik untuk mengganti berkas lampiran (Opsional)
                        </p>
                        <p style="margin: 0.25rem 0 0 0; font-size: 0.75rem; color: var(--neutral-500, #64748B);">
                            PDF, Word, Excel, PPT, ZIP, Gambar (Maks. 10 MB)
                        </p>
                        <input type="file" id="file_lampiran" name="file_lampiran" style="display: none;" onchange="showSelectedFilename(this)">
                        <div id="file-selected-name" style="margin-top: 0.5rem; font-size: 0.8125rem; font-weight: 700; color: #0641CC; display: none;"></div>
                    </div>
                </x-gentelella.x-panel>

            </div>

            {{-- KOLOM KANAN: PARAMETER, DEADLINE & PESERTA --}}
            <div style="display: flex; flex-direction: column; gap: 1.5rem;">
                
                {{-- Info Peserta Penerima Tugas (Terkunci) --}}
                <x-gentelella.card title="Penerima Tugas" icon="fa fa-user">
                    <div style="display: flex; align-items: center; gap: 0.85rem;">
                        <div style="width: 44px; height: 44px; border-radius: 8px; background: var(--blue-tint, #DBEAFE); color: #1E40AF; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1rem; flex-shrink: 0;">
                            {{ strtoupper(substr($tugas->penempatan?->peserta?->nama ?? 'P', 0, 1)) }}
                        </div>
                        <div>
                            <div style="font-weight: 800; font-size: 0.95rem; color: var(--neutral-900, #0F172A);">
                                {{ $tugas->penempatan?->peserta?->nama }}
                            </div>
                            <small style="color: var(--neutral-500, #64748B); font-size: 0.775rem;">
                                {{ $tugas->penempatan?->bidang?->nama_bidang ?? 'Dinas Kominfo Sleman' }} &bull; Selesai {{ $tugas->penempatan?->tgl_selesai?->translatedFormat('d M Y') }}
                            </small>
                        </div>
                    </div>
                </x-gentelella.card>

                {{-- Parameter Penugasan --}}
                <x-gentelella.x-panel title="Parameter Penugasan" subtitle="Kategori, kompleksitas, dan tenggat waktu" :toolbox="false">
                    {{-- Kategori Tugas --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label for="kategori" class="form-label">
                            Kategori Tugas <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="text" id="kategori" name="kategori" value="{{ old('kategori', $tugas->kategori) }}" required maxlength="50" class="form-control-input" placeholder="Contoh: Pemrograman, Desain, Analisis, Jaringan">
                    </div>

                    {{-- Tingkat Kompleksitas --}}
                    <div style="margin-bottom: 1.25rem;">
                        <label class="form-label">
                            Tingkat Kompleksitas <span style="color: #DC2626;">*</span>
                        </label>
                        @php
                            $currentKompleksitas = old('kompleksitas', $tugas->kompleksitas?->value ?? 'sedang');
                        @endphp
                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.5rem;">
                            <label class="complexity-option">
                                <input type="radio" name="kompleksitas" value="rendah" {{ $currentKompleksitas === 'rendah' ? 'checked' : '' }}>
                                <div class="complexity-box">Rendah</div>
                            </label>
                            <label class="complexity-option">
                                <input type="radio" name="kompleksitas" value="sedang" {{ $currentKompleksitas === 'sedang' ? 'checked' : '' }}>
                                <div class="complexity-box">Sedang</div>
                            </label>
                            <label class="complexity-option">
                                <input type="radio" name="kompleksitas" value="tinggi" {{ $currentKompleksitas === 'tinggi' ? 'checked' : '' }}>
                                <div class="complexity-box">Tinggi</div>
                            </label>
                        </div>
                    </div>

                    {{-- Batas Waktu / Deadline --}}
                    <div>
                        <label for="deadline" class="form-label">
                            Batas Waktu (Deadline) <span style="color: #DC2626;">*</span>
                        </label>
                        <input type="date" id="deadline" name="deadline" value="{{ old('deadline', $tugas->deadline?->format('Y-m-d')) }}" required class="form-control-input">
                        @if($tugas->penempatan?->tgl_selesai)
                            <small style="color: var(--neutral-500, #64748B); font-size: 0.725rem; display: block; margin-top: 0.35rem;">
                                Maks. tanggal {{ $tugas->penempatan->tgl_selesai->format('d/m/Y') }} (akhir magang peserta).
                            </small>
                        @endif
                    </div>
                </x-gentelella.x-panel>

            </div>

        </div>
    </form>

</div>

<script>
function showSelectedFilename(input) {
    const display = document.getElementById('file-selected-name');
    if (input.files && input.files[0]) {
        display.textContent = 'Berkas dipilih: ' + input.files[0].name;
        display.style.display = 'block';
    } else {
        display.style.display = 'none';
    }
}
</script>
@endsection
