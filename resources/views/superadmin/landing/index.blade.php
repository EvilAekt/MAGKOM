@extends('layouts.dashboard')

@section('title', 'Kelola Landing Page - SuperAdmin MagKom')
@section('page_title', 'CMS Pengelolaan Landing Page & Konten Publik')

@section('breadcrumbs')
    <a href="{{ route('superadmin.dashboard') }}">SuperAdmin</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">Landing Page</span>
@endsection

@section('content')
@php
    $activeTab = request()->query('tab', 'hero');
    if (!in_array($activeTab, ['header_seo', 'hero', 'kuota', 'tahapan', 'faq', 'footer', 'kebijakan', 'theme'])) {
        $activeTab = 'hero';
    }
@endphp

<div class="sa-fluid-container" style="display: flex; flex-direction: column; gap: 1.5rem; max-width: 1080px; margin: 0 auto; width: 100%;">

    {{-- ALERT FLASH MESSAGES --}}
    @if(session('success'))
        <div style="background: #ECFDF5; border: 1.5px solid #10B981; border-radius: 8px; padding: 1rem 1.25rem; color: #065F46; font-size: 0.875rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#10B981" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span style="font-weight: 600;">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div style="background: #FEF2F2; border: 1.5px solid #EF4444; border-radius: 8px; padding: 1rem 1.25rem; color: #991B1B; font-size: 0.875rem; display: flex; align-items: center; gap: 0.75rem;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#EF4444" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            <span style="font-weight: 600;">{{ session('error') }}</span>
        </div>
    @endif

    {{-- DRAFT SANDBOX STATUS BANNER & WORKSPACE ACTIONS --}}
    @include('superadmin.landing.partials._draft_status_banner')

    {{-- INFO PANEL --}}
    <div style="background: var(--blue-tint, #EFF6FF); border: 1px solid var(--blue-tint-line, #DBEAFE); border-radius: 8px; padding: 1rem 1.25rem; color: #1E40AF; font-size: 0.875rem; line-height: 1.5; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
        <div>
            <strong>CMS Pengelolaan Konten Publik:</strong> Anda dapat mengedit Hero Banner, Alur &amp; Tahapan Magang, FAQ, Footer Kontak, serta Halaman Kebijakan Privasi tanpa harus menyentuh kode program (zero hardcode).
        </div>
        <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: center;">
            <a href="{{ route('superadmin.site-editor.index') }}" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.45rem 1rem; background: #0073AA; border: 1px solid #005177; border-radius: 6px; font-weight: 700; font-size: 0.8125rem; color: #FFFFFF; text-decoration: none; box-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><line x1="9" y1="3" x2="9" y2="21"></line></svg>
                <span>Buka WordPress Site Editor 🎨</span>
            </a>
            <a href="{{ route('landing') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; background: #FFFFFF; border: 1px solid #BFDBFE; border-radius: 6px; font-weight: 700; font-size: 0.75rem; color: #1D4ED8; text-decoration: none;">
                <span>Lihat Landing Page &rarr;</span>
            </a>
            <a href="{{ route('peta-alur') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; background: #FFFFFF; border: 1px solid #BFDBFE; border-radius: 6px; font-weight: 700; font-size: 0.75rem; color: #1D4ED8; text-decoration: none;">
                <span>Lihat Peta Alur &rarr;</span>
            </a>
            <a href="{{ route('kebijakan-privasi') }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.4rem; padding: 0.4rem 0.85rem; background: #FFFFFF; border: 1px solid #BFDBFE; border-radius: 6px; font-weight: 700; font-size: 0.75rem; color: #1D4ED8; text-decoration: none;">
                <span>Lihat Kebijakan Privasi &rarr;</span>
            </a>
        </div>
    </div>

    {{-- NAVIGASI TAB SEGMENTED --}}
    <div style="display: flex; gap: 0.5rem; border-bottom: 2px solid var(--neutral-300, #E2E8F0); padding-bottom: 0.25rem; overflow-x: auto;">
        <a href="{{ route('superadmin.landing.index', ['tab' => 'header_seo']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'header_seo' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M4 6h16M4 12h16M4 18h7"></path></svg>
            <span>1. Header &amp; SEO</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'hero']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'hero' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
            <span>2. Hero &amp; Media</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'kuota']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'kuota' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
            <span>3. Kuota &amp; Formasi</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'tahapan']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'tahapan' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
            <span>4. Alur &amp; Tahapan</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'faq']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'faq' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="12" cy="12" r="10"></circle><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"></path><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
            <span>5. FAQ (Tanya Jawab)</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'footer']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'footer' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
            <span>6. Footer &amp; Kontak</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'kebijakan']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'kebijakan' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
            <span>7. Kebijakan Privasi</span>
        </a>
        <a href="{{ route('superadmin.landing.index', ['tab' => 'theme']) }}" style="padding: 0.65rem 1.15rem; font-size: 0.875rem; font-weight: 700; border-radius: 8px 8px 0 0; text-decoration: none; display: inline-flex; align-items: center; gap: 0.45rem; transition: all 0.2s ease; {{ $activeTab === 'theme' ? 'background: var(--blue-600, #0751FF); color: #FFFFFF; border-bottom: 2px solid var(--blue-600, #0751FF);' : 'background: transparent; color: var(--neutral-700); hover:background: var(--neutral-100);' }}">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 2.69l5.66 5.66a8 8 0 1 1-11.31 0z"></path></svg>
            <span>8. Tema &amp; Desain</span>
        </a>
    </div>

    {{-- =========================================================================
         TAB 0: HEADER & SEO META
         ========================================================================= --}}
    @if($activeTab === 'header_seo')
    <x-gentelella.x-panel title="Bagian 1: Header Brand &amp; SEO Meta Tags (Zero Hardcode)">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="header_seo">

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    1. Identitas Brand di Navbar / Header Atas
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Nama Brand / Portal <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="header_brand_name" value="{{ old('header_brand_name', $cmsData['header_brand_name']) }}" required placeholder="Contoh: MAGKOM" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Sub-Judul Brand Instansi <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="header_brand_sub" value="{{ old('header_brand_sub', $cmsData['header_brand_sub']) }}" required placeholder="Contoh: Portal Magang · Diskominfo Sleman" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    2. Pengaturan SEO (Search Engine Optimization) &amp; OpenGraph Social Preview
                </h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Judul Halaman Publik (Meta Title) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="meta_title" value="{{ old('meta_title', $cmsData['meta_title']) }}" required placeholder="Contoh: MAGKOM – Portal Magang Diskominfo Sleman 2026" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Deskripsi Meta Penelusuran (Meta Description) <span style="color: var(--red-600);">*</span>
                        </label>
                        <textarea name="meta_desc" rows="3" required placeholder="Contoh: Daftar magang di Diskominfo Sleman: lihat persyaratan, kuota per bidang, dan tahapan pendaftaran." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('meta_desc', $cmsData['meta_desc']) }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    3. Daftar Menu Navigasi Navbar
                </h4>
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--neutral-200); text-align: left; font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase;">
                                <th style="padding: 0.5rem;">Label Menu</th>
                                <th style="padding: 0.5rem;">URL Target (Anchor # atau Route)</th>
                                <th style="padding: 0.5rem; text-align: center; width: 90px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="nav-items-tbody">
                            @php
                                $navItems = is_array($cmsData['header_nav']) ? $cmsData['header_nav'] : [];
                            @endphp
                            @foreach($navItems as $idx => $nav)
                            <tr class="repeater-row">
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="nav_items[{{ $idx }}][label]" value="{{ $nav['label'] ?? '' }}" placeholder="Contoh: Beranda" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="nav_items[{{ $idx }}][url]" value="{{ $nav['url'] ?? '' }}" placeholder="Contoh: #beranda" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center;">
                                    <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        <button type="button" data-action="add-nav-item" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 0.5rem 1rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                            + Tambah Menu Navigasi
                        </button>
                    </div>
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

    {{-- =========================================================================
         TAB 1: HERO & MEDIA
         ========================================================================= --}}
    @if($activeTab === 'hero')
    <x-gentelella.x-panel title="Bagian 1: Hero Banner, Persyaratan &amp; Dokumen Panduan">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="hero">

            {{-- 1. Teks Hero --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    1. Teks Utama Bagian Hero Banner
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Sub-Judul Atas (Hero Kicker)
                        </label>
                        <input type="text" name="hero_kicker" value="{{ old('hero_kicker', $cmsData['hero_kicker']) }}" placeholder="Contoh: Kabupaten Sleman" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Teks Tombol Aksi (CTA Button)
                        </label>
                        <input type="text" name="hero_cta_text" value="{{ old('hero_cta_text', $cmsData['hero_cta_text']) }}" placeholder="Contoh: Daftar Sekarang" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Judul Utama Hero (Hero Title) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="hero_title" value="{{ old('hero_title', $cmsData['hero_title']) }}" required placeholder="Contoh: Magang Kominfo Periode II" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Badge Tahun Pelaksanaan
                        </label>
                        <input type="text" name="hero_year" value="{{ old('hero_year', $cmsData['hero_year']) }}" placeholder="Contoh: 2026" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>

                    <div style="grid-column: span 2;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Keterangan Periode &amp; Tanggal Magang (Hero Period Info)
                        </label>
                        <input type="text" name="hero_period_info" value="{{ old('hero_period_info', $cmsData['hero_period_info']) }}" placeholder="Contoh: Pendaftaran 1–15 Okt 2026 · mulai 2 Nov 2026" style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                </div>
            </div>

            {{-- 2. Foto Banner --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    2. Media Foto Slide Banner (Slide 2)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Unggah Foto Banner Baru
                        </label>
                        <input type="file" name="hero_image" accept="image/jpeg,image/png,image/jpg,image/webp" style="width: 100%; font-size: 0.875rem; padding: 0.45rem 0; font-family: inherit;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">
                            Format: JPG, PNG, WEBP. Maks: 2 MB. Rekomendasi rasio 2:1 (1200x600 px).
                        </span>
                        @error('hero_image')
                            <span style="color: var(--red-600); font-size: 0.75rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="background: var(--neutral-50, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase; margin-bottom: 0.5rem;">
                            Pratinjau Foto Banner Aktif:
                        </span>
                        @if(!empty($cmsData['hero_image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsData['hero_image']))
                            <div style="position: relative;">
                                <img src="{{ asset('storage/'.$cmsData['hero_image']) }}" alt="Foto Banner Aktif" style="width: 100%; height: 140px; object-fit: cover; border-radius: 6px; border: 1px solid var(--neutral-300);">
                                <span style="display: inline-block; background: #059669; color: #FFFFFF; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; margin-top: 0.5rem;">
                                    Foto Kustom Aktif
                                </span>
                            </div>
                        @else
                            <div style="position: relative;">
                                <img src="{{ asset('images/hero_dokumentasi_diskominfo.png') }}" alt="Foto Banner Default" style="width: 100%; height: 140px; object-fit: cover; border-radius: 6px; border: 1px solid var(--neutral-300);">
                                <span style="display: inline-block; background: var(--neutral-500); color: #FFFFFF; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.5rem; border-radius: 4px; margin-top: 0.5rem;">
                                    Foto Default Bawaan Sistem
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 3. Panduan PDF --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    3. Dokumen Buku Panduan / Juknis Magang (PDF)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: start;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Unggah Dokumen PDF Panduan Baru
                        </label>
                        <input type="file" name="panduan_pdf" accept="application/pdf" style="width: 100%; font-size: 0.875rem; padding: 0.45rem 0; font-family: inherit;">
                        <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">
                            Format didukung: PDF resmi. Maks: 5 MB. Diunduh melalui tombol "Unduh Panduan PDF" publik.
                        </span>
                        @error('panduan_pdf')
                            <span style="color: var(--red-600); font-size: 0.75rem; display: block; margin-top: 0.25rem;">{{ $message }}</span>
                        @enderror
                    </div>

                    <div style="background: var(--neutral-50, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <span style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); text-transform: uppercase; margin-bottom: 0.5rem;">
                            Status Dokumen Panduan Aktif:
                        </span>
                        @if(!empty($cmsData['panduan_pdf']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsData['panduan_pdf']))
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 6px; padding: 0.75rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem; overflow: hidden;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#DC2626" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line></svg>
                                    <span style="font-size: 0.8125rem; font-weight: 700; color: var(--neutral-900); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                        {{ $cmsData['panduan_pdf_original_name'] }}
                                    </span>
                                </div>
                                <a href="{{ route('panduan.pdf') }}" target="_blank" style="padding: 0.35rem 0.75rem; background: #0641CC; color: #FFFFFF; font-size: 0.75rem; font-weight: 700; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                                    Cek File
                                </a>
                            </div>
                        @else
                            <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; background: #FFFFFF; border: 1px solid var(--neutral-300); border-radius: 6px; padding: 0.75rem 1rem;">
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline></svg>
                                    <span style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-700);">
                                        Template PDF Dinamis MagKom
                                    </span>
                                </div>
                                <a href="{{ route('panduan.pdf') }}" target="_blank" style="padding: 0.35rem 0.75rem; background: #0641CC; color: #FFFFFF; font-size: 0.75rem; font-weight: 700; border-radius: 6px; text-decoration: none; white-space: nowrap;">
                                    Pratinjau
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 4. Persyaratan & Dokumen --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    4. Persyaratan &amp; Dokumen Pendaftaran (Kolom 2 &amp; 3 Hero Card)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Judul Kolom Persyaratan
                        </label>
                        <input type="text" name="label_persyaratan" value="{{ old('label_persyaratan', $cmsData['label_persyaratan']) }}" placeholder="Contoh: Persyaratan Umum" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; margin-bottom: 0.75rem;">

                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Daftar Persyaratan (Satu poin per baris)
                        </label>
                        <textarea name="persyaratan_umum" rows="5" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('persyaratan_umum', $cmsData['persyaratan_umum']) }}</textarea>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Judul Kolom Dokumen
                        </label>
                        <input type="text" name="label_dokumen" value="{{ old('label_dokumen', $cmsData['label_dokumen']) }}" placeholder="Contoh: Dokumen Pendaftaran" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; margin-bottom: 0.75rem;">

                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Daftar Dokumen (Satu poin per baris)
                        </label>
                        <textarea name="dokumen_pendaftaran" rows="5" style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('dokumen_pendaftaran', $cmsData['dokumen_pendaftaran']) }}</textarea>
                    </div>
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>

    @if((!empty($cmsData['hero_image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsData['hero_image'])) || (!empty($cmsData['panduan_pdf']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsData['panduan_pdf'])))
        <x-gentelella.card title="Opsi Reset Media ke Default Bawaan" icon="fa fa-refresh">
            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                @if(!empty($cmsData['hero_image']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsData['hero_image']))
                    <form action="{{ route('superadmin.landing.destroy-foto') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menghapus foto banner kustom dan kembali ke foto default bawaan sistem?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="height: 38px; padding: 0 1.25rem; background: #EF4444; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Reset Foto Banner ke Default</span>
                        </button>
                    </form>
                @endif

                @if(!empty($cmsData['panduan_pdf']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($cmsData['panduan_pdf']))
                    <form action="{{ route('superadmin.landing.destroy-panduan') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin menghapus dokumen PDF kustom dan kembali ke template PDF otomatis bawaan sistem?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="height: 38px; padding: 0 1.25rem; background: #DC2626; color: #FFFFFF; border: none; border-radius: 8px; font-weight: 700; font-size: 0.8125rem; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            <span>Reset Dokumen PDF ke Template Otomatis</span>
                        </button>
                    </form>
                @endif
            </div>
        </x-gentelella.card>
    @endif
    @endif

    {{-- =========================================================================
         TAB 3: KUOTA & FORMASI INTERACTIVE COPY
         ========================================================================= --}}
    @if($activeTab === 'kuota')
    <x-gentelella.x-panel title="Bagian 3: Ketersediaan Kuota &amp; Formasi (Teks &amp; Pilihan Interaktif)">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="kuota">

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    1. Judul &amp; Instruksi Utama Section Kuota
                </h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Judul Section (Heading) <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="kuota_heading" value="{{ old('kuota_heading', $cmsData['kuota_heading']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Sub-Judul / Petunjuk Langkah Pilihan <span style="color: var(--red-600);">*</span>
                        </label>
                        <textarea name="kuota_subheading" rows="2" required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('kuota_subheading', $cmsData['kuota_subheading']) }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    2. Label Pertanyaan Interaktif &amp; Tombol Pilihan (Pills)
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Pertanyaan 1: Jenjang <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="kuota_q1_label" value="{{ old('kuota_q1_label', $cmsData['kuota_q1_label']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Pill SMK</label>
                            <input type="text" name="kuota_pill_smk" value="{{ old('kuota_pill_smk', $cmsData['kuota_pill_smk']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Pill Mahasiswa</label>
                            <input type="text" name="kuota_pill_mhs" value="{{ old('kuota_pill_mhs', $cmsData['kuota_pill_mhs']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        </div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Pertanyaan 2: Periode Bulan <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="kuota_q2_label" value="{{ old('kuota_q2_label', $cmsData['kuota_q2_label']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Teks Saat Periode Kosong
                        </label>
                        <input type="text" name="kuota_empty_note" value="{{ old('kuota_empty_note', $cmsData['kuota_empty_note']) }}" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Pertanyaan 3: Mode Magang <span style="color: var(--red-600);">*</span>
                        </label>
                        <input type="text" name="kuota_q3_label" value="{{ old('kuota_q3_label', $cmsData['kuota_q3_label']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Pill Mandiri</label>
                            <input type="text" name="kuota_pill_mandiri" value="{{ old('kuota_pill_mandiri', $cmsData['kuota_pill_mandiri']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Pill Tim/Kelompok</label>
                            <input type="text" name="kuota_pill_tim" value="{{ old('kuota_pill_tim', $cmsData['kuota_pill_tim']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    3. Keterangan Tim &amp; Indikator Meter / Gauge
                </h4>
                <div style="display: flex; flex-direction: column; gap: 1rem;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                                Label Pemilih Jumlah Tim <span style="color: var(--red-600);">*</span>
                            </label>
                            <input type="text" name="kuota_team_label" value="{{ old('kuota_team_label', $cmsData['kuota_team_label']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                                Teks Deskripsi Ring Gauge <span style="color: var(--red-600);">*</span>
                            </label>
                            <input type="text" name="kuota_gauge_desc" value="{{ old('kuota_gauge_desc', $cmsData['kuota_gauge_desc']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                        </div>
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Catatan Edukasi Pendaftaran Tim <span style="color: var(--red-600);">*</span>
                        </label>
                        <textarea name="kuota_team_note" rows="2" required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('kuota_team_note', $cmsData['kuota_team_note']) }}</textarea>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    4. Label Status Legend &amp; Teks Tombol Aksi Kartu Kuota
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Legend Masih Banyak</label>
                        <input type="text" name="kuota_legend_tersedia" value="{{ old('kuota_legend_tersedia', $cmsData['kuota_legend_tersedia']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Legend Tinggal Sedikit</label>
                        <input type="text" name="kuota_legend_hampir" value="{{ old('kuota_legend_hampir', $cmsData['kuota_legend_hampir']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Legend Penuh/Waiting</label>
                        <input type="text" name="kuota_legend_penuh" value="{{ old('kuota_legend_penuh', $cmsData['kuota_legend_penuh']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Tombol Kuota Tersedia</label>
                        <input type="text" name="kuota_btn_tersedia" value="{{ old('kuota_btn_tersedia', $cmsData['kuota_btn_tersedia']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Tombol Waiting List</label>
                        <input type="text" name="kuota_btn_waiting" value="{{ old('kuota_btn_waiting', $cmsData['kuota_btn_waiting']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Tombol Pendaftaran Tutup</label>
                        <input type="text" name="kuota_btn_tutup" value="{{ old('kuota_btn_tutup', $cmsData['kuota_btn_tutup']) }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

    {{-- =========================================================================
         TAB 4: ALUR & TAHAPAN
         ========================================================================= --}}
    @if($activeTab === 'tahapan')
    <x-gentelella.x-panel title="Bagian 4: Alur &amp; Tahapan Pendaftaran Magang (Tersinkron ke Peta Alur)">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="tahapan">

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Judul Utama Bagian Tahapan (Heading) <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="tahapan_heading" value="{{ old('tahapan_heading', $cmsData['tahapan_heading']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Teks Ringkasan 4 Poin Alur (Satu kalimat per baris) <span style="color: var(--red-600);">*</span>
                </label>
                <textarea name="tahapan_summary" rows="5" required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('tahapan_summary', $cmsData['tahapan_summary']) }}</textarea>
                <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">
                    Tips: Awali setiap baris dengan kata Pertama, Kedua, Ketiga, Keempat agar kata awal otomatis dicetak tebal.
                </span>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    4 Kartu Tahapan (Tampil di Landing Page &amp; Balon Pos Peta Alur Interaktif)
                </h4>

                <div style="display: flex; flex-direction: column; gap: 1.25rem;">
                    {{-- Pos 1 --}}
                    <div style="background: var(--neutral-50, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1.25rem;">
                        <span style="display: inline-block; background: #E11D48; color: #FFFFFF; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 4px; margin-bottom: 0.75rem;">
                            POS 01 - MERAH
                        </span>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Judul Tahap 01</label>
                                <input type="text" name="step1_title" value="{{ old('step1_title', $cmsData['tahapan_steps'][0]['title'] ?? 'Registrasi') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Deskripsi Tahap 01</label>
                                <input type="text" name="step1_desc" value="{{ old('step1_desc', $cmsData['tahapan_steps'][0]['desc'] ?? 'Calon peserta membuat akun resmi, mengisi data pribadi, dan menunggu verifikasi admin.') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    {{-- Pos 2 --}}
                    <div style="background: var(--neutral-50, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1.25rem;">
                        <span style="display: inline-block; background: #6366F1; color: #FFFFFF; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 4px; margin-bottom: 0.75rem;">
                            POS 02 - UNGU / BIRU INDIGO
                        </span>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Judul Tahap 02</label>
                                <input type="text" name="step2_title" value="{{ old('step2_title', $cmsData['tahapan_steps'][1]['title'] ?? 'Administrasi & Seleksi') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Deskripsi Tahap 02</label>
                                <input type="text" name="step2_desc" value="{{ old('step2_desc', $cmsData['tahapan_steps'][1]['desc'] ?? 'Pilih bidang, unggah berkas, lalu admin memverifikasi dan menyeleksi dalam 1–3 hari kerja.') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    {{-- Pos 3 --}}
                    <div style="background: var(--neutral-50, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1.25rem;">
                        <span style="display: inline-block; background: #0284C7; color: #FFFFFF; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 4px; margin-bottom: 0.75rem;">
                            POS 03 - BIRU TOSKA / CYAN
                        </span>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Judul Tahap 03</label>
                                <input type="text" name="step3_title" value="{{ old('step3_title', $cmsData['tahapan_steps'][2]['title'] ?? 'Pelaksanaan Magang') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Deskripsi Tahap 03</label>
                                <input type="text" name="step3_desc" value="{{ old('step3_desc', $cmsData['tahapan_steps'][2]['desc'] ?? 'Kerjakan tugas, isi logbook harian, dan unggah progres sesuai durasi magang.') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>

                    {{-- Pos 4 --}}
                    <div style="background: var(--neutral-50, #F8FAFC); border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1.25rem;">
                        <span style="display: inline-block; background: #EAB308; color: #FFFFFF; font-size: 0.75rem; font-weight: 800; padding: 0.25rem 0.65rem; border-radius: 4px; margin-bottom: 0.75rem;">
                            POS 04 - KUNING EMAS
                        </span>
                        <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 1rem;">
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Judul Tahap 04</label>
                                <input type="text" name="step4_title" value="{{ old('step4_title', $cmsData['tahapan_steps'][3]['title'] ?? 'Kelulusan') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                            <div>
                                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Deskripsi Tahap 04</label>
                                <input type="text" name="step4_desc" value="{{ old('step4_desc', $cmsData['tahapan_steps'][3]['desc'] ?? 'Unduh Lembar Nilai Magang dan sertifikat dalam bentuk PDF dari portal.') }}" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    Label Tombol Aksi di Bawah Ringkasan
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Label Tombol Unduh Dokumen PDF
                        </label>
                        <input type="text" name="tahapan_btn_pdf" value="{{ old('tahapan_btn_pdf', $cmsData['tahapan_btn_pdf']) }}" placeholder="Unduh Panduan PDF" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Label Tombol Peta Alur Interaktif
                        </label>
                        <input type="text" name="tahapan_btn_peta" value="{{ old('tahapan_btn_peta', $cmsData['tahapan_btn_peta']) }}" placeholder="Peta Alur Interaktif" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                    </div>
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

    {{-- =========================================================================
         TAB 5: FAQ (TANYA JAWAB)
         ========================================================================= --}}
    @if($activeTab === 'faq')
    <x-gentelella.x-panel title="Bagian 5: Pertanyaan yang Sering Ditanyakan (FAQ)">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="faq">

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Judul Utama Bagian FAQ (Heading) <span style="color: var(--red-600);">*</span>
                </label>
                <input type="text" name="faq_heading" value="{{ old('faq_heading', $cmsData['faq_heading']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
            </div>

            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0; padding-bottom: 0.25rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                            Kelola Kategori &amp; Daftar Tanya Jawab (FAQ)
                        </h4>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">
                            Setiap kategori memuat daftar pertanyaan dan jawaban. Anda dapat menambah atau menghapus tanpa mengetik format JSON.
                        </span>
                    </div>
                    <button type="button" data-action="add-faq-category" style="background: var(--blue-600, #0751FF); color: #FFFFFF; border: none; border-radius: 6px; padding: 0.5rem 1.15rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                        + Tambah Kategori Baru
                    </button>
                </div>

                <div id="faq-categories-container">
                    @php
                        $faqCategories = is_array($cmsData['faq_data']) ? $cmsData['faq_data'] : [];
                    @endphp
                    @foreach($faqCategories as $catIdx => $cat)
                    <div class="faq-category-card" style="background: #FFFFFF; border: 1.5px solid var(--neutral-300, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 250px;">
                                <div style="width: 140px;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">ID Kategori (Slug)</label>
                                    <input type="text" name="faq_categories[{{ $catIdx }}][id]" value="{{ $cat['id'] ?? '' }}" placeholder="contoh: umum" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.8125rem;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Nama Kategori</label>
                                    <input type="text" name="faq_categories[{{ $catIdx }}][nama]" value="{{ $cat['nama'] ?? '' }}" placeholder="Contoh: Syarat dan Tata Cara Mendaftar" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; font-weight: 600;">
                                </div>
                            </div>
                            <button type="button" data-action="remove-faq-category" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; padding: 0.4rem 0.85rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                Hapus Kategori
                            </button>
                        </div>

                        <div class="faq-questions-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
                            @php
                                $questions = is_array($cat['items'] ?? null) ? $cat['items'] : [];
                            @endphp
                            @foreach($questions as $qIdx => $qItem)
                            <div class="faq-question-item" style="background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                                    <input type="text" name="faq_categories[{{ $catIdx }}][items][{{ $qIdx }}][q]" value="{{ $qItem['q'] ?? '' }}" placeholder="Pertanyaan..." required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; font-weight: 600;">
                                    <button type="button" data-action="remove-faq-item" style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; flex-shrink: 0;">
                                        Hapus Q&amp;A
                                    </button>
                                </div>
                                <textarea name="faq_categories[{{ $catIdx }}][items][{{ $qIdx }}][a]" rows="2" placeholder="Jawaban..." required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.65rem; font-size: 0.8125rem; font-family: inherit; line-height: 1.4;">{{ $qItem['a'] ?? '' }}</textarea>
                            </div>
                            @endforeach
                        </div>

                        <div>
                            <button type="button" data-action="add-faq-question" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                                + Tambah Pertanyaan di Kategori Ini
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

    {{-- =========================================================================
         TAB 6: FOOTER & KONTAK KEDINASAN
         ========================================================================= --}}
    @if($activeTab === 'footer')
    <x-gentelella.x-panel title="Bagian 6: Footer, Kontak Kedinasan, Medsos &amp; Tautan">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="footer">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Alamat Lengkap Kantor Diskominfo <span style="color: var(--red-600);">*</span>
                    </label>
                    <textarea name="footer_alamat" rows="2" required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('footer_alamat', $cmsData['footer_alamat']) }}</textarea>
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Nomor Telepon Kantor <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="footer_telepon" value="{{ old('footer_telepon', $cmsData['footer_telepon']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Email Layanan Resmi <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="email" name="footer_email" value="{{ old('footer_email', $cmsData['footer_email']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Jam Layanan Operasional Kantor <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="footer_jam_layanan" value="{{ old('footer_jam_layanan', $cmsData['footer_jam_layanan']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Teks Hak Cipta (Copyright Bar) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="footer_copyright" value="{{ old('footer_copyright', $cmsData['footer_copyright']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>
            </div>

            {{-- 2. Visual Builder: Akun Media Sosial --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0; padding-bottom: 0.25rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    Akun Media Sosial Dinas
                </h4>
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--neutral-200); text-align: left; font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase;">
                                <th style="padding: 0.5rem; width: 25%;">Platform</th>
                                <th style="padding: 0.5rem; width: 35%;">Label / Username</th>
                                <th style="padding: 0.5rem;">URL Tautan Profil</th>
                                <th style="padding: 0.5rem; text-align: center; width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="medsos-items-tbody">
                            @php
                                $medsosList = is_array($cmsData['footer_medsos']) ? $cmsData['footer_medsos'] : [];
                            @endphp
                            @foreach($medsosList as $mIdx => $m)
                            <tr class="repeater-row">
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="footer_medsos[{{ $mIdx }}][platform]" value="{{ $m['platform'] ?? '' }}" placeholder="Contoh: Instagram" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="footer_medsos[{{ $mIdx }}][label]" value="{{ $m['label'] ?? '' }}" placeholder="Contoh: @kominfo_sleman" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="url" name="footer_medsos[{{ $mIdx }}][url]" value="{{ $m['url'] ?? '' }}" placeholder="https://..." required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center;">
                                    <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        <button type="button" data-action="add-medsos-item" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                            + Tambah Akun Medsos
                        </button>
                    </div>
                </div>
            </div>

            {{-- 3. Visual Builder: Tautan Terkait --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0; padding-bottom: 0.25rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    Tautan Terkait &amp; Portal Eksternal
                </h4>
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--neutral-200); text-align: left; font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase;">
                                <th style="padding: 0.5rem; width: 40%;">Label Tautan</th>
                                <th style="padding: 0.5rem;">URL Target (Internal atau Eksternal)</th>
                                <th style="padding: 0.5rem; text-align: center; width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tautan-items-tbody">
                            @php
                                $tautanList = is_array($cmsData['footer_tautan']) ? $cmsData['footer_tautan'] : [];
                            @endphp
                            @foreach($tautanList as $tIdx => $t)
                            <tr class="repeater-row">
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="footer_tautan[{{ $tIdx }}][label]" value="{{ $t['label'] ?? '' }}" placeholder="Contoh: Kebijakan Privasi" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="footer_tautan[{{ $tIdx }}][url]" value="{{ $t['url'] ?? '' }}" placeholder="Contoh: /kebijakan-privasi atau https://..." required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center;">
                                    <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        <button type="button" data-action="add-tautan-item" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                            + Tambah Tautan Terkait
                        </button>
                    </div>
                </div>
            </div>

            {{-- 4. Visual Builder: Tautan Kolom Akun --}}
            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.75rem 0; padding-bottom: 0.25rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    Daftar Tautan Kolom Akun &amp; Portal Masuk
                </h4>
                <div style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--neutral-200); text-align: left; font-size: 0.75rem; color: var(--neutral-600); text-transform: uppercase;">
                                <th style="padding: 0.5rem; width: 40%;">Label Masuk Portal</th>
                                <th style="padding: 0.5rem;">URL Target</th>
                                <th style="padding: 0.5rem; text-align: center; width: 80px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="akun-items-tbody">
                            @php
                                $akunList = is_array($cmsData['footer_akun']) ? $cmsData['footer_akun'] : [];
                            @endphp
                            @foreach($akunList as $aIdx => $a)
                            <tr class="repeater-row">
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="footer_akun[{{ $aIdx }}][label]" value="{{ $a['label'] ?? '' }}" placeholder="Contoh: Masuk Peserta" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem;">
                                    <input type="text" name="footer_akun[{{ $aIdx }}][url]" value="{{ $a['url'] ?? '' }}" placeholder="/login" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                                </td>
                                <td style="padding: 0.75rem 0.5rem; text-align: center;">
                                    <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                        Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top: 1rem;">
                        <button type="button" data-action="add-akun-item" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                            + Tambah Tautan Akun
                        </button>
                    </div>
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

    {{-- =========================================================================
         TAB 7: KEBIJAKAN PRIVASI
         ========================================================================= --}}
    @if($activeTab === 'kebijakan')
    <x-gentelella.x-panel title="Bagian 7: Halaman Kebijakan Privasi &amp; Perlindungan Data">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="kebijakan">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Badge Regulasi (Badge Atas) <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="kebijakan_badge" value="{{ old('kebijakan_badge', $cmsData['kebijakan_badge']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>

                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Judul Dokumen Kebijakan <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="kebijakan_title" value="{{ old('kebijakan_title', $cmsData['kebijakan_title']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>

                <div style="grid-column: span 2;">
                    <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                        Tanggal &amp; Keterangan Pembaruan <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="kebijakan_updated_at" value="{{ old('kebijakan_updated_at', $cmsData['kebijakan_updated_at']) }}" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem;">
                </div>
            </div>

            {{-- Visual Builder: Daftar Pasal Kebijakan Privasi --}}
            <div>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; flex-wrap: wrap; gap: 0.75rem;">
                    <div>
                        <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0; padding-bottom: 0.25rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                            Daftar Pasal &amp; Ketentuan Kebijakan Privasi
                        </h4>
                        <span style="font-size: 0.75rem; color: var(--neutral-600); display: block; margin-top: 0.25rem;">
                            Kelola pasal, penjelasan isi, dan butir poin dokumen perlindungan data tanpa sintaks JSON.
                        </span>
                    </div>
                    <button type="button" data-action="add-kebijakan-section" style="background: var(--blue-600, #0751FF); color: #FFFFFF; border: none; border-radius: 6px; padding: 0.5rem 1.15rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                        + Tambah Pasal Baru
                    </button>
                </div>

                <div id="kebijakan-sections-container">
                    @php
                        $kebijakanSections = is_array($cmsData['kebijakan_sections']) ? $cmsData['kebijakan_sections'] : [];
                    @endphp
                    @foreach($kebijakanSections as $secIdx => $sec)
                    <div class="kebijakan-section-card" style="background: #FFFFFF; border: 1.5px solid var(--neutral-300, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.875rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                            <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 250px;">
                                <div style="width: 80px;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Nomor</label>
                                    <input type="number" name="kebijakan_sections[{{ $secIdx }}][nomor]" value="{{ $sec['nomor'] ?? ($secIdx + 1) }}" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.875rem; text-align: center; font-weight: 700;">
                                </div>
                                <div style="flex: 1;">
                                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Judul Pasal</label>
                                    <input type="text" name="kebijakan_sections[{{ $secIdx }}][judul]" value="{{ $sec['judul'] ?? '' }}" placeholder="Contoh: Komitmen Perlindungan Data Pribadi" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; font-weight: 700;">
                                </div>
                            </div>
                            <button type="button" data-action="remove-kebijakan-section" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; padding: 0.4rem 0.85rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                Hapus Bagian
                            </button>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.25rem;">Teks Isi / Paragraf Konten</label>
                            <textarea name="kebijakan_sections[{{ $secIdx }}][konten]" rows="3" placeholder="Tuliskan penjelasan pasal..." required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.75rem; font-size: 0.8125rem; font-family: inherit; line-height: 1.45;">{{ $sec['konten'] ?? '' }}</textarea>
                        </div>

                        <div>
                            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Daftar Butir Poin / Sub-items (Opsional)</label>
                            <div class="kebijakan-bullets-container" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;">
                                @php
                                    $items = is_array($sec['items'] ?? null) ? $sec['items'] : [];
                                @endphp
                                @foreach($items as $bItem)
                                <div class="kebijakan-bullet-item" style="display: flex; gap: 0.5rem; align-items: center;">
                                    <span style="color: var(--neutral-500); font-weight: 700;">&bull;</span>
                                    <input type="text" name="kebijakan_sections[{{ $secIdx }}][items][]" value="{{ $bItem }}" placeholder="Teks butir poin..." style="flex: 1; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.8125rem;">
                                    <button type="button" data-action="remove-kebijakan-item" style="background: #FEE2E2; color: #DC2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-weight: 700;">&times;</button>
                                </div>
                                @endforeach
                            </div>
                            <button type="button" data-action="add-kebijakan-item" style="background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1; border-radius: 6px; padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                                + Tambah Butir Poin
                            </button>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

    {{-- =========================================================================
         TAB 8: TEMA & DESAIN VISUAL (THEMING ENGINE)
         ========================================================================= --}}
    @if($activeTab === 'theme')
    <x-gentelella.x-panel title="Bagian 8: Tema, Palet Warna &amp; Tipografi Landing Page">
        <form action="{{ route('superadmin.landing.update') }}" method="POST" style="display: flex; flex-direction: column; gap: 1.75rem;">
            @csrf
            @method('PUT')
            <input type="hidden" name="section" value="theme">

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    1. Palet Warna Desain (Brand Colors)
                </h4>
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
                    {{-- Primary Navy --}}
                    <div data-color-pair style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.5rem;">
                            Warna Utama (Primary Navy) <span style="color: var(--red-600);">*</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" value="{{ old('theme_primary_color', $cmsData['theme_primary_color']) }}" style="width: 44px; height: 38px; border: none; border-radius: 6px; cursor: pointer; padding: 0; background: transparent;">
                            <input type="text" name="theme_primary_color" value="{{ old('theme_primary_color', $cmsData['theme_primary_color']) }}" required maxlength="7" style="flex: 1; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.875rem; font-family: monospace;">
                        </div>
                    </div>

                    {{-- Navy Dark --}}
                    <div data-color-pair style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.5rem;">
                            Warna Navy Gelap (Header/Footer) <span style="color: var(--red-600);">*</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" value="{{ old('theme_navy_dark', $cmsData['theme_navy_dark']) }}" style="width: 44px; height: 38px; border: none; border-radius: 6px; cursor: pointer; padding: 0; background: transparent;">
                            <input type="text" name="theme_navy_dark" value="{{ old('theme_navy_dark', $cmsData['theme_navy_dark']) }}" required maxlength="7" style="flex: 1; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.875rem; font-family: monospace;">
                        </div>
                    </div>

                    {{-- Accent Indigo --}}
                    <div data-color-pair style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.5rem;">
                            Warna Aksen (Accent Color) <span style="color: var(--red-600);">*</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" value="{{ old('theme_accent_color', $cmsData['theme_accent_color']) }}" style="width: 44px; height: 38px; border: none; border-radius: 6px; cursor: pointer; padding: 0; background: transparent;">
                            <input type="text" name="theme_accent_color" value="{{ old('theme_accent_color', $cmsData['theme_accent_color']) }}" required maxlength="7" style="flex: 1; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.875rem; font-family: monospace;">
                        </div>
                    </div>

                    {{-- Gold Accent --}}
                    <div data-color-pair style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.5rem;">
                            Warna Emas (Gold Highlight) <span style="color: var(--red-600);">*</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" value="{{ old('theme_gold_color', $cmsData['theme_gold_color']) }}" style="width: 44px; height: 38px; border: none; border-radius: 6px; cursor: pointer; padding: 0; background: transparent;">
                            <input type="text" name="theme_gold_color" value="{{ old('theme_gold_color', $cmsData['theme_gold_color']) }}" required maxlength="7" style="flex: 1; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.875rem; font-family: monospace;">
                        </div>
                    </div>

                    {{-- Background Page --}}
                    <div data-color-pair style="background: #F8FAFC; border: 1px solid var(--neutral-300, #E2E8F0); border-radius: 8px; padding: 1rem;">
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.5rem;">
                            Latar Belakang (Page Background) <span style="color: var(--red-600);">*</span>
                        </label>
                        <div style="display: flex; gap: 0.5rem; align-items: center;">
                            <input type="color" value="{{ old('theme_bg_color', $cmsData['theme_bg_color']) }}" style="width: 44px; height: 38px; border: none; border-radius: 6px; cursor: pointer; padding: 0; background: transparent;">
                            <input type="text" name="theme_bg_color" value="{{ old('theme_bg_color', $cmsData['theme_bg_color']) }}" required maxlength="7" style="flex: 1; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.875rem; font-family: monospace;">
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    2. Tipografi &amp; Gaya Font Google
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Font Judul / Display (Heading) <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="theme_font_display" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: #FFFFFF;">
                            @foreach(['Lexend', 'Plus Jakarta Sans', 'Inter', 'Montserrat', 'Poppins'] as $font)
                                <option value="{{ $font }}" {{ old('theme_font_display', $cmsData['theme_font_display']) === $font ? 'selected' : '' }}>{{ $font }} (Google Fonts)</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Font Konten / Body Text <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="theme_font_body" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: #FFFFFF;">
                            @foreach(['Source Sans 3', 'Inter', 'Roboto', 'Open Sans', 'Nunito Sans'] as $font)
                                <option value="{{ $font }}" {{ old('theme_font_body', $cmsData['theme_font_body']) === $font ? 'selected' : '' }}>{{ $font }} (Google Fonts)</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <div>
                <h4 style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0; padding-bottom: 0.5rem; border-bottom: 2px solid var(--blue-600); display: inline-block;">
                    3. Kelengkungan Kartu (Radius) &amp; Gaya Tombol
                </h4>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem;">
                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Kelengkungan Sudut Kartu (Border Radius) <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="theme_border_radius" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: #FFFFFF;">
                            <option value="0px" {{ old('theme_border_radius', $cmsData['theme_border_radius']) === '0px' ? 'selected' : '' }}>Tajam / Flat (0px)</option>
                            <option value="6px" {{ old('theme_border_radius', $cmsData['theme_border_radius']) === '6px' ? 'selected' : '' }}>Halus (6px)</option>
                            <option value="12px" {{ old('theme_border_radius', $cmsData['theme_border_radius']) === '12px' ? 'selected' : '' }}>Standar Modern (12px)</option>
                            <option value="16px" {{ old('theme_border_radius', $cmsData['theme_border_radius']) === '16px' ? 'selected' : '' }}>Bulat Lembut (16px)</option>
                            <option value="24px" {{ old('theme_border_radius', $cmsData['theme_border_radius']) === '24px' ? 'selected' : '' }}>Sangat Melengkung (24px)</option>
                        </select>
                    </div>

                    <div>
                        <label style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
                            Gaya Tombol Aksi (Button Style) <span style="color: var(--red-600);">*</span>
                        </label>
                        <select name="theme_btn_style" required style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; background: #FFFFFF;">
                            <option value="rounded" {{ old('theme_btn_style', $cmsData['theme_btn_style']) === 'rounded' ? 'selected' : '' }}>Rounded (8px)</option>
                            <option value="pill" {{ old('theme_btn_style', $cmsData['theme_btn_style']) === 'pill' ? 'selected' : '' }}>Pill Capsule (9999px)</option>
                            <option value="sharp" {{ old('theme_btn_style', $cmsData['theme_btn_style']) === 'sharp' ? 'selected' : '' }}>Sharp / Square (0px)</option>
                        </select>
                    </div>
                </div>
            </div>

            @include('superadmin.landing.partials._form_actions')
        </form>
    </x-gentelella.x-panel>
    @endif

</div>

@push('scripts')
    @vite(['resources/js/pages/superadmin/cms-builder.js'])
@endpush
@endsection
