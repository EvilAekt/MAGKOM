@extends('layouts.dashboard')

@section('title', 'Instrumen Penilaian - Pembimbing MagKom')
@section('page_title', 'Instrumen Penilaian Magang')
@section('page_description', 'Evaluasi kompetensi peserta magang berdasarkan 5 rubrik penilaian resmi Kominfo Sleman.')

@section('breadcrumbs')
    <a href="{{ route('pembimbing.penilaian.index') }}" class="breadcrumb-item">Penilaian Magang</a>
    <span class="breadcrumb-separator">/</span>
    <span class="breadcrumb-current">{{ $penempatan->peserta->nama ?? 'Peserta' }}</span>
@endsection

@section('content')
@php
    $peserta = $penempatan->peserta;
    $profil = $peserta->profil;
    $isFinal = $penilaian && $penilaian->status->value === 'final';
    $isDraft = $penilaian && $penilaian->status->value === 'draft';
    
    // Map existing detail scores
    $detailScores = [];
    if ($penilaian && $penilaian->details) {
        foreach ($penilaian->details as $d) {
            $detailScores[$d->rubrik_id] = $d->skor;
        }
    }
@endphp

<div style="display: flex; flex-direction: column; gap: 1.5rem;">
    <!-- Top Action Bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
        <a href="{{ route('pembimbing.penilaian.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--neutral-700); text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Kembali ke Daftar Penilaian
        </a>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if($isFinal)
                <span style="padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: var(--green-50); color: var(--green-800);">
                    ✓ Status: Nilai Final Resmi
                </span>
                <a href="{{ route('pembimbing.penilaian.unduh', $penempatan) }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 38px; padding: 0 1rem; background: var(--neutral-0); border: 1px solid var(--neutral-400); color: var(--neutral-800); font-weight: 600; font-size: 0.8125rem; text-decoration: none; border-radius: 6px; transition: background 0.15s;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
                    Unduh Lembar Nilai PDF
                </a>
            @elseif($isDraft)
                <span style="padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: var(--yellow-50); color: var(--yellow-800);">
                    Draf Sementara (Belum Final)
                </span>
            @else
                <span style="padding: 0.375rem 0.875rem; border-radius: 9999px; font-size: 0.8125rem; font-weight: 700; background: var(--neutral-200); color: var(--neutral-700);">
                    Belum Dinilai
                </span>
            @endif
        </div>
    </div>

    <!-- Mentee Profile Banner -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
        <div>
            <div style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-600); text-transform: uppercase;">Nama Peserta</div>
            <div style="font-size: 1.0625rem; font-weight: 800; color: var(--neutral-900); margin-top: 0.25rem;">{{ $peserta->nama ?? '-' }}</div>
            <div style="font-size: 0.8125rem; color: var(--neutral-600);">{{ $peserta->email ?? '-' }}</div>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-600); text-transform: uppercase;">Institusi / Jurusan</div>
            <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin-top: 0.25rem;">{{ $profil->institusi ?? ($profil->asal_instansi ?? '-') }}</div>
            <div style="font-size: 0.8125rem; color: var(--neutral-600);">{{ $profil->jurusan?->nama_jurusan ?? ($profil->jurusan ?? '-') }} ({{ strtoupper($profil->jenjang?->value ?? (string)($profil->jenjang ?? '-')) }})</div>
        </div>
        <div>
            <div style="font-size: 0.75rem; font-weight: 600; color: var(--neutral-600); text-transform: uppercase;">Bidang Magang</div>
            <div style="font-size: 0.9375rem; font-weight: 700; color: var(--neutral-900); margin-top: 0.25rem;">{{ $penempatan->bidang->nama_bidang ?? '-' }}</div>
            <div style="font-size: 0.8125rem; color: var(--neutral-600);">Status: {{ ucfirst($penempatan->status->value ?? 'Aktif') }}</div>
        </div>
        @if($penilaian && $penilaian->nilai_akhir !== null)
            <div style="background: var(--blue-50); border: 1px solid var(--blue-200); border-radius: 8px; padding: 0.75rem 1rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--blue-800); text-transform: uppercase;">Nilai Akhir Saat Ini</div>
                <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                    <span style="font-size: 1.5rem; font-weight: 800; color: var(--blue-900);">{{ number_format((float)$penilaian->nilai_akhir, 1) }}</span>
                    <span style="font-size: 1rem; font-weight: 700; color: var(--blue-700);">Predikat {{ $penilaian->predikat->value ?? $penilaian->predikat }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Scoring Form -->
    <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
        <h2 style="font-size: 1.125rem; font-weight: 800; color: var(--neutral-900); margin: 0 0 1.25rem 0; border-bottom: 1px solid var(--neutral-300); padding-bottom: 0.75rem;">
            Formulir Rubrik Penilaian Kompetensi
        </h2>

        <form action="{{ route('pembimbing.penilaian.store', $penempatan) }}" method="POST" id="formPenilaian" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            <!-- Rubrics Table -->
            <div style="overflow-x: auto; border: 1px solid var(--neutral-300); border-radius: 8px;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.875rem;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-700);">
                            <th style="padding: 0.875rem 1rem; text-align: left; width: 40px;">No</th>
                            <th style="padding: 0.875rem 1rem; text-align: left;">Kriteria Rubrik Penilaian</th>
                            <th style="padding: 0.875rem 1rem; text-align: center; width: 100px;">Bobot</th>
                            <th style="padding: 0.875rem 1rem; text-align: right; width: 180px;">Skor (0 - 100)</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($rubrikAktif as $index => $rubrik)
                            @php
                                $val = old("skor.{$rubrik->id}", $detailScores[$rubrik->id] ?? '');
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-300);">
                                <td style="padding: 1rem; font-weight: 600; color: var(--neutral-600);">
                                    {{ $index + 1 }}
                                </td>
                                <td style="padding: 1rem;">
                                    <div style="font-weight: 700; color: var(--neutral-900); font-size: 0.9375rem;">
                                        {{ $rubrik->kriteria }}
                                    </div>
                                    <div style="font-size: 0.8125rem; color: var(--neutral-600); margin-top: 0.25rem; line-height: 1.4;">
                                        {{ $rubrik->deskripsi }}
                                    </div>
                                </td>
                                <td style="padding: 1rem; text-align: center; font-weight: 700; color: var(--blue-700);">
                                    <span class="rubrik-bobot" data-bobot="{{ $rubrik->bobot }}">{{ $rubrik->bobot }}%</span>
                                </td>
                                <td style="padding: 1rem; text-align: right;">
                                    <input type="number" 
                                           name="skor[{{ $rubrik->id }}]" 
                                           class="rubrik-skor-input" 
                                           data-bobot="{{ $rubrik->bobot }}"
                                           min="0" 
                                           max="100" 
                                           step="0.01" 
                                           required 
                                           value="{{ $val }}"
                                           placeholder="0 - 100" 
                                           style="width: 120px; height: 38px; text-align: right; padding: 0 0.75rem; border: 1px solid var(--neutral-400); border-radius: 6px; font-weight: 700; font-size: 0.9375rem;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr style="background: var(--neutral-50); border-top: 2px solid var(--neutral-400);">
                            <td colspan="2" style="padding: 1rem; font-weight: 800; font-size: 0.9375rem; color: var(--neutral-900);">
                                Perkiraan Nilai Akhir Terbobot
                            </td>
                            <td style="padding: 1rem; text-align: center; font-weight: 800; color: var(--blue-800);">
                                100%
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                    <div style="font-size: 1.25rem; font-weight: 800; color: var(--blue-700);" id="previewSkorAkhir">
                                        {{ $penilaian && $penilaian->nilai_akhir !== null ? number_format((float)$penilaian->nilai_akhir, 2) : '0.00' }}
                                    </div>
                                    <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600);" id="previewPredikat">
                                        Predikat: {{ $penilaian ? ($penilaian->predikat->value ?? $penilaian->predikat) : '-' }}
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Notes Field -->
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Catatan & Ulasan Evaluasi Pembimbing
                </label>
                <textarea name="catatan" rows="3" placeholder="Tuliskan catatan apresiasi, evaluasi sikap kerja, atau rekomendasi perbaikan untuk peserta magang..." style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0.75rem; font-size: 0.875rem; font-family: inherit;">{{ old('catatan', $penilaian->catatan ?? '') }}</textarea>
            </div>

            @if($isFinal)
                <!-- Revision Reason Section if already final -->
                <div style="background: var(--yellow-50); border: 1px solid var(--yellow-300); border-radius: 8px; padding: 1.25rem;">
                    <div style="font-size: 0.875rem; font-weight: 700; color: var(--yellow-900); margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                        Formulir Revisi Nilai Final (Audit Log Wajib)
                    </div>
                    <p style="font-size: 0.8125rem; color: var(--yellow-800); margin: 0 0 0.75rem 0;">
                        Nilai peserta ini sudah berstatus Final. Untuk melakukan penyesuaian skor, Anda wajib menyertakan alasan perubahan yang sah demi akuntabilitas jejak audit (AC-10).
                    </p>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--yellow-900); margin-bottom: 0.375rem;">
                        Alasan Revisi Nilai <span style="color: var(--red-600);">*</span>
                    </label>
                    <input type="text" name="alasan" placeholder="Contoh: Koreksi input nilai rubrik kerjasama proyek berdasarkan evaluasi ulang..." style="width: 100%; height: 38px; border: 1px solid var(--yellow-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: #FFFFFF;">
                </div>
            @endif

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap; border-top: 1px solid var(--neutral-300); padding-top: 1.25rem;">
                @if(!$isFinal)
                    <button type="submit" name="is_final" value="0" style="height: 42px; padding: 0 1.25rem; background: var(--neutral-100); border: 1px solid var(--neutral-400); color: var(--neutral-800); font-weight: 600; font-size: 0.875rem; border-radius: 8px; cursor: pointer;">
                        Simpan Sebagai Draf
                    </button>
                    <button type="submit" name="is_final" value="1" onclick="return confirm('Apakah Anda yakin ingin memfinalisasi nilai peserta ini?')" style="height: 42px; padding: 0 1.5rem; background: var(--blue-600); border: none; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; cursor: pointer;">
                        Finalisasi Nilai Resmi
                    </button>
                @else
                    <button type="submit" name="is_final" value="1" onclick="return confirm('Simpan revisi nilai final ini beserta alasan perubahan?')" style="height: 42px; padding: 0 1.5rem; background: var(--yellow-600, #D97706); border: none; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; cursor: pointer;">
                        Simpan Revisi Nilai Final
                    </button>
                @endif
            </div>
        </form>
    </div>

    <!-- Complete Internship Action (BR-10) -->
    @if($isFinal && $penempatan->status->value !== 'selesai')
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.5rem; box-shadow: var(--shadow-subtle); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
            <div>
                <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 0.25rem 0;">
                    Penyelesaian Periode Magang (BR-10)
                </h3>
                <p style="font-size: 0.8125rem; color: var(--neutral-600); margin: 0;">
                    Nilai peserta telah final. Klik tombol berikut untuk menyelesaikan status magang peserta dan mengaktifkan masa tenggang akun (grace period).
                </p>
            </div>
            <form action="{{ route('pembimbing.penempatan.selesai', $penempatan) }}" method="POST">
                @csrf
                <button type="submit" onclick="return confirm('Tandai masa magang peserta ini telah selesai dan aktifkan masa tenggang akun?')" style="height: 42px; padding: 0 1.25rem; background: #059669; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer;">
                    Tandai Selesai Magang
                </button>
            </form>
        </div>
    @endif

    <!-- Revision History Table -->
    @if($penilaian && $penilaian->riwayats->isNotEmpty())
        <div style="background: var(--neutral-0); border: 1px solid var(--neutral-400); border-radius: 12px; padding: 1.75rem; box-shadow: var(--shadow-subtle);">
            <h3 style="font-size: 1rem; font-weight: 700; color: var(--neutral-900); margin: 0 0 1rem 0;">
                Riwayat Jejak Perubahan Nilai Final (Audit Trail)
            </h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.8125rem;">
                    <thead>
                        <tr style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300); text-align: left; color: var(--neutral-700);">
                            <th style="padding: 0.75rem 1rem;">Waktu Perubahan</th>
                            <th style="padding: 0.75rem 1rem;">Pengubah</th>
                            <th style="padding: 0.75rem 1rem;">Nilai Lama</th>
                            <th style="padding: 0.75rem 1rem;">Nilai Baru</th>
                            <th style="padding: 0.75rem 1rem;">Alasan Revisi</th>
                        </tr>
                    </thead>
                    <tbody style="divide-y: 1px solid var(--neutral-300);">
                        @foreach($penilaian->riwayats as $riwayat)
                            @php
                                $nilaiLamaAkhir = is_array($riwayat->nilai_lama) ? ($riwayat->nilai_lama['nilai_akhir'] ?? null) : null;
                                $nilaiBaruAkhir = is_array($riwayat->nilai_baru) ? ($riwayat->nilai_baru['nilai_akhir'] ?? null) : null;
                                $predikatLama = is_array($riwayat->nilai_lama) ? ($riwayat->nilai_lama['predikat'] ?? '') : '';
                                $predikatBaru = is_array($riwayat->nilai_baru) ? ($riwayat->nilai_baru['predikat'] ?? '') : '';
                                $predikatLamaStr = is_object($predikatLama) ? ($predikatLama->value ?? '') : (string)$predikatLama;
                                $predikatBaruStr = is_object($predikatBaru) ? ($predikatBaru->value ?? '') : (string)$predikatBaru;
                            @endphp
                            <tr style="border-bottom: 1px solid var(--neutral-300);">
                                <td style="padding: 0.75rem 1rem;">{{ $riwayat->waktu ? $riwayat->waktu->translatedFormat('d M Y H:i') : '-' }}</td>
                                <td style="padding: 0.75rem 1rem; font-weight: 600;">{{ $riwayat->pengubah?->nama ?? '-' }}</td>
                                <td style="padding: 0.75rem 1rem;">
                                    {{ $nilaiLamaAkhir !== null ? number_format((float)$nilaiLamaAkhir, 2) : '-' }}
                                    @if($predikatLamaStr) ({{ $predikatLamaStr }}) @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; font-weight: 700; color: #0641CC;">
                                    {{ $nilaiBaruAkhir !== null ? number_format((float)$nilaiBaruAkhir, 2) : '-' }}
                                    @if($predikatBaruStr) ({{ $predikatBaruStr }}) @endif
                                </td>
                                <td style="padding: 0.75rem 1rem; color: var(--neutral-800); font-style: italic;">"{{ $riwayat->alasan }}"</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inputs = document.querySelectorAll('.rubrik-skor-input');
        const previewEl = document.getElementById('previewSkorAkhir');
        const predikatEl = document.getElementById('previewPredikat');

        function calculateScore() {
            let total = 0;
            inputs.forEach(input => {
                const val = parseFloat(input.value) || 0;
                const bobot = parseFloat(input.dataset.bobot) || 0;
                total += (val * bobot) / 100;
            });

            previewEl.textContent = total.toFixed(2);

            let predikat = 'E';
            if (total >= 85) predikat = 'A (Sangat Baik)';
            else if (total >= 70) predikat = 'B (Baik)';
            else if (total >= 55) predikat = 'C (Cukup)';
            else if (total >= 40) predikat = 'D (Kurang)';
            else predikat = 'E (Sangat Kurang)';

            predikatEl.textContent = 'Predikat: ' + predikat;
        }

        inputs.forEach(input => {
            input.addEventListener('input', calculateScore);
        });
    });
</script>
@endsection
