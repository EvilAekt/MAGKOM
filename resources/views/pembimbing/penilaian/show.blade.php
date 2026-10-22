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
        <a href="{{ route('pembimbing.penilaian.index') }}" class="btn btn-default" style="display: inline-flex; align-items: center; gap: 0.5rem; text-decoration: none; font-weight: 600; font-size: 0.875rem;">
            <i class="fa fa-arrow-left"></i> Kembali ke Daftar Penilaian
        </a>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @if($isFinal)
                <x-gentelella.badge variant="success" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">
                    <i class="fa fa-check-circle" style="margin-right: 0.35rem;"></i> Status: Nilai Final Resmi
                </x-gentelella.badge>
                <a href="{{ route('pembimbing.penilaian.unduh', $penempatan) }}" target="_blank" class="btn btn-default" style="display: inline-flex; align-items: center; gap: 0.375rem; height: 38px; padding: 0 1rem; font-weight: 600; font-size: 0.8125rem; text-decoration: none; border-radius: 6px;">
                    <i class="fa fa-file-pdf-o" style="color: #ef4444;"></i> Unduh Lembar Nilai PDF
                </a>
            @elseif($isDraft)
                <x-gentelella.badge variant="warning" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">
                    <i class="fa fa-pencil" style="margin-right: 0.35rem;"></i> Draf Sementara (Belum Final)
                </x-gentelella.badge>
            @else
                <x-gentelella.badge variant="secondary" style="font-size: 0.8125rem; padding: 0.4rem 0.85rem;">
                    Belum Dinilai
                </x-gentelella.badge>
            @endif
        </div>
    </div>

    <!-- Mentee Profile Banner -->
    <x-gentelella.card title="Profil Peserta Magang" icon="fa fa-user">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.25rem;">
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
                <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: 8px; padding: 0.75rem 1rem;">
                    <div style="font-size: 0.75rem; font-weight: 700; color: #3730a3; text-transform: uppercase;">Nilai Akhir Saat Ini</div>
                    <div style="display: flex; align-items: baseline; gap: 0.5rem; margin-top: 0.25rem;">
                        <span style="font-size: 1.5rem; font-weight: 800; color: #0641CC;">{{ number_format((float)$penilaian->nilai_akhir, 1) }}</span>
                        <span style="font-size: 1rem; font-weight: 700; color: #4338ca;">Predikat {{ $penilaian->predikat->value ?? $penilaian->predikat }}</span>
                    </div>
                </div>
            @endif
        </div>
    </x-gentelella.card>

    <!-- Scoring Form -->
    <x-gentelella.x-panel title="Formulir Rubrik Penilaian Kompetensi" subtitle="Evaluasi kompetensi peserta magang berdasarkan 5 rubrik penilaian resmi Kominfo Sleman">
        <form action="{{ route('pembimbing.penilaian.store', $penempatan) }}" method="POST" id="formPenilaian" style="display: flex; flex-direction: column; gap: 1.5rem;">
            @csrf

            <!-- Rubrics Table -->
            <x-gentelella.table :hover="true" :striped="false" class="jambo_table bulk_action">
                <thead>
                    <tr class="headings" style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--neutral-700);">
                        <th style="padding: 0.875rem 1rem; text-align: left; width: 40px;">No</th>
                        <th style="padding: 0.875rem 1rem; text-align: left;">Kriteria Rubrik Penilaian</th>
                        <th style="padding: 0.875rem 1rem; text-align: center; width: 100px;">Bobot</th>
                        <th style="padding: 0.875rem 1rem; text-align: right; width: 180px;">Skor (0 - 100)</th>
                    </tr>
                </thead>
                <tbody>
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
                            <td style="padding: 1rem; text-align: center; font-weight: 700; color: #0641CC;">
                                <span class="rubrik-bobot" data-bobot="{{ $rubrik->bobot }}">{{ $rubrik->bobot }}%</span>
                            </td>
                            <td style="padding: 1rem; text-align: right;">
                                <input type="number" 
                                       name="skor[{{ $rubrik->id }}]" 
                                       class="rubrik-skor-input form-control" 
                                       data-bobot="{{ $rubrik->bobot }}"
                                       min="0" 
                                       max="100" 
                                       step="0.01" 
                                       required 
                                       value="{{ $val }}"
                                       placeholder="0 - 100" 
                                       style="width: 120px; height: 38px; text-align: right; display: inline-block; font-weight: 700; font-size: 0.9375rem;">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: var(--neutral-50); border-top: 2px solid var(--neutral-400);">
                        <td colspan="2" style="padding: 1rem; font-weight: 800; font-size: 0.9375rem; color: var(--neutral-900);">
                            Perkiraan Nilai Akhir Terbobot
                        </td>
                        <td style="padding: 1rem; text-align: center; font-weight: 800; color: #0641CC;">
                            100%
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                <div style="font-size: 1.25rem; font-weight: 800; color: #0641CC;" id="previewSkorAkhir">
                                    {{ $penilaian && $penilaian->nilai_akhir !== null ? number_format((float)$penilaian->nilai_akhir, 2) : '0.00' }}
                                </div>
                                <div style="font-size: 0.75rem; font-weight: 700; color: var(--neutral-600);" id="previewPredikat">
                                    Predikat: {{ $penilaian ? ($penilaian->predikat->value ?? $penilaian->predikat) : '-' }}
                                </div>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </x-gentelella.table>

            <!-- Notes Field -->
            <div>
                <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: var(--neutral-800); margin-bottom: 0.375rem;">
                    Catatan & Ulasan Evaluasi Pembimbing
                </label>
                <textarea name="catatan" rows="3" placeholder="Tuliskan catatan apresiasi, evaluasi sikap kerja, atau rekomendasi perbaikan untuk peserta magang..." class="form-control" style="width: 100%; border-radius: 8px; padding: 0.75rem; font-size: 0.875rem;">{{ old('catatan', $penilaian->catatan ?? '') }}</textarea>
            </div>

            @if($isFinal)
                <!-- Revision Reason Section if already final -->
                <div style="background: #fffbeb; border: 1px solid #fcd34d; border-radius: 8px; padding: 1.25rem;">
                    <div style="font-size: 0.875rem; font-weight: 700; color: #92400e; margin-bottom: 0.5rem; display: flex; align-items: center; gap: 0.375rem;">
                        <i class="fa fa-exclamation-triangle"></i>
                        Formulir Revisi Nilai Final (Audit Log Wajib)
                    </div>
                    <p style="font-size: 0.8125rem; color: #92400e; margin: 0 0 0.75rem 0;">
                        Nilai peserta ini sudah berstatus Final. Untuk melakukan penyesuaian skor, Anda wajib menyertakan alasan perubahan yang sah demi akuntabilitas jejak audit (AC-10).
                    </p>
                    <label style="display: block; font-size: 0.8125rem; font-weight: 600; color: #92400e; margin-bottom: 0.375rem;">
                        Alasan Revisi Nilai <span style="color: #dc2626;">*</span>
                    </label>
                    <input type="text" name="alasan" placeholder="Contoh: Koreksi input nilai rubrik kerjasama proyek berdasarkan evaluasi ulang..." class="form-control" style="width: 100%; height: 38px; border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; background: #FFFFFF;">
                </div>
            @endif

            <!-- Action Buttons -->
            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; flex-wrap: wrap; border-top: 1px solid var(--neutral-300); padding-top: 1.25rem;">
                @if(!$isFinal)
                    <button type="submit" name="is_final" value="0" class="btn btn-default" style="height: 42px; padding: 0 1.25rem; font-weight: 600; font-size: 0.875rem; border-radius: 8px; cursor: pointer;">
                        <i class="fa fa-save" style="margin-right: 0.35rem;"></i> Simpan Sebagai Draf
                    </button>
                    <button type="submit" name="is_final" value="1" onclick="return confirm('Apakah Anda yakin ingin memfinalisasi nilai peserta ini?')" class="btn btn-primary" style="height: 42px; padding: 0 1.5rem; background: #0641CC; border: none; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border-radius: 8px; cursor: pointer;">
                        <i class="fa fa-check-circle" style="margin-right: 0.35rem;"></i> Finalisasi Nilai Resmi
                    </button>
                @else
                    <button type="submit" name="is_final" value="1" onclick="return confirm('Simpan revisi nilai final ini beserta alasan perubahan?')" class="btn btn-warning" style="height: 42px; padding: 0 1.5rem; font-weight: 700; font-size: 0.875rem; border-radius: 8px; cursor: pointer;">
                        <i class="fa fa-pencil-square-o" style="margin-right: 0.35rem;"></i> Simpan Revisi Nilai Final
                    </button>
                @endif
            </div>
        </form>
    </x-gentelella.x-panel>

    <!-- Complete Internship Action (BR-10) -->
    @if($isFinal && $penempatan->status->value !== 'selesai')
        <x-gentelella.card title="Penyelesaian Periode Magang (BR-10)" icon="fa fa-graduation-cap">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <p style="font-size: 0.85rem; color: var(--neutral-600); margin: 0;">
                        Nilai peserta telah final. Klik tombol berikut untuk menyelesaikan status magang peserta dan mengaktifkan masa tenggang akun (grace period).
                    </p>
                </div>
                <form action="{{ route('pembimbing.penempatan.selesai', $penempatan) }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Tandai masa magang peserta ini telah selesai dan aktifkan masa tenggang akun?')" class="btn btn-success" style="height: 42px; padding: 0 1.25rem; color: #FFFFFF; font-weight: 700; font-size: 0.875rem; border: none; border-radius: 8px; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;">
                        <i class="fa fa-flag-checkered"></i> Tandai Selesai Magang
                    </button>
                </form>
            </div>
        </x-gentelella.card>
    @endif

    <!-- Revision History Table -->
    @if($penilaian && $penilaian->riwayats->isNotEmpty())
        <x-gentelella.x-panel title="Riwayat Jejak Perubahan Nilai Final (Audit Trail)" subtitle="Catatan akuntabilitas perubahan skor peserta pasca finalisasi">
            <x-gentelella.table :hover="true" :striped="false" class="jambo_table">
                <thead>
                    <tr class="headings" style="background: var(--neutral-100); border-bottom: 1px solid var(--neutral-300); text-align: left; color: var(--neutral-700);">
                        <th style="padding: 0.75rem 1rem;">Waktu Perubahan</th>
                        <th style="padding: 0.75rem 1rem;">Pengubah</th>
                        <th style="padding: 0.75rem 1rem;">Nilai Lama</th>
                        <th style="padding: 0.75rem 1rem;">Nilai Baru</th>
                        <th style="padding: 0.75rem 1rem;">Alasan Revisi</th>
                    </tr>
                </thead>
                <tbody>
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
            </x-gentelella.table>
        </x-gentelella.x-panel>
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
