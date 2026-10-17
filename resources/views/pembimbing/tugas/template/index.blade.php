@extends('layouts.dashboard')

@section('title', 'Bank Template Tugas - Portal Pembimbing')

@section('content')
<div class="dashboard-content" style="display: flex; flex-direction: column; gap: 1.5rem;">



    <!-- Header & Action Button -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <div>
            <nav style="font-size: 0.8rem; color: var(--neutral-500); margin-bottom: 0.35rem; display: flex; align-items: center; gap: 0.4rem;">
                <a href="{{ route('pembimbing.dashboard') }}" style="color: var(--neutral-600); text-decoration: none;">Dashboard</a>
                <span>/</span>
                <a href="{{ route('pembimbing.tugas.index') }}" style="color: var(--neutral-600); text-decoration: none;">Tugas</a>
                <span>/</span>
                <span style="color: var(--neutral-900); font-weight: 600;">Bank Template</span>
            </nav>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--neutral-900); margin: 0;">
                Bank Template Penugasan (PRD F-08)
            </h1>
            <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0.25rem 0 0 0;">
                Koleksi template penugasan standar untuk mempercepat pembagian tugas berkala kepada mentee aktif.
            </p>
        </div>
        <div style="display: flex; gap: 0.75rem;">
            <a href="{{ route('pembimbing.tugas.create') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1rem; background: #ffffff; color: var(--neutral-700); font-weight: 600; font-size: 0.8125rem; border: 1px solid var(--neutral-300); border-radius: 8px; text-decoration: none;">
                Kembali ke Form Tugas
            </a>
            <button type="button" onclick="openModalTambahTemplate()" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                <span>Buat Template Baru</span>
            </button>
        </div>
    </div>

    <!-- Filter Bar Card -->
    <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200); padding: 1.25rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('pembimbing.tugas.template.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="flex: 2; min-width: 220px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Cari Template</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari judul atau materi penugasan..." style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <div style="flex: 1; min-width: 180px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Kategori</label>
                <select name="kategori" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoriList as $kat)
                        <option value="{{ $kat }}" {{ request('kategori') === $kat ? 'selected' : '' }}>{{ $kat }}</option>
                    @endforeach
                </select>
            </div>

            <div style="flex: 1; min-width: 160px;">
                <label style="font-size: 0.8rem; font-weight: 600; color: var(--neutral-700); display: block; margin-bottom: 0.35rem;">Kompleksitas</label>
                <select name="kompleksitas" style="width: 100%; height: 40px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                    <option value="">Semua Tingkat</option>
                    @foreach($kompleksitasCases as $k)
                        <option value="{{ $k->value }}" {{ request('kompleksitas') === $k->value ? 'selected' : '' }}>{{ ucfirst($k->value) }}</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 0.5rem;">
                <button type="submit" style="height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Filter
                </button>
                @if(request()->hasAny(['q', 'kategori', 'kompleksitas']))
                    <a href="{{ route('pembimbing.tugas.template.index') }}" style="height: 40px; padding: 0 1rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; text-decoration: none; border-radius: 8px; display: inline-flex; align-items: center;">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Template Grid Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.25rem;">
        @forelse($templates as $tmpl)
            <div style="background: #ffffff; border-radius: 12px; border: 1px solid var(--neutral-200); padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.04); transition: transform 0.15s ease, box-shadow 0.15s ease;">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 0.5rem; margin-bottom: 0.75rem;">
                        <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.2rem 0.6rem; border-radius: 6px; background: #eff6ff; color: #0641CC;">
                            {{ $tmpl->kategori }}
                        </span>
                        <div style="display: flex; align-items: center; gap: 0.4rem;">
                            @php
                                $badgeColor = match($tmpl->kompleksitas?->value ?? (string) $tmpl->kompleksitas) {
                                    'tinggi' => ['bg' => '#fef2f2', 'text' => '#dc2626'],
                                    'sedang' => ['bg' => '#fffbeb', 'text' => '#d97706'],
                                    default => ['bg' => '#ecfdf5', 'text' => '#059669'],
                                };
                            @endphp
                            <span style="font-size: 0.75rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 6px; background: {{ $badgeColor['bg'] }}; color: {{ $badgeColor['text'] }};">
                                {{ ucfirst($tmpl->kompleksitas?->value ?? (string) $tmpl->kompleksitas) }}
                            </span>
                            @if($tmpl->is_global)
                                <span style="font-size: 0.7rem; font-weight: 600; padding: 0.2rem 0.5rem; border-radius: 6px; background: #f3e8ff; color: #7e22ce;">
                                    Global Dinas
                                </span>
                            @endif
                        </div>
                    </div>

                    <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.5rem 0; line-height: 1.35;">
                        {{ $tmpl->judul }}
                    </h3>

                    <p style="font-size: 0.85rem; color: var(--neutral-600); margin: 0 0 1rem 0; line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ $tmpl->deskripsi ?: 'Tidak ada instruksi detail tambahan.' }}
                    </p>

                    @if($tmpl->link_referensi)
                        <div style="margin-bottom: 1rem;">
                            <a href="{{ $tmpl->link_referensi }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.8rem; color: #0641CC; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem; word-break: break-all;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                                <span>Tautan Rujukan</span>
                            </a>
                        </div>
                    @endif
                </div>

                <div style="border-top: 1px solid var(--neutral-200); padding-top: 0.85rem; display: flex; justify-content: space-between; align-items: center;">
                    <a href="{{ route('pembimbing.tugas.create', ['template_id' => $tmpl->id]) }}" style="font-size: 0.8125rem; font-weight: 600; color: #0641CC; text-decoration: none; display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span>Gunakan Template</span>
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </a>

                    @if($tmpl->pembimbing_id === auth()->id() || auth()->user()->isSuperAdmin())
                        <form method="POST" action="{{ route('pembimbing.tugas.template.destroy', $tmpl) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus template ini?');" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background: transparent; border: none; cursor: pointer; color: #ef4444; padding: 0.25rem; border-radius: 4px;" title="Hapus Template">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; background: #ffffff; border-radius: 12px; border: 1px dashed var(--neutral-300); padding: 3rem 1.5rem; text-align: center;">
                <div style="width: 48px; height: 48px; border-radius: 50%; background: #eff6ff; color: #0641CC; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto;">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Belum Ada Template Penugasan</h3>
                <p style="font-size: 0.875rem; color: var(--neutral-600); margin: 0 0 1.25rem 0; max-width: 450px; margin-left: auto; margin-right: auto;">
                    Simpan penugasan yang sering Anda berikan agar dapat digunakan kembali dengan 1-klik saat membimbing angkatan magang baru.
                </p>
                <button type="button" onclick="openModalTambahTemplate()" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer;">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                    <span>Buat Template Pertama</span>
                </button>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($templates->hasPages())
        <div style="margin-top: 1rem;">
            {{ $templates->links() }}
        </div>
    @endif
