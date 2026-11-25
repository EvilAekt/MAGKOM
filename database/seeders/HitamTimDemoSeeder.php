<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\KelompokAnggota;
use App\Models\KelompokMagang;
use App\Models\Kuota;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class HitamTimDemoSeeder extends Seeder
{
    /**
     * Menempatkan akun "Hitam Cuih" ke dalam tim berisi 3 orang (sebagai Anggota, BUKAN Ketua)
     * tanpa menghapus Tugas Individu miliknya, serta membuat 1 Tugas Tim aktif.
     */
    public function run(): void
    {
        $hitam = User::where('nama', 'like', '%Hitam Cuih%')->first();
        if (! $hitam) {
            $this->command?->warn('User "Hitam Cuih" tidak ditemukan di database lokal.');

            return;
        }

        $penempatanHitam = Penempatan::where('peserta_id', $hitam->id)
            ->with(['pendaftaran', 'bidang', 'pembimbing'])
            ->latest('id')
            ->first();

        $bidangId = $penempatanHitam?->bidang_id
            ?? Bidang::first()?->id;
        $pembimbingId = $penempatanHitam?->pembimbing_id
            ?? User::where('role', RoleEnum::Pembimbing->value)->first()?->id;
        $tglMulai = $penempatanHitam?->tgl_mulai?->toDateString() ?? now()->subDays(14)->toDateString();
        $tglSelesai = $penempatanHitam?->tgl_selesai?->toDateString() ?? now()->addMonths(2)->toDateString();

        $jurusanId = $hitam->profil?->jurusan_id ?? Jurusan::first()?->id;
        $institusi = $hitam->profil?->institusi ?? 'Universitas Gadjah Mada';
        $bulanMagangId = $penempatanHitam?->pendaftaran?->bulan_magang_id ?? BulanMagang::first()?->id;
        $kuotaId = $penempatanHitam?->pendaftaran?->kuota_id ?? Kuota::first()?->id;

        // 1. Buat / ambil 2 rekan tim (Ketua Tim + Anggota 2) sehingga total 3 orang termasuk Hitam Cuih
        $ketua = User::firstOrCreate(
            ['email' => 'ketua.tim.hitam@magkom.test'],
            [
                'nama' => 'Arif Pratama',
                'password' => Hash::make('Password123!'),
                'role' => RoleEnum::Peserta->value,
                'email_verified_at' => now(),
            ]
        );

        PesertaProfil::firstOrCreate(
            ['user_id' => $ketua->id],
            [
                'jurusan_id' => $jurusanId,
                'institusi' => $institusi,
                'nim_nisn' => 'TIM-KETUA-01',
                'jenjang' => JenjangEnum::S1->value,
                'no_hp' => '081230000011',
            ]
        );

        $anggota2 = User::firstOrCreate(
            ['email' => 'anggota.tim.hitam@magkom.test'],
            [
                'nama' => 'Dina Maharani',
                'password' => Hash::make('Password123!'),
                'role' => RoleEnum::Peserta->value,
                'email_verified_at' => now(),
            ]
        );

        PesertaProfil::firstOrCreate(
            ['user_id' => $anggota2->id],
            [
                'jurusan_id' => $jurusanId,
                'institusi' => $institusi,
                'nim_nisn' => 'TIM-ANGGOTA-02',
                'jenjang' => JenjangEnum::S1->value,
                'no_hp' => '081230000022',
            ]
        );

        // 2. Buat / ambil KelompokMagang dengan ketua = Arif Pratama (BUKAN Hitam Cuih)
        $kelompok = KelompokMagang::firstOrCreate(
            ['nama_kelompok' => 'Tim Inovasi Layanan Sleman'],
            ['ketua_user_id' => $ketua->id]
        );

        if ((int) $kelompok->ketua_user_id !== (int) $ketua->id) {
            $kelompok->update(['ketua_user_id' => $ketua->id]);
        }

        // Pastikan Hitam Cuih dan Dina Maharani terdaftar sebagai anggota tim
        KelompokAnggota::firstOrCreate(
            ['kelompok_magang_id' => $kelompok->id, 'user_id' => $hitam->id],
            ['nama' => $hitam->nama, 'email' => $hitam->email]
        );

        KelompokAnggota::firstOrCreate(
            ['kelompok_magang_id' => $kelompok->id, 'user_id' => $anggota2->id],
            ['nama' => $anggota2->nama, 'email' => $anggota2->email]
        );

        // 3. Pendaftaran kelompok + Penempatan aktif untuk Ketua & Anggota 2
        $pendaftaranKelompok = Pendaftaran::firstOrCreate(
            ['kelompok_magang_id' => $kelompok->id],
            [
                'user_id' => $ketua->id,
                'bulan_magang_id' => $bulanMagangId,
                'kuota_id' => $kuotaId,
                'durasi_bulan' => 3,
                'tipe' => 'kelompok',
                'status' => 'diterima',
            ]
        );

        $penempatanKetua = Penempatan::firstOrCreate(
            ['peserta_id' => $ketua->id],
            [
                'pendaftaran_id' => $pendaftaranKelompok->id,
                'bidang_id' => $bidangId,
                'pembimbing_id' => $pembimbingId,
                'jalur' => JalurEnum::Umum->value,
                'sumber' => SumberPenempatanEnum::Online->value,
                'status' => StatusPenempatanEnum::Aktif->value,
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
            ]
        );

        $penempatanAnggota2 = Penempatan::firstOrCreate(
            ['peserta_id' => $anggota2->id],
            [
                'pendaftaran_id' => $pendaftaranKelompok->id,
                'bidang_id' => $bidangId,
                'pembimbing_id' => $pembimbingId,
                'jalur' => JalurEnum::Umum->value,
                'sumber' => SumberPenempatanEnum::Online->value,
                'status' => StatusPenempatanEnum::Aktif->value,
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
            ]
        );

        if (! $penempatanHitam) {
            $penempatanHitam = Penempatan::create([
                'pendaftaran_id' => $pendaftaranKelompok->id,
                'peserta_id' => $hitam->id,
                'bidang_id' => $bidangId,
                'pembimbing_id' => $pembimbingId,
                'jalur' => JalurEnum::Umum->value,
                'sumber' => SumberPenempatanEnum::Online->value,
                'status' => StatusPenempatanEnum::Aktif->value,
                'tgl_mulai' => $tglMulai,
                'tgl_selesai' => $tglSelesai,
            ]);
        } elseif ($penempatanHitam->status !== StatusPenempatanEnum::Aktif) {
            $penempatanHitam->update(['status' => StatusPenempatanEnum::Aktif->value]);
        }

        // 4. Buat 1 Tugas Tim untuk ketiga anggota tim (ketua + Hitam Cuih + Dina Maharani)
        $tugasTim = Tugas::firstOrCreate(
            [
                'tipe' => 'kelompok',
                'kelompok_magang_id' => $kelompok->id,
                'judul' => 'Pengembangan Portal Layanan Publik Terpadu (Tugas Tim)',
            ],
            [
                'penempatan_id' => null,
                'deskripsi' => 'Kerjakan secara kolaboratif bersama seluruh anggota tim. Siapa pun di dalam tim dapat mengunggah, membatalkan, atau merevisi progres sebelum ditinjau pembimbing.',
                'kategori' => 'Pengembangan Aplikasi',
                'kompleksitas' => 'tinggi',
                'deadline' => now()->addDays(14)->toDateString(),
                'status' => StatusTugasEnum::Belum->value,
                'status_publikasi' => 'dipublikasikan',
            ]
        );

        $members = [
            ['user_id' => $ketua->id, 'penempatan_id' => $penempatanKetua->id],
            ['user_id' => $hitam->id, 'penempatan_id' => $penempatanHitam->id],
            ['user_id' => $anggota2->id, 'penempatan_id' => $penempatanAnggota2->id],
        ];

        foreach ($members as $m) {
            $tugasTim->tugasPeserta()->firstOrCreate(
                ['user_id' => $m['user_id']],
                ['penempatan_id' => $m['penempatan_id']]
            );
        }

        $this->command?->info("Berhasil memasukkan {$hitam->nama} ke '{$kelompok->nama_kelompok}' (3 orang, Ketua: {$ketua->nama}) beserta 1 Tugas Tim.");
    }
}
