<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AkunPesertaDiajukanSeeder extends Seeder
{
    /**
     * Seed 2 akun peserta magang dengan status pendaftaran 'diajukan'
     * untuk keperluan pengujian alur verifikasi berkas dan keputusan admin.
     */
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');
        $now = now();

        Storage::disk('public')->makeDirectory('profil-peserta');
        Storage::disk('private')->makeDirectory('documents');

        $samplePdfPath = 'pendaftaran/2/doc_1_sample.pdf';
        $hasSamplePdf = Storage::disk('private')->exists($samplePdfPath);

        $bulanAktif = BulanMagang::where('aktif', true)->first() ?? BulanMagang::first();

        $accounts = [
            [
                'email' => 'peserta.diajukan1@gmail.com',
                'nama' => 'Bagas Saputra',
                'nim_nisn' => '21/473892/TK/52101',
                'institusi' => 'Universitas Gadjah Mada',
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => 18, // Teknik Informatika (S1 / Mahasiswa)
                'no_hp' => '081223344551',
                'alamat' => 'Jl. Kaliurang KM 5, Mlati, Sleman, D.I. Yogyakarta',
                'nama_kontak_darurat' => 'Bambang Saputra',
                'no_kontak_darurat' => '081299887711',
                'hubungan_kontak_darurat' => 'Orang Tua',
                'diajukan_at' => $now->copy()->subDays(1),
            ],
            [
                'email' => 'peserta.diajukan2@gmail.com',
                'nama' => 'Anisa Rahmawati',
                'nim_nisn' => '0067823412',
                'institusi' => 'SMK Negeri 1 Sleman',
                'jenjang' => JenjangEnum::Smk,
                'jurusan_id' => 4, // Rekayasa Perangkat Lunak (SMK)
                'no_hp' => '081223344552',
                'alamat' => 'Jl. Magelang KM 9, Mlati, Sleman, D.I. Yogyakarta',
                'nama_kontak_darurat' => 'Rahmat Hidayat',
                'no_kontak_darurat' => '081299887722',
                'hubungan_kontak_darurat' => 'Orang Tua',
                'diajukan_at' => $now->copy()->subHours(6),
            ],
        ];

        DB::transaction(function () use ($accounts, $defaultPassword, $now, $bulanAktif, $hasSamplePdf, $samplePdfPath) {
            foreach ($accounts as $acc) {
                $slug = Str::slug($acc['nama'], '_');
                $fotoPath = "profil-peserta/{$slug}.svg";

                $this->createAvatarAsset($fotoPath, $acc['nama']);

                // 1. User Akun Peserta
                $user = User::where('email', $acc['email'])->first() ?? new User;
                $user->nama = $acc['nama'];
                $user->email = $acc['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::Peserta;
                $user->foto = $fotoPath;
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();

                // 2. Profil Peserta
                PesertaProfil::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nim_nisn' => $acc['nim_nisn'],
                        'institusi' => $acc['institusi'],
                        'jurusan_id' => $acc['jurusan_id'],
                        'jenjang' => $acc['jenjang'],
                        'no_hp' => $acc['no_hp'],
                        'alamat' => $acc['alamat'],
                        'foto' => $fotoPath,
                        'nama_kontak_darurat' => $acc['nama_kontak_darurat'],
                        'no_kontak_darurat' => $acc['no_kontak_darurat'],
                        'hubungan_kontak_darurat' => $acc['hubungan_kontak_darurat'],
                        'persetujuan_wali_at' => $now,
                    ]
                );

                // 3. Kuota Terkait Jurusan Peserta
                $kuota = Kuota::firstOrCreate(
                    [
                        'bulan_magang_id' => $bulanAktif->id,
                        'jurusan_id' => $acc['jurusan_id'],
                    ],
                    [
                        'jumlah' => 10,
                        'terisi' => 2,
                        'aktif' => true,
                    ]
                );

                // 4. Pastikan tidak ada penempatan magang aktif (karena status masih diajukan)
                DB::table('penempatan')->where('peserta_id', $user->id)->delete();

                // 5. Pendaftaran Status 'diajukan'
                $pendaftaran = Pendaftaran::where('user_id', $user->id)->first();
                if (! $pendaftaran) {
                    $pendaftaran = new Pendaftaran;
                    $pendaftaran->user_id = $user->id;
                }

                $pendaftaran->bulan_magang_id = $bulanAktif->id;
                $pendaftaran->kuota_id = $kuota->id;
                $pendaftaran->tipe = 'mandiri';
                $pendaftaran->durasi_bulan = 3;
                $pendaftaran->status = StatusPendaftaranEnum::Diajukan;
                $pendaftaran->verifikator_id = null;
                $pendaftaran->catatan = null;
                $pendaftaran->file_surat_diterima = null;
                $pendaftaran->surat_diterima_uploaded_at = null;
                $pendaftaran->diputuskan_at = null;
                $pendaftaran->diajukan_at = $acc['diajukan_at'];
                $pendaftaran->save();

                // 6. Dokumen Berkas Persyaratan Sesuai Jenjang
                $dokumenFolder = "documents/{$pendaftaran->id}";
                Storage::disk('private')->makeDirectory($dokumenFolder);

                $jenjangKey = $acc['jenjang'] instanceof JenjangEnum ? $acc['jenjang']->value : (string) $acc['jenjang'];
                $jenisDokumenList = JenisDokumen::where('jenjang', $jenjangKey)
                    ->where('aktif', true)
                    ->get();

                foreach ($jenisDokumenList as $jd) {
                    $docUuid = Str::uuid()->toString();
                    $docRelativePath = "{$dokumenFolder}/doc_{$docUuid}.pdf";

                    if ($hasSamplePdf) {
                        Storage::disk('private')->copy($samplePdfPath, $docRelativePath);
                        $fileSize = Storage::disk('private')->size($docRelativePath);
                        $fileHash = hash_file('sha256', Storage::disk('private')->path($docRelativePath));
                    } else {
                        $dummyContent = "%PDF-1.4\n%MagKom Sample Document for ".$jd->nama."\n%%EOF";
                        Storage::disk('private')->put($docRelativePath, $dummyContent);
                        $fileSize = strlen($dummyContent);
                        $fileHash = hash('sha256', $dummyContent);
                    }

                    Dokumen::updateOrCreate(
                        [
                            'pendaftaran_id' => $pendaftaran->id,
                            'jenis_dokumen_id' => $jd->id,
                        ],
                        [
                            'path' => $docRelativePath,
                            'nama_asli' => Str::slug($jd->nama, '_').'.pdf',
                            'mime' => 'application/pdf',
                            'ukuran' => $fileSize,
                            'hash' => $fileHash,
                        ]
                    );
                }
            }
        });
    }

    /**
     * Helper membuat berkas grafik avatar SVG beresolusi tinggi di storage public.
     */
    protected function createAvatarAsset(string $path, string $name): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        $colors = ['#1E40AF', '#6366F1'];
        $initials = collect(explode(' ', $name))
            ->map(fn ($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->join('');

        $svgContent = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320" width="320" height="320">
            <defs>
                <linearGradient id="grad_peserta_{$initials}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{$colors[0]}" />
                    <stop offset="100%" stop-color="{$colors[1]}" />
                </linearGradient>
            </defs>
            <circle cx="160" cy="160" r="160" fill="url(#grad_peserta_{$initials})" />
            
            <!-- Siluet Potret Modern -->
            <circle cx="160" cy="115" r="50" fill="#FFFFFF" opacity="0.95" />
            <path d="M65,280 C65,200 110,180 160,180 C210,180 255,200 255,280 Z" fill="#FFFFFF" opacity="0.95" />
            
            <!-- Badge Inisial Resmi -->
            <circle cx="250" cy="250" r="38" fill="{$colors[0]}" stroke="#FFFFFF" stroke-width="4" />
            <text x="250" y="258" font-family="'Plus Jakarta Sans', Arial, sans-serif" font-size="20" font-weight="800" fill="#FFFFFF" text-anchor="middle">{$initials}</text>
        </svg>
        SVG;

        Storage::disk('public')->put($path, trim($svgContent));
    }
}
