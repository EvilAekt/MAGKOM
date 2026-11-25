@extends('layouts.dashboard')

@section('title', 'Buat Project Bersama - ' . $kelompok->nama_kelompok)
@section('topbar_title', 'Buat Project Bersama Tim')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-index.css'])
@endpush

@section('content')
<div class="tugas-classroom-container">
    {{-- Header Banner Info Kelompok --}}
    <div class="tugas-banner-card sc-reveal-up" data-scroll-reveal>
        <div class="tugas-banner-content">
            <div class="banner-left-info">
                <div class="banner-badge-role">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    <span>PENUGASAN PROJECT TIM</span>
                </div>
                <h1 class="tugas-banner-title">Project Bersama: {{ $kelompok->nama_kelompok }}</h1>
                <div class="tugas-banner-meta">
                    <span>Ketua Tim: <strong>{{ $kelompok->ketua?->nama ?? 'Ketua' }}</strong></span>
                    <span>&bull;</span>
                    <span>Total <strong>{{ $memberUsers->count() }} Anggota Terdaftar</strong></span>
                    <span>&bull;</span>
                    <span>Bidang: <strong>{{ auth()->user()->bidang?->nama_bidang ?? 'Diskominfo Sleman' }}</strong></span>
                </div>
            </div>
            <div>
                <a href="{{ route('pembimbing.tugas.kelompok.show', ['kelompok' => $kelompok->id, 'tab' => 'project']) }}" class="btn-filter-reset" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Kembali ke Tim</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Error Flash Notification --}}
    @if ($errors->any())
        <div style="margin-bottom: 1.25rem; padding: 14px 18px; border-radius: 10px; background: #FEF2F2; border: 1px solid #FCA5A5; color: #991B1B; font-size: 0.9rem;">
            <strong style="display: block; margin-bottom: 6px;">Mohon periksa kembali formulir project:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Container --}}
    <form method="POST" action="{{ route('pembimbing.tugas.kelompok.project.store', $kelompok->id) }}" style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 14px; overflow: hidden; box-shadow: 0 4px 16px rgba(15, 23, 42, 0.05); margin-bottom: 2rem;">
        @csrf

        <div style="padding: 24px 28px; border-bottom: 1px solid #F1F5F9; background: #FAFBFD;">
            <h2 style="margin: 0 0 4px; font-size: 1.2rem; font-weight: 800; color: #003566;">Rincian &amp; Target Project Bersama</h2>
            <p style="margin: 0; font-size: 0.875rem; color: #64748B;">Tentukan judul, instruksi teknis, batas waktu pengerjaan, dan pilih peserta yang bertanggung jawab mengerjakan project ini.</p>
        </div>

        <div style="padding: 28px; display: flex; flex-direction: column; gap: 24px;">
            {{-- 1. Judul Project --}}
            <div>
                <label for="inputJudul" style="display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">
                    Judul Project Bersama <span style="color: #DC2626;">*</span>
                </label>
                <input type="text"
                       name="judul"
                       id="inputJudul"
                       value="{{ old('judul') }}"
                       required
                       maxlength="150"
                       placeholder="Misal: Pengembangan Dashboard Monitoring Jaringan E-Gov v1"
                       style="width: 100%; box-sizing: border-box; padding: 12px 16px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A; outline: none; transition: border-color 0.2s ease;">
            </div>

            {{-- 2. Baris Kategori, Kompleksitas, dan Deadline --}}
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 18px;">
                <div>
                    <label for="selectKategori" style="display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">
                        Kategori Project <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="kategori" id="selectKategori" required style="width: 100%; box-sizing: border-box; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.925rem; color: #0F172A; background: #FFFFFF; outline: none;">
                        <option value="Implementasi Fitur" {{ old('kategori') === 'Implementasi Fitur' ? 'selected' : '' }}>Implementasi Fitur</option>
                        <option value="Analisis Sistem" {{ old('kategori') === 'Analisis Sistem' ? 'selected' : '' }}>Analisis Sistem</option>
                        <option value="Desain UI/UX" {{ old('kategori') === 'Desain UI/UX' ? 'selected' : '' }}>Desain UI/UX</option>
                        <option value="Infrastruktur & Jaringan" {{ old('kategori') === 'Infrastruktur & Jaringan' ? 'selected' : '' }}>Infrastruktur &amp; Jaringan</option>
                        <option value="Dokumentasi & QA" {{ old('kategori') === 'Dokumentasi & QA' ? 'selected' : '' }}>Dokumentasi &amp; QA</option>
                        <option value="Lainnya" {{ old('kategori') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                    </select>
                </div>

                <div>
                    <label for="selectKompleksitas" style="display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">
                        Tingkat Kompleksitas <span style="color: #DC2626;">*</span>
                    </label>
                    <select name="kompleksitas" id="selectKompleksitas" required style="width: 100%; box-sizing: border-box; padding: 12px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.925rem; color: #0F172A; background: #FFFFFF; outline: none;">
                        <option value="sedang" {{ old('kompleksitas', 'sedang') === 'sedang' ? 'selected' : '' }}>Sedang</option>
                        <option value="rendah" {{ old('kompleksitas') === 'rendah' ? 'selected' : '' }}>Rendah</option>
                        <option value="tinggi" {{ old('kompleksitas') === 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                    </select>
                </div>

                <div>
                    <label for="inputDeadline" style="display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">
                        Tenggat Waktu (Deadline) <span style="color: #DC2626;">*</span>
                    </label>
                    <input type="date"
                           name="deadline"
                           id="inputDeadline"
                           value="{{ old('deadline', now()->addDays(7)->format('Y-m-d')) }}"
                           required
                           style="width: 100%; box-sizing: border-box; padding: 11px 14px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.925rem; color: #0F172A; background: #FFFFFF; outline: none;">
                </div>
            </div>

            {{-- 3. Tautan Referensi --}}
            <div>
                <label for="inputLinkRef" style="display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">
                    Tautan Referensi / Repositori / Workspace (Opsional)
                </label>
                <input type="url"
                       name="link_referensi"
                       id="inputLinkRef"
                       value="{{ old('link_referensi') }}"
                       placeholder="https://github.com/... atau https://figma.com/..."
                       style="width: 100%; box-sizing: border-box; padding: 12px 16px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A; outline: none;">
            </div>

            {{-- 4. Deskripsi & Instruksi Pengerjaan --}}
            <div>
                <label for="inputDeskripsi" style="display: block; font-size: 0.875rem; font-weight: 700; color: #0F172A; margin-bottom: 8px;">
                    Instruksi &amp; Panduan Pengerjaan Project
                </label>
                <textarea name="deskripsi"
                          id="inputDeskripsi"
                          rows="6"
                          placeholder="Jelaskan ruang lingkup project, pembagian peran kerja, kriteria penilaian, dan instruksi penyerahan tugas..."
                          style="width: 100%; box-sizing: border-box; padding: 14px 16px; border: 1.5px solid #CBD5E1; border-radius: 10px; font-size: 0.95rem; color: #0F172A; outline: none; line-height: 1.6; resize: vertical;">{{ old('deskripsi') }}</textarea>
            </div>

            {{-- 5. LOGIKA ASSIGNMENT PESERTA: Penentuan Anggota yang Terlibat --}}
            <div style="background: #F8FAFC; border: 1.5px solid #E2E8F0; border-radius: 12px; padding: 22px 24px;">
                <div style="margin-bottom: 14px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#003566" stroke-width="2.3"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                        <h3 style="margin: 0; font-size: 1.05rem; font-weight: 800; color: #003566;">
                            Penugasan Anggota Tim (Assignment) <span style="color: #DC2626;">*</span>
                        </h3>
                    </div>
                    <p style="margin: 0; font-size: 0.85rem; color: #64748B; line-height: 1.5;">
                        Pilih peserta mana saja dari tim ini yang dilibatkan dalam project bersama.
                        <strong style="color: #0F172A;">Peserta yang tidak dicentang tidak akan dapat melihat atau mengakses project ini di dashboard miliknya.</strong>
                    </p>
                </div>

                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 12px;">
                    @foreach($memberUsers as $member)
                        @php
                            $isKetua = $member->id === $kelompok->ketua_user_id;
                            $mName = $member->nama ?? 'Peserta';
                            $mInst = $member->profil?->institusi ?? 'Institusi';
                            $mInitials = collect(explode(' ', $mName))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->implode('');
                            $isSelected = in_array($member->id, old('peserta_ids', $memberUsers->pluck('id')->all()));
                        @endphp
                        <label style="display: flex; align-items: center; gap: 14px; padding: 12px 16px; background: #FFFFFF; border: 1.5px solid #CBD5E1; border-radius: 10px; cursor: pointer; transition: all 0.15s ease;">
                            <input type="checkbox"
                                   name="peserta_ids[]"
                                   value="{{ $member->id }}"
                                   {{ $isSelected ? 'checked' : '' }}
                                   style="width: 18px; height: 18px; cursor: pointer; accent-color: #003566;">
                            <div style="width: 36px; height: 36px; border-radius: 50%; background: #003566; color: #FFFFFF; font-weight: 800; font-size: 0.8rem; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                {{ $mInitials }}
                            </div>
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 6px;">
                                    <span style="font-size: 0.9rem; font-weight: 800; color: #0F172A; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">{{ $mName }}</span>
                                    @if($isKetua)
                                        <span style="font-size: 0.7rem; font-weight: 800; padding: 2px 6px; border-radius: 4px; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">KETUA</span>
                                    @endif
                                </div>
                                <div style="font-size: 0.775rem; color: #64748B; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    {{ $member->email }} &bull; {{ $mInst }}
                                </div>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Form Actions --}}
        <div style="padding: 18px 28px; background: #F8FAFC; border-top: 1px solid #E2E8F0; display: flex; justify-content: flex-end; align-items: center; gap: 12px;">
            <a href="{{ route('pembimbing.tugas.kelompok.show', ['kelompok' => $kelompok->id, 'tab' => 'project']) }}" class="btn-filter-reset" style="text-decoration: none;">
                Batal
            </a>
            <button type="submit" class="btn-create-task" style="display: inline-flex; align-items: center; gap: 8px; cursor: pointer; border: none;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                <span>Tugaskan Project Sekarang</span>
            </button>
        </div>
    </form>
</div>
@endsection

