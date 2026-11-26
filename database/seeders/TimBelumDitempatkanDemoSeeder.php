<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
use App\Models\Jurusan;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\PesertaProfil;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TimBelumDitempatkanDemoSeeder extends Seeder
{
    /**
     * Mengisi data demo pendaftaran bertipe Tim (Kelompok) berstatus Diterima
     * yang belum memiliki Penempatan (masuk tab Tim -> Belum Ditempatkan).
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Cannot run TimBelumDitempatkanDemoSeeder in production environment.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $defaultPassword = Hash::make('Password123!');

        Storage::disk('private')->makeDirectory('dokumen');
        $samplePdfPath = 'dokumen/sample_berkas_tim_antrean_demo.pdf';
        $samplePdfContent = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f\n0000000009 00000 n\n0000000058 00000 n\n0000000115 00000 n\ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n206\n%%EOF";

        if (! Storage::disk('private')->exists($samplePdfPath)) {
            Storage::disk('private')->put($samplePdfPath, $samplePdfContent);
        }

        $pdfSize = (int) Storage::disk('private')->size($samplePdfPath);
        $pdfHash = hash('sha256', $samplePdfContent);

        $bulanAktifList = BulanMagang::where('aktif', true)->orderBy('id')->get();
        if ($bulanAktifList->isEmpty()) {
            $bulanFallback = BulanMagang::orderBy('id')->first();
            if (! $bulanFallback) {
                throw new RuntimeException('Tidak ada data bulan_magang di database.');
            }
            $bulanAktifList = collect([$bulanFallback]);
        }

        $bulan1 = $bulanAktifList->get(0) ?? $bulanAktifList->first();
        $bulan2 = $bulanAktifList->get(1) ?? $bulan1;
        $bulan3 = $bulanAktifList->get(2) ?? $bulan1;

        $jenisDokumenByJenjang = JenisDokumen::where('aktif', true)
            ->where('wajib', true)
            ->get()
            ->groupBy(fn (JenisDokumen $doc) => $doc->jenjang instanceof JenjangEnum ? $doc->jenjang->value : (string) $doc->jenjang);

        $timEntries = [
            [
                'email' => 'ketua.spbe.tim.demo@gmail.com',
                'nama' => 'Dimas Arya Pratama',
                'nim_nisn' => '22/509811/TK/55901',
                'institusi' => 'Universitas Gadjah Mada',
                'nama_pembimbing_sekolah' => 'Dr. Andi Pratama, M.Kom.',
                'jenjang' => JenjangEnum::S1,
                'jurusan_nama' => 'Informatika',
                'no_hp' => '081234990101',
                'alamat' => 'Jl. Kaliurang KM 6,5, Depok, Sleman, D.I. Yogyakarta',
                'nama_kelompok' => 'Tim Integrasi Data SPBE UGM',
                'bulan' => $bulan1,
                'durasi_bulan' => 3,
                'diajukan_at' => $now->copy()->subDays(7),
                'diputuskan_at' => $now->copy()->subDays(2),
                'anggota' => [
                    [
                        'nama' => 'Rani Kusumawardani',
                        'nim_nisn' => '22/509812/TK/55902',
                        'no_hp' => '081234990102',
                        'email' => 'rani.kusuma@mail.ugm.ac.id',
                    ],
                    [
                        'nama' => 'Fikri Maulana',
                        'nim_nisn' => '22/509813/TK/55903',
                        'no_hp' => '081234990103',
                        'email' => 'fikri.maulana@mail.ugm.ac.id',
                    ],
                ],
            ],
            [
                'email' => 'ketua.jaringan.tim.demo@gmail.com',
                'nama' => 'Bagus Saputra',
                'nim_nisn' => '0079981201',
                'institusi' => 'SMK Negeri 2 Depok Sleman',
                'nama_pembimbing_sekolah' => 'Heri Susanto, S.Pd., M.T.',
                'jenjang' => JenjangEnum::Smk,
                'jurusan_nama' => 'Teknik Komputer dan Jaringan (TKJ)',
                'no_hp' => '081234990201',
                'alamat' => 'Jl. STM Pembangunan, Mrican, Caturtunggal, Depok, Sleman',
                'nama_kelompok' => 'Tim Monitoring Infrastruktur Jaringan SMKN 2 Depok',
                'bulan' => $bulan2,
                'durasi_bulan' => 3,
                'diajukan_at' => $now->copy()->subDays(6),
                'diputuskan_at' => $now->copy()->subDays(1),
                'anggota' => [
                    [
                        'nama' => 'Ilham Ramadhan',
                        'nim_nisn' => '0079981202',
                        'no_hp' => '081234990202',
                        'email' => 'ilham.ramadhan@smkn2depok.sch.id',
                    ],
                    [
                        'nama' => 'Tania Wulandari',
                        'nim_nisn' => '0079981203',
                        'no_hp' => '081234990203',
                        'email' => 'tania.wulandari@smkn2depok.sch.id',
                    ],
                ],
            ],
            [
                'email' => 'ketua.publikasi.tim.demo@gmail.com',
                'nama' => 'Keisha Maharani',
                'nim_nisn' => '22419141021',
                'institusi' => 'Universitas Negeri Yogyakarta',
                'nama_pembimbing_sekolah' => 'Dra. Ratna Dewi, M.Si.',
                'jenjang' => JenjangEnum::S1,
                'jurusan_nama' => 'Ilmu Komunikasi',
                'no_hp' => '081234990301',
                'alamat' => 'Jl. Colombo No. 1, Karangmalang, Depok, Sleman',
                'nama_kelompok' => 'Tim Produksi Konten & Publikasi Digital UNY',
                'bulan' => $bulan3,
                'durasi_bulan' => 2,
                'diajukan_at' => $now->copy()->subDays(5),
                'diputuskan_at' => $now->copy()->subHours(10),
                'anggota' => [
                    [
                        'nama' => 'Kevin Adriansyah',
                        'nim_nisn' => '22419141022',
                        'no_hp' => '081234990302',
                        'email' => 'kevin.adriansyah@student.uny.ac.id',
                    ],
                    [
                        'nama' => 'Nayla Azzahra',
                        'nim_nisn' => '22419141023',
                        'no_hp' => '081234990303',
                        'email' => 'nayla.azzahra@student.uny.ac.id',
                    ],
                ],
            ],
        ];

        $seededCount = 0;

        DB::transaction(function () use (
            $timEntries,
            $defaultPassword,
            $now,
            $jenisDokumenByJenjang,
            $samplePdfPath,
            $pdfSize,
            $pdfHash,
            &$seededCount
        ) {
            foreach ($timEntries as $item) {
                $jurusan = Jurusan::where('nama_jurusan', $item['jurusan_nama'])->first()
                    ?? Jurusan::where('aktif', true)->first()
                    ?? Jurusan::first();

                if (! $jurusan) {
                    throw new RuntimeException('Data jurusan belum tersedia. Jalankan JurusanSeeder terlebih dahulu.');
                }

                $user = $this->upsertPeserta($item, $jurusan->id, $defaultPassword, $now);

                $kuota = Kuota::firstOrCreate(
                    [
                        'bulan_magang_id' => $item['bulan']->id,
                        'jurusan_id' => $jurusan->id,
                    ],
                    [
                        'jumlah' => 10,
                        'terisi' => 0,
                        'aktif' => true,
                    ]
                );

                $kelompok = KelompokMagang::updateOrCreate(
                    ['ketua_user_id' => $user->id],
                    ['nama_kelompok' => $item['nama_kelompok']]
                );

                $kelompok->anggota()->delete();
                foreach ($item['anggota'] as $idx => $anggotaData) {
                    $anggotaUser = User::firstOrNew(['email' => strtolower(trim((string) $anggotaData['email']))]);
                    $anggotaUser->nama = $anggotaData['nama'];
                    $anggotaUser->no_telp = $anggotaData['no_hp'];
                    $anggotaUser->password = $defaultPassword;
                    $anggotaUser->role = RoleEnum::Peserta;
                    $anggotaUser->status_aktif = true;
                    $anggotaUser->must_change_password = false;
                    $anggotaUser->email_verified_at = $now;
                    $anggotaUser->save();

                    $profilPayload = [
                        'institusi' => $item['institusi'],
                        'nama_pembimbing_sekolah' => $item['nama_pembimbing_sekolah'],
                        'nim_nisn' => $anggotaData['nim_nisn'],
                        'jenjang' => $item['jenjang'],
                        'jurusan_id' => $jurusan->id,
                        'no_hp' => $anggotaData['no_hp'],
                    ];

                    // Anggota pertama pada demo sudah melengkapi alamat & kontak darurat agar bervariasi dengan anggota kedua
                    if ($idx === 0) {
                        $profilPayload['alamat'] = $item['alamat'];
                        $profilPayload['nama_kontak_darurat'] = 'Wali '.$anggotaData['nama'];
                        $profilPayload['no_kontak_darurat'] = '081900002233';
                        $profilPayload['hubungan_kontak_darurat'] = 'Orang Tua / Wali';
                    }

                    PesertaProfil::updateOrCreate(
                        ['user_id' => $anggotaUser->id],
                        $profilPayload
                    );

                    $kelompok->anggota()->create([
                        'user_id' => $anggotaUser->id,
                        'nama' => $anggotaData['nama'],
                        'nim_nisn' => $anggotaData['nim_nisn'],
                        'no_hp' => $anggotaData['no_hp'],
                        'email' => $anggotaUser->email,
                    ]);
                }

                $existingPendaftaran = Pendaftaran::where('user_id', $user->id)
                    ->where('bulan_magang_id', $item['bulan']->id)
                    ->where('tipe', 'kelompok')
                    ->first();

                if ($existingPendaftaran && $existingPendaftaran->penempatan()->exists()) {
                    $this->command?->info("Lewati {$item['nama_kelompok']} karena sudah memiliki penempatan.");

                    continue;
                }

                $pendaftaran = Pendaftaran::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bulan_magang_id' => $item['bulan']->id,
                        'tipe' => 'kelompok',
                    ],
                    [
                        'kuota_id' => $kuota->id,
                        'kelompok_magang_id' => $kelompok->id,
                        'durasi_bulan' => $item['durasi_bulan'],
                        'status' => StatusPendaftaranEnum::Diterima,
                        'catatan' => 'Berkas tim lengkap dan dinyatakan lolos seleksi administrasi.',
                        'diajukan_at' => $item['diajukan_at'],
                        'diputuskan_at' => $item['diputuskan_at'],
                    ]
                );

                $this->syncDokumen($pendaftaran, $item['jenjang'], $jenisDokumenByJenjang, $samplePdfPath, $pdfSize, $pdfHash);
                $this->ensureBidangHasPembimbing($jurusan, $defaultPassword, $now);

                $seededCount++;
            }
        });

        $this->command?->info("TimBelumDitempatkanDemoSeeder selesai: {$seededCount} tim siap di antrean Belum Ditempatkan.");
    }

    /**
     * Membuat atau memperbarui akun ketua tim beserta profil akademiknya.
     *
     * @param  array<string, mixed>  $item
     */
    private function upsertPeserta(array $item, int $jurusanId, string $defaultPassword, Carbon $now): User
    {
        $user = User::firstOrNew(['email' => $item['email']]);
        $user->nama = $item['nama'];
        $user->password = $defaultPassword;
        $user->role = RoleEnum::Peserta;
        $user->status_aktif = true;
        $user->must_change_password = false;
        $user->email_verified_at = $now;
        $user->save();

        PesertaProfil::updateOrCreate(
            ['user_id' => $user->id],
            [
                'institusi' => $item['institusi'],
                'nama_pembimbing_sekolah' => $item['nama_pembimbing_sekolah'],
                'nim_nisn' => $item['nim_nisn'],
                'jenjang' => $item['jenjang'],
                'jurusan_id' => $jurusanId,
                'no_hp' => $item['no_hp'],
                'alamat' => $item['alamat'],
                'nama_kontak_darurat' => 'Wali '.$item['nama'],
                'no_kontak_darurat' => '081900001122',
                'hubungan_kontak_darurat' => 'Orang Tua / Wali',
            ]
        );

        return $user;
    }

    /**
     * Memastikan bidang terkait jurusan memiliki minimal 1 pembimbing aktif agar dropdown penempatan tidak kosong.
     */
    private function ensureBidangHasPembimbing(Jurusan $jurusan, string $defaultPassword, Carbon $now): void
    {
        $bidang = $jurusan->bidang()->first();
        if (! $bidang) {
            return;
        }

        $hasActiveMentor = User::where('role', RoleEnum::Pembimbing)
            ->where('bidang_id', $bidang->id)
            ->where('status_aktif', true)
            ->exists();

        if ($hasActiveMentor) {
            return;
        }

        $kodeSlug = strtolower((string) preg_replace('/[^a-zA-Z0-9]+/', '', (string) ($bidang->kode_bidang ?: 'bidang'.$bidang->id)));
        $email = "pembimbing.{$kodeSlug}.demo@gmail.com";

        $mentor = User::firstOrNew(['email' => $email]);
        $mentor->nama = 'Pembimbing '.$bidang->nama_bidang;
        $mentor->password = $defaultPassword;
        $mentor->role = RoleEnum::Pembimbing;
        $mentor->bidang_id = $bidang->id;
        $mentor->status_aktif = true;
        $mentor->must_change_password = false;
        $mentor->email_verified_at = $now;
        $mentor->save();
    }

    /**
     * Menyinkronkan dokumen persyaratan wajib berdasarkan jenjang pendaftar.
     *
     * @param  Collection<string, Collection<int, JenisDokumen>>  $jenisDokumenByJenjang
     */
    private function syncDokumen(
        Pendaftaran $pendaftaran,
        JenjangEnum $jenjang,
        Collection $jenisDokumenByJenjang,
        string $samplePdfPath,
        int $pdfSize,
        string $pdfHash
    ): void {
        $jenisList = $jenisDokumenByJenjang->get($jenjang->value, collect());

        foreach ($jenisList as $jenis) {
            Dokumen::updateOrCreate(
                [
                    'pendaftaran_id' => $pendaftaran->id,
                    'jenis_dokumen_id' => $jenis->id,
                    'kelompok_anggota_id' => null,
                ],
                [
                    'path' => $samplePdfPath,
                    'nama_asli' => $jenis->kode.'_berkas_tim_demo.pdf',
                    'mime' => 'application/pdf',
                    'ukuran' => $pdfSize,
                    'hash' => $pdfHash,
                ]
            );
        }
    }
}
