<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Models\BulanMagang;
use App\Models\Dokumen;
use App\Models\JenisDokumen;
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

class AdminQueueDemoSeeder extends Seeder
{
    /**
     * Mengisi antrean verifikasi pendaftaran Admin (Kelompok & Individu)
     * agar seluruh tab status dan kategori pada halaman Admin memiliki data realistis.
     */
    public function run(): void
    {
        if (app()->isProduction()) {
            throw new RuntimeException('Cannot run AdminQueueDemoSeeder in production environment.');
        }

        $now = Carbon::now('Asia/Jakarta');
        $defaultPassword = Hash::make('Password123!');

        Storage::disk('private')->makeDirectory('dokumen');
        $samplePdfPath = 'dokumen/sample_berkas_admin_demo.pdf';
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

        $kelompokEntries = [
            [
                'email' => 'ketua.ugm.demo@gmail.com',
                'nama' => 'Aditya Mahendra',
                'nim_nisn' => '22/501234/TK/54321',
                'institusi' => 'Universitas Gadjah Mada',
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => 18,
                'no_hp' => '081234560101',
                'alamat' => 'Jl. Teknika Utara No. 12, Depok, Sleman, D.I. Yogyakarta',
                'nama_kelompok' => 'Tim Pengembangan Portal Layanan Publik UGM',
                'bulan' => $bulan1,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => $now->copy()->subDays(1),
                'diputuskan_at' => null,
                'catatan' => null,
                'anggota' => [
                    [
                        'nama' => 'Naufal Hakim',
                        'nim_nisn' => '22/501235/TK/54322',
                        'no_hp' => '081234560102',
                        'email' => 'naufal.hakim@mail.ugm.ac.id',
                    ],
                    [
                        'nama' => 'Salsaabila Putri',
                        'nim_nisn' => '22/501236/TK/54323',
                        'no_hp' => '081234560103',
                        'email' => 'salsaabila.putri@mail.ugm.ac.id',
                    ],
                ],
            ],
            [
                'email' => 'ketua.amikom.demo@gmail.com',
                'nama' => 'Bayu Setiawan',
                'nim_nisn' => '22.11.4810',
                'institusi' => 'Universitas AMIKOM Yogyakarta',
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => 7,
                'no_hp' => '081234560201',
                'alamat' => 'Jl. Ring Road Utara, Condongcatur, Depok, Sleman',
                'nama_kelompok' => 'Tim Audit Keamanan Jaringan AMIKOM',
                'bulan' => $bulan2,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => $now->copy()->subDays(4),
                'diputuskan_at' => null,
                'catatan' => null,
                'anggota' => [
                    [
                        'nama' => 'Dinda Permata',
                        'nim_nisn' => '22.11.4811',
                        'no_hp' => '081234560202',
                        'email' => 'dinda.permata@students.amikom.ac.id',
                    ],
                    [
                        'nama' => 'Reza Kurniawan',
                        'nim_nisn' => '22.11.4812',
                        'no_hp' => '081234560203',
                        'email' => 'reza.kurniawan@students.amikom.ac.id',
                    ],
                ],
            ],
            [
                'email' => 'ketua.smkn1.demo@gmail.com',
                'nama' => 'Rizky Ramadhan',
                'nim_nisn' => '0078912341',
                'institusi' => 'SMK Negeri 1 Depok Sleman',
                'jenjang' => JenjangEnum::Smk,
                'jurusan_id' => 2,
                'no_hp' => '081234560301',
                'alamat' => 'Jl. Ring Road Utara Maguwoharjo, Depok, Sleman',
                'nama_kelompok' => 'Tim Konten Multimedia SMKN 1 Depok',
                'bulan' => $bulan3,
                'durasi_bulan' => 2,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => $now->copy()->subDays(6),
                'diputuskan_at' => null,
                'catatan' => null,
                'anggota' => [
                    [
                        'nama' => 'Aulia Zahra',
                        'nim_nisn' => '0078912342',
                        'no_hp' => '081234560302',
                        'email' => 'aulia.zahra@smkn1depok.sch.id',
                    ],
                ],
            ],
            [
                'email' => 'ketua.uny.demo@gmail.com',
                'nama' => 'Galih Prakoso',
                'nim_nisn' => '22520241015',
                'institusi' => 'Universitas Negeri Yogyakarta',
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => 5,
                'no_hp' => '081234560401',
                'alamat' => 'Jl. Colombo No. 1, Karangmalang, Depok, Sleman',
                'nama_kelompok' => 'Tim Otomasi Infrastruktur UNY',
                'bulan' => $bulan1,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Ditolak,
                'diajukan_at' => $now->copy()->subDays(10),
                'diputuskan_at' => $now->copy()->subDays(8),
                'catatan' => 'Kuota penempatan bidang infrastruktur untuk periode ini sudah penuh.',
                'anggota' => [
                    [
                        'nama' => 'Hendra Wijaya',
                        'nim_nisn' => '22520241016',
                        'no_hp' => '081234560402',
                        'email' => 'hendra.wijaya@student.uny.ac.id',
                    ],
                ],
            ],
            [
                'email' => 'ketua.upn.demo@gmail.com',
                'nama' => 'Tariq Alamsyah',
                'nim_nisn' => '123210112',
                'institusi' => 'UPN "Veteran" Yogyakarta',
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => 7,
                'no_hp' => '081234560501',
                'alamat' => 'Jl. Padjajaran, Condongcatur, Depok, Sleman',
                'nama_kelompok' => 'Tim Analitik Data SPBE UPN',
                'bulan' => $bulan2,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::PerluPerbaikan,
                'diajukan_at' => $now->copy()->subDays(5),
                'diputuskan_at' => $now->copy()->subDays(2),
                'catatan' => 'Mohon unggah ulang surat pengantar fakultas yang sudah ditandatangani dan distempel basah.',
                'anggota' => [
                    [
                        'nama' => 'Mega Ayuningtyas',
                        'nim_nisn' => '123210113',
                        'no_hp' => '081234560502',
                        'email' => 'mega.ayuningtyas@upnyk.ac.id',
                    ],
                ],
            ],
        ];

        $individuEntries = [
            [
                'email' => 'raka.pratama.demo@gmail.com',
                'nama' => 'Raka Pratama',
                'nim_nisn' => '21523104',
                'institusi' => 'Universitas Islam Indonesia',
                'jenjang' => JenjangEnum::S1,
                'jurusan_id' => 18,
                'no_hp' => '081234560601',
                'alamat' => 'Jl. Kaliurang KM 14,5, Ngaglik, Sleman',
                'bulan' => $bulan1,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => $now->copy()->subHours(12),
            ],
            [
                'email' => 'nadia.kusuma.demo@gmail.com',
                'nama' => 'Nadia Kusuma',
                'nim_nisn' => '230102045',
                'institusi' => 'Universitas Teknologi Yogyakarta',
                'jenjang' => JenjangEnum::D3,
                'jurusan_id' => 6,
                'no_hp' => '081234560701',
                'alamat' => 'Jl. Siliwangi, Jombor, Mlati, Sleman',
                'bulan' => $bulan2,
                'durasi_bulan' => 2,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => $now->copy()->subDays(3),
            ],
            [
                'email' => 'fajar.nugroho.demo@gmail.com',
                'nama' => 'Fajar Nugroho',
                'nim_nisn' => '0079123490',
                'institusi' => 'SMK Negeri 2 Depok Sleman',
                'jenjang' => JenjangEnum::Smk,
                'jurusan_id' => 1,
                'no_hp' => '081234560801',
                'alamat' => 'Mrican, Caturtunggal, Depok, Sleman',
                'bulan' => $bulan3,
                'durasi_bulan' => 3,
                'status' => StatusPendaftaranEnum::Diajukan,
                'diajukan_at' => $now->copy()->subDays(6),
            ],
        ];

        DB::transaction(function () use (
            $kelompokEntries,
            $individuEntries,
            $defaultPassword,
            $now,
            $jenisDokumenByJenjang,
            $samplePdfPath,
            $pdfSize,
            $pdfHash
        ) {
            foreach ($kelompokEntries as $item) {
                $user = $this->upsertPeserta($item, $defaultPassword, $now);
                $kuota = Kuota::firstOrCreate(
                    [
                        'bulan_magang_id' => $item['bulan']->id,
                        'jurusan_id' => $item['jurusan_id'],
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
                $kelompok->anggota()->createMany($item['anggota']);

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
                        'status' => $item['status'],
                        'catatan' => $item['catatan'],
                        'diajukan_at' => $item['diajukan_at'],
                        'diputuskan_at' => $item['diputuskan_at'],
                    ]
                );

                $this->syncDokumen($pendaftaran, $item['jenjang'], $jenisDokumenByJenjang, $samplePdfPath, $pdfSize, $pdfHash);
            }

            foreach ($individuEntries as $item) {
                $user = $this->upsertPeserta($item, $defaultPassword, $now);
                $kuota = Kuota::firstOrCreate(
                    [
                        'bulan_magang_id' => $item['bulan']->id,
                        'jurusan_id' => $item['jurusan_id'],
                    ],
                    [
                        'jumlah' => 10,
                        'terisi' => 0,
                        'aktif' => true,
                    ]
                );

                $pendaftaran = Pendaftaran::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'bulan_magang_id' => $item['bulan']->id,
                        'tipe' => 'mandiri',
                    ],
                    [
                        'kuota_id' => $kuota->id,
                        'kelompok_magang_id' => null,
                        'durasi_bulan' => $item['durasi_bulan'],
                        'status' => $item['status'],
                        'catatan' => null,
                        'diajukan_at' => $item['diajukan_at'],
                        'diputuskan_at' => null,
                    ]
                );

                $this->syncDokumen($pendaftaran, $item['jenjang'], $jenisDokumenByJenjang, $samplePdfPath, $pdfSize, $pdfHash);
            }
        });
    }

    /**
     * Membuat atau memperbarui akun peserta beserta profil akademiknya.
     *
     * @param  array<string, mixed>  $item
     */
    private function upsertPeserta(array $item, string $defaultPassword, Carbon $now): User
    {
        $user = User::updateOrCreate(
            ['email' => $item['email']],
            [
                'nama' => $item['nama'],
                'password' => $defaultPassword,
                'role' => RoleEnum::Peserta,
                'status_aktif' => true,
                'must_change_password' => false,
                'email_verified_at' => $now,
            ]
        );

        PesertaProfil::updateOrCreate(
            ['user_id' => $user->id],
            [
                'institusi' => $item['institusi'],
                'nim_nisn' => $item['nim_nisn'],
                'jenjang' => $item['jenjang'],
                'jurusan_id' => $item['jurusan_id'],
                'no_hp' => $item['no_hp'],
                'alamat' => $item['alamat'],
                'nama_kontak_darurat' => 'Keluarga '.$item['nama'],
                'no_kontak_darurat' => '081900001122',
                'hubungan_kontak_darurat' => 'Orang Tua / Wali',
            ]
        );

        return $user;
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
                    'nama_asli' => $jenis->kode.'_berkas_demo.pdf',
                    'mime' => 'application/pdf',
                    'ukuran' => $pdfSize,
                    'hash' => $pdfHash,
                ]
            );
        }
    }
}