</div>

<!-- Modal Tambah Template Baru -->
<div id="modalTambahTemplate" style="display: none; position: fixed; inset: 0; background: rgba(15, 23, 42, 0.6); z-index: 1000; align-items: center; justify-content: center; padding: 1.5rem; backdrop-filter: blur(4px);">
    <div style="background: #ffffff; border-radius: 14px; width: 100%; max-width: 550px; max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1); padding: 1.75rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-200); padding-bottom: 1rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--neutral-900); margin: 0;">Buat Template Tugas Baru</h3>
            <button type="button" onclick="closeModalTambahTemplate()" style="background: transparent; border: none; cursor: pointer; color: var(--neutral-500); font-size: 1.25rem;">&times;</button>
        </div>

        <form method="POST" action="{{ route('pembimbing.tugas.template.store') }}" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 0.35rem;">Judul Tugas <span style="color: #ef4444;">*</span></label>
                <input type="text" name="judul" required placeholder="Contoh: Analisis Kebutuhan Arsitektur Jaringan Sleman" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 0.35rem;">Kategori <span style="color: #ef4444;">*</span></label>
                    <input type="text" name="kategori" required placeholder="Contoh: Jaringan, Programming, Server" style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
                </div>
                <div>
                    <label style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 0.35rem;">Kompleksitas <span style="color: #ef4444;">*</span></label>
                    <select name="kompleksitas" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: #ffffff;">
                        @foreach($kompleksitasCases as $k)
                            <option value="{{ $k->value }}">{{ ucfirst($k->value) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 0.35rem;">Deskripsi & Instruksi Pengerjaan</label>
                <textarea name="deskripsi" rows="4" placeholder="Jelaskan ruang lingkup, instruksi teknis, dan ekspektasi hasil tugas..." style="width: 100%; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; line-height: 1.5;"></textarea>
            </div>

            <div>
                <label style="font-size: 0.85rem; font-weight: 600; color: var(--neutral-800); display: block; margin-bottom: 0.35rem;">Link Referensi Tambahan (Opsional)</label>
                <input type="url" name="link_referensi" placeholder="https://..." style="width: 100%; height: 42px; border: 1px solid var(--neutral-300); border-radius: 8px; padding: 0 0.85rem; font-size: 0.875rem;">
            </div>

            @if(auth()->user()->isSuperAdmin())
                <div style="display: flex; align-items: center; gap: 0.5rem;">
                    <input type="checkbox" name="is_global" id="is_global" value="1">
                    <label for="is_global" style="font-size: 0.85rem; color: var(--neutral-800);">Jadikan template global untuk seluruh pembimbing dinas</label>
                </div>
            @endif

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem; border-top: 1px solid var(--neutral-200); padding-top: 1rem;">
                <button type="button" onclick="closeModalTambahTemplate()" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); color: var(--neutral-700); font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: #0641CC; color: #ffffff; font-weight: 600; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Simpan Template
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalTambahTemplate() {
        const modal = document.getElementById('modalTambahTemplate');
        if (modal) modal.style.display = 'flex';
    }
    function closeModalTambahTemplate() {
        const modal = document.getElementById('modalTambahTemplate');
        if (modal) modal.style.display = 'none';
    }
</script>
@endsection
