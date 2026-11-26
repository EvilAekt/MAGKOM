@extends('layouts.dashboard')

@section('title', 'Detail Tim Bimbingan - ' . $kelompok->nama_kelompok)
@section('topbar_title', 'Review Tugas Tim')

@push('styles')
    @vite(['resources/css/pages/pembimbing/tugas-index.css'])
@endpush

@section('content')
<div class="tugas-classroom-container">
    {{-- Top Header Banner --}}
    <div class="tugas-banner-card sc-reveal-up" data-scroll-reveal>
        <div class="tugas-banner-content">
            <div class="banner-left-info">
                <div class="banner-badge-role">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    <span>TIM BIMBINGAN MAGANG</span>
                </div>
                <h1 class="tugas-banner-title">{{ $kelompok->nama_kelompok }}</h1>
                <div class="tugas-banner-meta">
                    <span>Ketua Tim: <strong>{{ $kelompok->ketua?->nama ?? 'Ketua' }}</strong></span>
                    <span>&bull;</span>
                    <span>Total <strong>{{ $memberUsers->count() }} Anggota</strong></span>
                    <span>&bull;</span>
                    <span>Bidang: <strong>{{ auth()->user()->bidang?->nama_bidang ?? 'Diskominfo Sleman' }}</strong></span>
                </div>
            </div>
            <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                <a href="{{ route('pembimbing.tugas.index', ['jenis_tugas' => 'kelompok']) }}" class="btn-filter-reset" style="display: inline-flex; align-items: center; gap: 6px;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    <span>Kembali ke Daftar Tim</span>
                </a>
                <a href="{{ route('pembimbing.tugas.kelompok.project.create', $kelompok->id) }}" class="btn-create-task" style="text-decoration: none;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>+ Buat Project Bersama</span>
                </a>
            </div>
        </div>
    </div>

    {{-- Alert Flash Message --}}
    @if(session('success'))
        <div style="margin-bottom: 1.25rem; padding: 12px 18px; border-radius: 10px; background: #ECFDF5; border: 1px solid #A7F3D0; color: #065F46; font-size: 0.9rem; font-weight: 600; display: flex; align-items: center; gap: 8px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Tab Navigasi: Review Individu vs Project Bersama --}}
    <div style="display: flex; gap: 8px; margin-bottom: 1.5rem; border-bottom: 2px solid #E2E8F0; padding-bottom: 2px;">
        <a href="{{ route('pembimbing.tugas.kelompok.show', ['kelompok' => $kelompok->id, 'tab' => 'individu']) }}"
           style="padding: 10px 20px; font-size: 0.95rem; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab !== 'project' ? '#003566' : 'transparent' }}; color: {{ $activeTab !== 'project' ? '#003566' : '#64748B' }}; display: inline-flex; align-items: center; gap: 8px; margin-bottom: -2px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            <span>Review Individu ({{ $tugasIndividu->count() }} Tugas)</span>
        </a>
        <a href="{{ route('pembimbing.tugas.kelompok.show', ['kelompok' => $kelompok->id, 'tab' => 'project']) }}"
           style="padding: 10px 20px; font-size: 0.95rem; font-weight: 700; text-decoration: none; border-bottom: 3px solid {{ $activeTab === 'project' ? '#003566' : 'transparent' }}; color: {{ $activeTab === 'project' ? '#003566' : '#64748B' }}; display: inline-flex; align-items: center; gap: 8px; margin-bottom: -2px;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
            <span>Project Bersama ({{ $projectBersama->count() }} Project)</span>
        </a>
    </div>

    @if($activeTab === 'project')
        {{-- VIEW MODE: PROJECT BERSAMA --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">
            @if($projectBersama->isEmpty())
                <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; padding: 48px 24px; text-align: center;">
                    <div style="width: 56px; height: 56px; border-radius: 50%; background: #EFF6FF; color: #2563EB; display: flex; align-items: center; justify-content: center; margin: 0 auto 16px;">
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"></polygon><polyline points="2 17 12 22 22 17"></polyline><polyline points="2 12 12 17 22 12"></polyline></svg>
                    </div>
                    <h3 style="margin: 0 0 8px; font-size: 1.15rem; font-weight: 800; color: #0F172A;">Belum Ada Project Bersama</h3>
                    <p style="margin: 0 0 20px; color: #64748B; font-size: 0.9rem;">Buat project kelompok baru dan tentukan peserta mana saja dari kelompok ini yang dilibatkan.</p>
                    <a href="{{ route('pembimbing.tugas.kelompok.project.create', $kelompok->id) }}" class="btn-create-task" style="text-decoration: none; display: inline-flex;">
                        <span>+ Buat Project Bersama Sekarang</span>
                    </a>
                </div>
            @else
                @foreach($projectBersama as $proj)
                    @php
                        $assignedIds = $proj->pesertaDitugaskan->pluck('id')->all();
                        $projStatus = $proj->status->value ?? (string)$proj->status;
                    @endphp
                    <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                        <div style="padding: 18px 24px; background: #FAFBFD; border-bottom: 1px solid #E2E8F0; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                            <div>
                                <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 4px;">
                                    <span style="display: inline-block; padding: 3px 8px; border-radius: 6px; font-size: 0.725rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; background: #FEF3C7; color: #92400E; border: 1px solid #FDE68A;">PROJECT TIM</span>
                                    <span style="font-size: 0.8rem; color: #64748B;">Tenggat: <strong>{{ $proj->deadline?->format('d M Y') ?? 'Tidak Ditentukan' }}</strong></span>
                                </div>
                                <h3 style="margin: 0; font-size: 1.15rem; font-weight: 800; color: #003566;">{{ $proj->judul }}</h3>
                            </div>
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <a href="{{ route('pembimbing.tugas.show', $proj->id) }}" class="btn-compact-download" style="text-decoration: none; padding: 6px 14px; font-weight: 700; background: #003566; color: #FFFFFF; border-radius: 8px;">
                                    <span>Buka Canvas Review</span>
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                </a>
                            </div>
                        </div>

                        <div style="padding: 20px 24px;">
                            @if($proj->deskripsi)
                                <p style="margin: 0 0 16px; color: #334155; font-size: 0.9rem; line-height: 1.5;">{!! nl2br(e($proj->deskripsi)) !!}</p>
                            @endif

                            {{-- Anggota yang Ditugaskan (Assignment Box) --}}
                            <div style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 18px;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; flex-wrap: wrap; gap: 8px;">
                                    <span style="font-size: 0.8rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; color: #475569;">
                                        Peserta yang Dilibatkan ({{ count($assignedIds) }} dari {{ $memberUsers->count() }} Anggota)
                                    </span>
                                    <span style="font-size: 0.775rem; color: #64748B;">*Hanya peserta tercentang yang dapat melihat project ini di dashboard masing-masing</span>
                                </div>

                                <form method="POST" action="{{ route('pembimbing.tugas.project.assignment', $proj->id) }}" style="display: flex; flex-direction: column; gap: 12px;">
                                    @csrf
                                    @method('PUT')
                                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                                        @foreach($memberUsers as $mUser)
                                            @php
                                                $isAssigned = in_array($mUser->id, $assignedIds, true);
                                                $isKetua = (int)$mUser->id === (int)$kelompok->ketua_user_id;
                                            @endphp
                                            <label style="display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 8px; border: 1.5px solid {{ $isAssigned ? '#2563EB' : '#CBD5E1' }}; background: {{ $isAssigned ? '#EFF6FF' : '#FFFFFF' }}; cursor: pointer; transition: all 0.15s ease;">
                                                <input type="checkbox" name="peserta_ids[]" value="{{ $mUser->id }}" {{ $isAssigned ? 'checked' : '' }} style="accent-color: #2563EB; width: 16px; height: 16px;">
                                                <span style="font-size: 0.85rem; font-weight: 700; color: {{ $isAssigned ? '#1D4ED8' : '#334155' }};">
                                                    {{ $mUser->nama }}
                                                    @if($isKetua)
                                                        <span style="font-size: 0.725rem; font-weight: 800; color: #D97706; margin-left: 4px;">(Ketua)</span>
                                                    @endif
                                                </span>
                                            </label>
                                        @endforeach
                                    </div>
                                    <div style="display: flex; justify-content: flex-end;">
                                        <button type="submit" class="btn-filter-apply" style="padding: 6px 16px; font-size: 0.825rem;">
                                            <span>Simpan Perubahan Penugasan</span>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    @else
        {{-- VIEW MODE: REVIEW INDIVIDU --}}
        <div style="display: flex; flex-direction: column; gap: 16px;">
            <div style="background: #FFFFFF; border: 1px solid #E2E8F0; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(15, 23, 42, 0.04);">
                <div style="padding: 16px 20px; background: #FAFBFD; border-bottom: 1px solid #E2E8F0;">
                    <h3 style="margin: 0; font-size: 1rem; font-weight: 800; color: #003566;">Daftar Anggota Tim &amp; Status Penugasan Individu</h3>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                        <thead>
                            <tr style="border-bottom: 1px solid #E2E8F0; background: #F8FAFC; color: #475569; font-weight: 700;">
                                <th style="padding: 12px 16px;">No</th>
                                <th style="padding: 12px 16px;">Nama Peserta</th>
                                <th style="padding: 12px 16px;">Peran</th>
                                <th style="padding: 12px 16px;">Institusi / Jurusan</th>
                                <th style="padding: 12px 16px;">Total Tugas Individu</th>
                                <th style="padding: 12px 16px; text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($memberUsers as $idx => $mUser)
                                @php
                                    $isKetua = (int)$mUser->id === (int)$kelompok->ketua_user_id;
                                    $userPenempatan = $mUser->penempatanPeserta()->where('status', \App\Enums\StatusPenempatanEnum::Aktif)->first();
                                    $userTasksCount = $tugasIndividu->where('penempatan.peserta_id', $mUser->id)->count();
                                @endphp
                                <tr style="border-bottom: 1px solid #F1F5F9;">
                                    <td style="padding: 12px 16px; color: #64748B;">{{ $idx + 1 }}</td>
                                    <td style="padding: 12px 16px;">
                                        <strong>{{ $mUser->nama }}</strong>
                                        <div style="font-size: 0.775rem; color: #64748B;">{{ $mUser->email }}</div>
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        @if($isKetua)
                                            <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 800; background: #FEF3C7; color: #92400E;">KETUA TIM</span>
                                        @else
                                            <span style="display: inline-block; padding: 2px 8px; border-radius: 6px; font-size: 0.75rem; font-weight: 700; background: #F1F5F9; color: #475569;">Anggota</span>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 16px; color: #334155;">
                                        {{ $mUser->profil?->institusi ?? '-' }} / {{ $mUser->profil?->jurusan?->nama_jurusan ?? '-' }}
                                    </td>
                                    <td style="padding: 12px 16px;">
                                        <span style="font-weight: 800; color: #003566;">{{ $userTasksCount }} Tugas</span>
                                    </td>
                                    <td style="padding: 12px 16px; text-align: center;">
                                        @if($userPenempatan)
                                            <a href="{{ route('pembimbing.tugas.index', ['masuk' => $userPenempatan->id]) }}" class="btn-filter-apply" style="padding: 4px 12px; font-size: 0.8rem; text-decoration: none; display: inline-flex;">
                                                <span>Lihat Tugas Mandiri</span>
                                            </a>
                                        @else
                                            <span style="color: #94A3B8; font-size: 0.8rem;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

