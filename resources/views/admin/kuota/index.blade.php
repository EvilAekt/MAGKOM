@extends('layouts.dashboard')

@section('title', 'Manajemen Kuota - Admin MagKom Sleman')
@section('page_title', 'Manajemen Kuota Magang Per Jurusan')
@section('page_description', 'Kelola alokasi jumlah kursi kuota umum magang per kejuruan pada setiap periode bulan.')

@section('breadcrumbs')
    <span class="breadcrumb-current">Manajemen Kuota</span>
@endsection

@section('page_actions')
    @if($selectedBulan)
        <button type="button" onclick="openTambahKuotaModal()" style="display: inline-flex; align-items: center; gap: 0.5rem; background: var(--blue-600); color: #FFFFFF; border: none; padding: 0.55rem 1.25rem; border-radius: 8px; font-weight: 600; font-size: 0.875rem; cursor: pointer; box-shadow: var(--shadow-subtle);">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            <span>Alokasikan Kuota Jurusan</span>
        </button>
    @endif
@endsection

@section('content')
<div style="display: flex; flex-direction: column; gap: 1.5rem;">

    <!-- Filter & Pilihan Bulan -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.25rem 1.5rem; box-shadow: var(--shadow-subtle);">
        <form method="GET" action="{{ route('admin.kuota.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-end;">
            <div style="display: flex; flex-direction: column; gap: 0.375rem; flex: 1; min-width: 260px;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Pilih Periode Bulan Magang</label>
                <select name="bulan_magang_id" onchange="this.form.submit()" style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem; background: var(--neutral-0);">
                    @foreach($bulanList as $b)
                        <option value="{{ $b->id }}" {{ $selectedBulan && $selectedBulan->id === $b->id ? 'selected' : '' }}>
                            {{ $b->bulan_mulai?->translatedFormat('F Y') }} (Pendaftaran: {{ \Carbon\Carbon::parse($b->tgl_buka)->translatedFormat('d M') }} – {{ \Carbon\Carbon::parse($b->tgl_tutup)->translatedFormat('d M Y') }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    <!-- Data Table Card -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; overflow: hidden; box-shadow: var(--shadow-subtle);">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--neutral-400); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-800); margin: 0;">
                Alokasi Kuota Jurusan: {{ $selectedBulan ? $selectedBulan->bulan_mulai?->translatedFormat('F Y') : '-' }}
            </h3>
            <span style="font-size: 0.8125rem; color: var(--neutral-600);">
                Total: {{ $selectedBulan ? $selectedBulan->kuota->count() : 0 }} Jurusan Terdaftar
            </span>
        </div>

        @if(!$selectedBulan || $selectedBulan->kuota->isEmpty())
            <div style="padding: 3.5rem 1.5rem; text-align: center; color: var(--neutral-600);">
                <div style="width: 56px; height: 56px; background: var(--neutral-200); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; color: var(--neutral-500);">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <line x1="12" y1="8" x2="12" y2="12"></line>
                        <line x1="12" y1="16" x2="12.01" y2="16"></line>
                    </svg>
                </div>
                <h4 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">Belum Ada Kuota Terdaftar</h4>
                <p style="font-size: 0.875rem; max-width: 460px; margin: 0 auto;">Belum ada alokasi kuota jurusan untuk periode bulan ini. Klik tombol "Alokasikan Kuota Jurusan" di atas untuk menambahkan.</p>
            </div>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-200); border-bottom: 1px solid var(--neutral-400); height: 52px;">
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Nama Jurusan</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Kategori Jenjang</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center;">Total Kuota</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center;">Terisi</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: center;">Sisa Kuota</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">Status</th>
                            <th style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800); text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedBulan->kuota as $k)
                            @php
                                $sisa = max(0, $k->jumlah - $k->terisi);
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-400); height: 56px;">
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: var(--neutral-800);">
                                    {{ $k->jurusan?->nama_jurusan ?? 'Semua Jurusan' }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; padding: 0.2rem 0.5rem; border-radius: 4px; background: var(--blue-tint); color: var(--blue-700);">
                                        {{ $k->jurusan?->kategori === 'smk' ? 'SMK' : 'Mahasiswa (D3/S1)' }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: var(--neutral-800);">
                                    {{ $k->jumlah }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 600; color: var(--amber-800);">
                                    {{ $k->terisi }}
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: center; font-weight: 700; color: {{ $sisa > 0 ? 'var(--green-700)' : 'var(--red-700)' }};">
                                    {{ $sisa }}
                                </td>
                                <td style="padding: 0.75rem 1rem;">
                                    <span class="role-chip-badge" style="background: {{ $k->aktif ? 'var(--green-100)' : 'var(--neutral-200)' }}; color: {{ $k->aktif ? 'var(--green-700)' : 'var(--neutral-700)' }}; font-weight: 600;">
                                        {{ $k->aktif ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td style="padding: 0.75rem 1rem; text-align: right;">
                                    <div style="display: inline-flex; gap: 0.5rem; align-items: center;">
                                        <button type="button" onclick="openEditKuotaModal({{ $k->id }}, {{ $k->jumlah }}, {{ $k->terisi }}, '{{ addslashes($k->jurusan?->nama_jurusan) }}')" style="padding: 0.375rem 0.75rem; background: var(--neutral-200); border: 1px solid var(--neutral-400); border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                                            Edit
                                        </button>
                                        <form method="POST" action="{{ route('admin.kuota.toggle', $k) }}" style="display: inline;" onsubmit="return confirm('Ubah status aktif kuota jurusan ini?');">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" style="padding: 0.375rem 0.75rem; background: {{ $k->aktif ? 'var(--red-100)' : 'var(--green-100)' }}; color: {{ $k->aktif ? 'var(--red-700)' : 'var(--green-700)' }}; border: 1px solid {{ $k->aktif ? 'var(--red-300)' : 'var(--green-300)' }}; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                                                {{ $k->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>

<!-- MODAL TAMBAH KUOTA -->
@if($selectedBulan)
<div id="modal-tambah-kuota" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 460px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Tambah Kuota Jurusan</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;">
            Periode: <strong>{{ $selectedBulan->bulan_mulai?->translatedFormat('F Y') }}</strong>
        </p>

        <form method="POST" action="{{ route('admin.kuota.store') }}">
            @csrf
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Pilih Jurusan <span style="color: var(--red-600);">*</span></label>
                <select name="jurusan_id" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                    <option value="">-- Pilih Jurusan --</option>
                    @foreach($jurusanList as $jur)
                        <option value="{{ $jur->id }}">{{ $jur->nama_jurusan }} ({{ strtoupper($jur->kategori) }})</option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Jumlah Kuota Kursi <span style="color: var(--red-600);">*</span></label>
                <input type="number" name="jumlah" min="1" max="100" value="5" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeTambahKuotaModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Simpan Kuota
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT KUOTA -->
<div id="modal-edit-kuota" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 999; align-items: center; justify-content: center;">
    <div style="background: var(--neutral-0); border-radius: 12px; max-width: 460px; width: 90%; padding: 1.5rem; box-shadow: var(--shadow-subtle);">
        <h3 style="font-size: 1.125rem; font-weight: 700; color: var(--neutral-800); margin: 0 0 0.5rem 0;">Perbarui Alokasi Kuota</h3>
        <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0 0 1.25rem 0;" id="edit-kuota-jurusan-title"></p>

        <form method="POST" id="form-edit-kuota" action="">
            @csrf
            @method('PUT')
            <input type="hidden" name="bulan_magang_id" value="{{ $selectedBulan->id }}">

            <div style="display: flex; flex-direction: column; gap: 0.375rem; margin-bottom: 1.25rem;">
                <label style="font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800);">Jumlah Kuota Baru (Tidak boleh kurang dari terisi)</label>
                <input type="number" id="edit-input-jumlah" name="jumlah" required style="height: 42px; border: 1px solid var(--neutral-500); border-radius: 8px; padding: 0 0.75rem; font-size: 0.875rem;">
                <small id="edit-info-terisi" style="font-size: 0.75rem; color: var(--neutral-600);"></small>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.5rem;">
                <button type="button" onclick="closeEditKuotaModal()" style="padding: 0.5rem 1rem; background: var(--neutral-200); border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Batal
                </button>
                <button type="submit" style="padding: 0.5rem 1.25rem; background: var(--blue-600); color: #FFFFFF; border: none; border-radius: 6px; font-weight: 600; font-size: 0.8125rem; cursor: pointer;">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<script>
    function openTambahKuotaModal() {
        document.getElementById('modal-tambah-kuota').style.display = 'flex';
    }
    function closeTambahKuotaModal() {
        document.getElementById('modal-tambah-kuota').style.display = 'none';
    }

    function openEditKuotaModal(kuotaId, jumlah, terisi, namaJurusan) {
        document.getElementById('edit-kuota-jurusan-title').innerText = 'Jurusan: ' + namaJurusan;
        document.getElementById('edit-input-jumlah').value = jumlah;
        document.getElementById('edit-input-jumlah').min = terisi;
        document.getElementById('edit-info-terisi').innerText = 'Saat ini telah terisi: ' + terisi + ' kursi.';
        document.getElementById('form-edit-kuota').action = '/admin/kuota/' + kuotaId;
        document.getElementById('modal-edit-kuota').style.display = 'flex';
    }
    function closeEditKuotaModal() {
        document.getElementById('modal-edit-kuota').style.display = 'none';
    }
</script>
@endsection
