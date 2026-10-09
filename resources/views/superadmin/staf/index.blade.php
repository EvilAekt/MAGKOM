@extends('layouts.dashboard')

@section('title', 'Manajemen Staf Internal - SuperAdmin MagKom')
@section('page_title', 'Manajemen Staf Internal')
@section('page_description', 'Kelola akun staf dinas (Admin Verifikator & Pembimbing Lapangan), reset kata sandi, dan status aktifasi.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Staf Internal</span>
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Filter & Toolbar Bar -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <form method="GET" action="{{ route('superadmin.staf.index') }}" style="display: flex; gap: 0.75rem; flex-wrap: wrap; align-items: center; flex: 1;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email staf..." style="height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.875rem; font-size: 0.875rem; min-width: 220px;">

            <select name="role" style="height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                <option value="">Semua Peran Staf</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Verifikator</option>
                <option value="pembimbing" {{ request('role') === 'pembimbing' ? 'selected' : '' }}>Pembimbing Lapangan</option>
            </select>

            <select name="status_aktif" style="height: 40px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                <option value="">Semua Status</option>
                <option value="true" {{ request('status_aktif') === 'true' ? 'selected' : '' }}>Aktif</option>
                <option value="false" {{ request('status_aktif') === 'false' ? 'selected' : '' }}>Nonaktif</option>
            </select>

            <button type="submit" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-800); color: #FFFFFF; font-weight: 600; font-size: 0.8125rem; border: none; border-radius: 8px; cursor: pointer;">
                Filter
            </button>
            @if(request()->hasAny(['search', 'role', 'status_aktif']))
                <a href="{{ route('superadmin.staf.index') }}" style="font-size: 0.8125rem; color: var(--neutral-600); text-decoration: none; padding: 0.5rem;">Reset</a>
            @endif
        </form>

        <button onclick="document.getElementById('modalTambahStaf').style.display='flex'" style="display: inline-flex; align-items: center; gap: 0.5rem; height: 40px; padding: 0 1.25rem; background: var(--blue-600); color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; transition: background 0.15s;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
            Tambah Staf Baru
        </button>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        @if($stafList->isEmpty())
            <div style="padding: 4rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <p style="font-size: 1rem; margin: 0;">Tidak ada data staf internal yang ditemukan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem; text-align: left;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-400); color: var(--neutral-700); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em;">
                            <th style="padding: 1rem 1.25rem; font-weight: 700;">Nama & Email</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Peran Akses</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Unit Kerja / Bidang</th>
                            <th style="padding: 1rem 1rem; font-weight: 700;">Status Akun</th>
                            <th style="padding: 1rem 1.25rem; font-weight: 700; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($stafList as $staf)
                            @php
                                $roleBadgeBg = $staf->role->value === 'admin' ? 'var(--purple-50, #F3E8FF)' : 'var(--blue-50)';
                                $roleBadgeText = $staf->role->value === 'admin' ? 'var(--purple-800, #6B21A8)' : 'var(--blue-700)';
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-300); transition: background 0.15s;" onmouseover="this.style.background='var(--neutral-100)'" onmouseout="this.style.background='transparent'">
                                <td style="padding: 1rem 1.25rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900);">{{ $staf->nama }}</div>
                                    <div style="font-size: 0.75rem; color: var(--neutral-600);">{{ $staf->email }}</div>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: {{ $roleBadgeBg }}; color: {{ $roleBadgeText }};">
                                        {{ $staf->role->label() }}
                                    </span>
                                </td>
                                <td style="padding: 1rem 1rem;">
                                    <span style="font-weight: 600; color: var(--neutral-800);">{{ $staf->bidang->nama_bidang ?? '-' }}</span>
                                </td>
                                <td style="padding: 1rem 1rem; white-space: nowrap;">
                                    @if($staf->status_aktif)
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--green-50); color: var(--green-800);">
                                            Aktif
                                        </span>
                                    @else
                                        <span style="display: inline-block; padding: 0.25rem 0.625rem; border-radius: 9999px; font-size: 0.75rem; font-weight: 700; background: var(--red-50); color: var(--red-700);">
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td style="padding: 1rem 1.25rem; text-align: right; white-space: nowrap;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                        <!-- Reset Password Button -->
                                        <button onclick="openModalReset('{{ $staf->id }}', '{{ addslashes($staf->nama) }}')" style="padding: 0.375rem 0.75rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: var(--neutral-800); cursor: pointer;">
                                            Reset Sandi
                                        </button>

                                        <!-- Toggle Status Form -->
                                        <form action="{{ route('superadmin.staf.toggle', $staf) }}" method="POST" style="display: inline-block;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" onclick="return confirm('{{ $staf->status_aktif ? 'Nonaktifkan akun staf ini?' : 'Aktifkan kembali akun staf ini?' }}')" style="padding: 0.375rem 0.75rem; background: {{ $staf->status_aktif ? 'var(--red-50)' : 'var(--green-50)' }}; border: 1px solid {{ $staf->status_aktif ? 'var(--red-200)' : 'var(--green-200)' }}; border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: {{ $staf->status_aktif ? 'var(--red-700)' : 'var(--green-800)' }}; cursor: pointer;">
                                                {{ $staf->status_aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($stafList->hasPages())
                <div style="padding: 1rem 1.25rem; border-top: 1px solid var(--neutral-300);">
                    {{ $stafList->links() }}
                </div>
            @endif
        @endif
    </div>
</div>

<!-- Modal Tambah Staf Baru -->
<div id="modalTambahStaf" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 520px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Tambah Akun Staf Baru</h3>
            <button onclick="document.getElementById('modalTambahStaf').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form action="{{ route('superadmin.staf.store') }}" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Nama Lengkap Staf <span style="color: var(--red-600);">*</span></label>
                <input type="text" name="nama" required placeholder="Contoh: Budi Santoso, S.Kom" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Alamat Email Dinas <span style="color: var(--red-600);">*</span></label>
                <input type="email" name="email" required placeholder="budi@slemankab.go.id" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Peran Akses <span style="color: var(--red-600);">*</span></label>
                    <select name="role" required style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                        <option value="admin">Admin Verifikator</option>
                        <option value="pembimbing">Pembimbing Lapangan</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Bidang Unit Kerja</label>
                    <select name="bidang_id" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                        <option value="">-- Tanpa Bidang Spesifik --</option>
                        @foreach($bidangList as $b)
                            <option value="{{ $b->id }}">{{ $b->nama_bidang }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Kata Sandi Awal <span style="color: var(--red-600);">*</span></label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter (huruf & angka)" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
                <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.25rem; display: block;">Staf akan diwajibkan mengganti kata sandi pada saat pertama kali login.</span>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1rem;">
                <button type="button" onclick="document.getElementById('modalTambahStaf').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Simpan Staf
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Reset Sandi Staf -->
<div id="modalResetSandi" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center; padding: 1.5rem;">
    <div style="background: var(--neutral-0); border-radius: 16px; width: 100%; max-width: 440px; padding: 2rem; box-shadow: var(--shadow-card); position: relative;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            <h3 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0;">Reset Kata Sandi Staf</h3>
            <button onclick="document.getElementById('modalResetSandi').style.display='none'" style="background: none; border: none; font-size: 1.25rem; cursor: pointer; color: var(--neutral-600);">&times;</button>
        </div>

        <form id="formResetSandi" method="POST" style="display: flex; flex-direction: column; gap: 1rem;">
            @csrf
            <p style="font-size: 0.875rem; color: var(--neutral-700); margin: 0;">
                Mereset kata sandi untuk akun <strong id="resetNamaStaf">-</strong>.
            </p>

            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">Kata Sandi Baru <span style="color: var(--red-600);">*</span></label>
                <input type="password" name="password_baru" required placeholder="Minimal 8 karakter kombinasi huruf & angka" style="width: 100%; height: 40px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 0.75rem;">
                <button type="button" onclick="document.getElementById('modalResetSandi').style.display='none'" style="height: 40px; padding: 0 1.25rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.875rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="height: 40px; padding: 0 1.5rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 700; font-size: 0.875rem; cursor: pointer;">
                    Simpan Sandi Baru
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModalReset(stafId, stafNama) {
        document.getElementById('resetNamaStaf').textContent = stafNama;
        document.getElementById('formResetSandi').action = '/superadmin/staf/' + stafId + '/reset-password';
        document.getElementById('modalResetSandi').style.display = 'flex';
    }
</script>
@endsection
