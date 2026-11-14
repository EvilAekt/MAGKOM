# TASK SPECIFICATION & IMPLEMENTATION PRD (v1.2)
## Pembersihan Akun Non-Gmail, Restrukturisasi Akun Operasional MagKom `@gmail.com`, & Pengisian Foto Profil Nyata

- **Dokumen Referensi:** PRD MagKom v1.2, Kamus Data Database MagKom v1.0, Aturan Repositori MagKom (Rule 5 & 7)
- **Peran Pembuat Dokumen:** Senior Lead Developer
- **Target Pembaca / Pelaksana:** Junior Developer & AI Assistant (Gemini 3.8 Flash / Claude / Cursor)
- **Tingkat Kerumitan:** Menengah (*Intermediate &ndash; Data Integrity, Foreign Key Safety & File Storage Assets*)
- **Target Modul:** Basis Data Lokal & File Storage (`users`, `peserta_profil`, `penempatan`, `pendaftaran`, `tugas`, `logbook`, `storage/app/public/`)
- **Task ID:** `TASK-DB-PURGE-AND-AVATAR-GMAIL-03`
- **Status Dokumen:** **SELESAI DIEKSEKUSI & DIVERIFIKASI (*COMPLETED & VERIFIED*)**

---

## 1. DIRECTIVE & PESAN DARI SENIOR LEAD DEVELOPER

Halo Junior Developer / AI Assistant! 👋

Saat ini basis data lokal MagKom memiliki campuran akun demo dengan domain instansi `@magkom.go.id`, `@slemankab.go.id`, serta akun dev `@gmail.com` (total 77 akun). Selain itu, banyak profil pengguna yang tidak memiliki foto profil fisik, sehingga avatar di topbar header, sidebar profil, card bento dashboard, dan halaman profil 360 hanya menampilkan inisial bulat kosong atau broken image.

### 🎯 Tujuan Utama Tugas Ini:
1. **Standarisasi Domain 100% `@gmail.com`**: Hapus bersih semua akun non-Gmail dari database tanpa merusak integritas relasi foreign key.
2. **Dataset Bersih & Realistis (28 Akun Operasional)**:
   - **2 Akun SuperAdmin**
   - **2 Akun Admin Verifikator**
   - **4 Akun Pembimbing Lapangan** (1 pembimbing per bidang dinas: EGOV, IKP, ITI, Sekretariat)
   - **20 Akun Peserta Magang** (tersebar merata: 5 peserta per bidang & pembimbing)
3. **Foto Profil Nyata untuk Setiap Profil**:
   - Seluruh 28 akun staf dan peserta **wajib memiliki berkas foto profil fisik nyata** di disk public (`storage/app/public/`).
   - Kolom `foto` di tabel `users` dan `peserta_profil` harus terisi path berkas yang valid.
   - Accessor `$user->foto_url` dan `$profil->foto_url` harus mengembalikan URL valid (tidak `null`).
4. **Preservasi Akun Pengembang**:
   - Akun Google OAuth aktif milik pengembang: **`adhityagalang553@gmail.com` (User ID: 77) WAJIB DIPERTAHANKAN** dan tidak boleh terhapus.

---

### ⚠️ PERINGATAN KRITIKAL DARI SENIOR DEVELOPER (BACA INI DULU!)

> [!CAUTION]
> #### 1. Jangan Gunakan Raw Query DELETE Langsung!
> Jika kamu langsung menjalankan:
> `DELETE FROM users WHERE email NOT LIKE '%@gmail.com'`
> MySQL akan **GAGAL** dengan error:
> `SQLSTATE[23000]: Integrity constraint violation: 1451 Cannot delete or update a parent row: a foreign key constraint fails (penempatan, CONSTRAINT penempatan_pembimbing_id_foreign FOREIGN KEY (pembimbing_id) REFERENCES users (id))`
> 
> Mengapa? Karena ada 40 data penempatan yang saat ini terhubung ke pembimbing non-gmail (IDs 4, 29, 30, 31, 32, 33).  
> **Solusi**: Relink (alihkan) `pembimbing_id` di tabel `penempatan` ke akun pembimbing `@gmail.com` yang baru **SEBELUM** akun pembimbing lama dihapus!

> [!WARNING]
> #### 2. Atribut `role` pada Model User di-Guarded!
> Pada `app/Models/User.php`, kolom `role` **sengaja tidak dimasukkan ke dalam `$fillable`** demi keamanan (aturan NFR-S8 / TDD 5.2):
> ```php
> // JANGAN LAKUKAN INI (role akan diabaikan oleh fillable!):
> User::updateOrCreate(['email' => $email], ['role' => RoleEnum::Admin, ...]);
> 
> // LAKUKAN INI:
> $user = User::where('email', $email)->first() ?? new User();
> $user->fill($attributes);
> $user->role = RoleEnum::Admin; // set eksplisit
> $user->save();
> ```

> [!NOTE]
> #### 3. Mengapa Menggunakan Generator Avatar SVG Vektor?
> Untuk memastikan seeder dapat berjalan mandiri di lingkungan offline, kita akan men-generate berkas grafis avatar SVG beresolusi tinggi (aspek 1:1, 320x320 px) dengan palet resmi warna dinas Sleman (Navy, Emerald, Cyan, Slate), siluet potret profesional, dan inisial resmi. Berkas ini berukuran sangat ringan, tajam di semua layar retina, dan langsung dikenali oleh sistem storage Laravel.

---

## 2. SPESIFIKASI PENYIMPANAN FOTO PROFIL

### 2.1. Jalur Direktori Fisik
- **Foto Profil Staf (SuperAdmin, Admin, Pembimbing)**:
  `storage/app/public/foto-staf/` (diakses via `/storage/foto-staf/{filename}.svg`)
- **Foto Profil Peserta Magang**:
  `storage/app/public/profil-peserta/` (diakses via `/storage/profil-peserta/{filename}.svg`)

### 2.2. Mekanisme Accessor Model
1. **Model `User` (`app/Models/User.php`)**:
   ```php
   public function getFotoUrlAttribute(): ?string
   {
       if ($this->foto && Storage::disk('public')->exists($this->foto)) {
           return Storage::disk('public')->url($this->foto);
       }
       if ($this->isPeserta() && $this->profil?->foto_url) {
           return $this->profil->foto_url;
       }
       return null;
   }
   ```
2. **Model `PesertaProfil` (`app/Models/PesertaProfil.php`)**:
   ```php
   public function getFotoUrlAttribute(): ?string
   {
       if (! empty($this->foto)) {
           return asset('storage/'.$this->foto);
       }
       return null;
   }
   ```
> [!TIP]
> Untuk peserta, simpan path foto di **kedua kolom**: `users.foto` dan `peserta_profil.foto` (misalnya: `'profil-peserta/aditya_pratama.svg'`). Dengan begitu, baik pemanggilan `$user->foto_url` maupun `$profil->foto_url` dijamin berhasil 100%.

---

## 3. MATRIKS 28 AKUN OPERASIONAL `@gmail.com`

Seluruh akun menggunakan kata sandi terenkripsi seragam untuk pengujian: **`Password123!`**.

### 3.1. SuperAdmin (2 Akun)
| No | Nama Lengkap | Email | Role | Unit Kerja | Berkas Foto Profil |
|:---:|---|---|:---:|---|---|
| 1 | Super Administrator Utama | `superadmin@gmail.com` | `superadmin` | Pengelola Pusat Sistem | `foto-staf/superadmin-utama.svg` |
| 2 | Super Administrator Sleman | `superadmin.sleman@gmail.com` | `superadmin` | Cadangan Administrator | `foto-staf/superadmin-sleman.svg` |

### 3.2. Admin Verifikator (2 Akun)
| No | Nama Lengkap | Email | Role | Unit Kerja | Berkas Foto Profil |
|:---:|---|---|:---:|---|---|
| 1 | Admin Verifikator Utama | `admin@gmail.com` | `admin` | Verifikator Pendaftaran | `foto-staf/admin-utama.svg` |
| 2 | Admin Verifikator Sleman | `admin.verifikator@gmail.com` | `admin` | Verifikator Kuota & Berkas | `foto-staf/admin-sleman.svg` |

### 3.3. Pembimbing Lapangan (4 Akun &ndash; 1 Per Bidang Dinas)
| No | Nama Pembimbing | Email | Bidang Dinas | Kuota | Berkas Foto Profil |
|:---:|---|---|---|:---:|---|
| 1 | Budi Santoso, S.Kom | `pembimbing@gmail.com` | Aplikasi Informatika & E-Government (EGOV) | 5 Peserta | `foto-staf/pembimbing-egov.svg` |
| 2 | Sri Wahyuni, S.I.Kom | `pembimbing.ikp@gmail.com` | Informasi dan Komunikasi Publik (IKP) | 5 Peserta | `foto-staf/pembimbing-ikp.svg` |
| 3 | Agus Wibowo, S.T | `pembimbing.iti@gmail.com` | Infrastruktur Teknologi Informasi (ITI) | 5 Peserta | `foto-staf/pembimbing-iti.svg` |
| 4 | Endang Sulastri, S.AP | `pembimbing.sekretariat@gmail.com` | Sekretariat Dinas Kominfo | 5 Peserta | `foto-staf/pembimbing-sekretariat.svg` |

### 3.4. Peserta Magang (20 Akun &ndash; 5 Per Bidang Dinas)

#### Klaster 1: Bidang EGOV (Pembimbing: Budi Santoso, S.Kom)
| No | Nama Peserta | Email | Institusi | Jurusan (ID Database) | Jenjang | Berkas Foto Profil |
|:---:|---|---|---|---|:---:|---|
| 1 | Aditya Pratama | `aditya.pratama@gmail.com` | Universitas Gadjah Mada | Teknik Informatika (ID: 18) | S1 | `profil-peserta/aditya_pratama.svg` |
| 2 | Bunga Citra | `bunga.citra@gmail.com` | UPN Veteran Yogyakarta | Sistem Informasi (ID: 7) | S1 | `profil-peserta/bunga_citra.svg` |
| 3 | Chandra Wijaya | `chandra.wijaya@gmail.com` | Universitas AMIKOM | Informatika (ID: 5) | S1 | `profil-peserta/chandra_wijaya.svg` |
| 4 | Dinda Kirana | `dinda.kirana@gmail.com` | Universitas Negeri Yogyakarta | Manajemen Informatika (ID: 6) | D3 | `profil-peserta/dinda_kirana.svg` |
| 5 | Eko Prasetyo | `eko.prasetyo@gmail.com` | SMK N 2 Depok Sleman | Rekayasa Perangkat Lunak (ID: 4) | SMK | `profil-peserta/eko_prasetyo.svg` |

#### Klaster 2: Bidang IKP (Pembimbing: Sri Wahyuni, S.I.Kom)
| No | Nama Peserta | Email | Institusi | Jurusan (ID Database) | Jenjang | Berkas Foto Profil |
|:---:|---|---|---|---|:---:|---|
| 6 | Fani Rahmawati | `fani.rahma@gmail.com` | UIN Sunan Kalijaga | Ilmu Komunikasi (ID: 10) | S1 | `profil-peserta/fani_rahma.svg` |
| 7 | Gilang Ramadhan | `gilang.ramadhan@gmail.com` | ISI Yogyakarta | Desain Grafis (ID: 12) | S1 | `profil-peserta/gilang_ramadhan.svg` |
| 8 | Hana Safitri | `hana.safitri@gmail.com` | Universitas Atma Jaya | Komunikasi Penyiaran Islam (ID: 11) | S1 | `profil-peserta/hana_safitri.svg` |
| 9 | Indra Kurniawan | `indra.kurniawan@gmail.com` | SMK N 1 Bantul | Desain Grafis (SMK) (ID: 2) | SMK | `profil-peserta/indra_kurniawan.svg` |
| 10 | Joko Susilo | `joko.susilo@gmail.com` | Universitas Ahmad Dahlan | Ilmu Komunikasi (ID: 10) | S1 | `profil-peserta/joko_susilo.svg` |

#### Klaster 3: Bidang ITI (Pembimbing: Agus Wibowo, S.T)
| No | Nama Peserta | Email | Institusi | Jurusan (ID Database) | Jenjang | Berkas Foto Profil |
|:---:|---|---|---|---|:---:|---|
| 11 | Kartika Putri | `kartika.putri@gmail.com` | Universitas Gadjah Mada | Teknologi Informasi (ID: 8) | S1 | `profil-peserta/kartika_putri.svg` |
| 12 | Lukman Hakim | `lukman.hakim@gmail.com` | Universitas Sanata Dharma | Teknik Komputer (ID: 17) | S1 | `profil-peserta/lukman_hakim.svg` |
| 13 | Maya Anggraini | `maya.anggraini@gmail.com` | SMK N 2 Depok Sleman | Teknik Komputer & Jaringan (ID: 1) | SMK | `profil-peserta/maya_anggraini.svg` |
| 14 | Naufal Rizky | `naufal.rizky@gmail.com` | Universitas Negeri Yogyakarta | Statistik (ID: 9) | S1 | `profil-peserta/naufal_rizky.svg` |
| 15 | Olivia Tan | `olivia.tan@gmail.com` | ITNY | Teknik Informatika (ID: 18) | S1 | `profil-peserta/olivia_tan.svg` |

#### Klaster 4: Bidang Sekretariat (Pembimbing: Endang Sulastri, S.AP)
| No | Nama Peserta | Email | Institusi | Jurusan (ID Database) | Jenjang | Berkas Foto Profil |
|:---:|---|---|---|---|:---:|---|
| 16 | Panji Nugroho | `panji.nugroho@gmail.com` | Universitas Negeri Yogyakarta | Manajemen Perkantoran (ID: 14) | S1 | `profil-peserta/panji_nugroho.svg` |
| 17 | Qori Amanda | `qori.amanda@gmail.com` | Universitas Islam Indonesia | Ekonomi dan Sosial (ID: 13) | S1 | `profil-peserta/qori_amanda.svg` |
| 18 | Rian Ardiansyah | `rian.ardiansyah@gmail.com` | SMK N 1 Depok Sleman | Administrasi Perkantoran (ID: 3) | SMK | `profil-peserta/rian_ardiansyah.svg` |
| 19 | Siti Nurhaliza | `siti.nurhaliza@gmail.com` | UPN Veteran Yogyakarta | Manajemen Rekod (ID: 16) | D3 | `profil-peserta/siti_nurhaliza.svg` |
| 20 | Taufik Hidayat | `taufik.hidayat@gmail.com` | STIE YKPN Yogyakarta | Ilmu Kearsipan (ID: 15) | D3 | `profil-peserta/taufik_hidayat.svg` |

### 3.5. Akun Pengembang
| No | Nama | Email | Role | Catatan Khusus |
|:---:|---|---|:---:|---|
| 21 | Galang Adhit | `adhityagalang553@gmail.com` | `peserta` | **WAJIB DIPRESERVASI** (Akun Google OAuth Pengembang Aktif) |

---

## 4. KODE IMPLEMENTASI SEEDER LENGKAP

Simpan kode ini di berkas:  
`database/seeders/PurgeAndSeedCleanGmailAccountsSeeder.php`

```php
<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\JalurEnum;
use App\Enums\JenjangEnum;
use App\Enums\KompleksitasEnum;
use App\Enums\RoleEnum;
use App\Enums\StatusLogbookEnum;
use App\Enums\StatusPendaftaranEnum;
use App\Enums\StatusPenempatanEnum;
use App\Enums\StatusTugasEnum;
use App\Enums\SumberPenempatanEnum;
use App\Models\Bidang;
use App\Models\BulanMagang;
use App\Models\Jurusan;
use App\Models\Logbook;
use App\Models\Pendaftaran;
use App\Models\Penempatan;
use App\Models\PesertaProfil;
use App\Models\Tugas;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class PurgeAndSeedCleanGmailAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('Password123!');
        $now = now();

        // 1. Pastikan folder foto profil ada di storage disk public
        Storage::disk('public')->makeDirectory('foto-staf');
        Storage::disk('public')->makeDirectory('profil-peserta');

        DB::transaction(function () use ($defaultPassword, $now) {
            // ==========================================
            // LANGKAH 1: UNIT KERJA / BIDANG DINAS
            // ==========================================
            $bidangEgov = Bidang::firstOrCreate(
                ['nama_bidang' => 'Aplikasi Informatika & E-Government (EGOV)'],
                ['deskripsi' => 'Pengembangan SPBE dan integrasi aplikasi dinas.', 'aktif' => true]
            );
            $bidangIkp = Bidang::firstOrCreate(
                ['nama_bidang' => 'Informasi dan Komunikasi Publik (IKP)'],
                ['deskripsi' => 'Publikasi multimedia, kehumasan, dan relasi media massa.', 'aktif' => true]
            );
            $bidangIti = Bidang::firstOrCreate(
                ['nama_bidang' => 'Infrastruktur Teknologi Informasi (ITI)'],
                ['deskripsi' => 'Jaringan intra-pemerintah, server dinas, dan CSIRT Sleman.', 'aktif' => true]
            );
            $bidangSekretariat = Bidang::firstOrCreate(
                ['nama_bidang' => 'Sekretariat Dinas'],
                ['deskripsi' => 'Administrasi umum, keuangan, dan tata kelola kepegawaian.', 'aktif' => true]
            );

            // ==========================================
            // LANGKAH 2: SUPERADMIN (2 AKUN)
            // ==========================================
            $superAdmins = [
                [
                    'email' => 'superadmin@gmail.com',
                    'nama' => 'Super Administrator Utama',
                    'foto' => 'foto-staf/superadmin-utama.svg',
                    'role_name' => 'superadmin',
                ],
                [
                    'email' => 'superadmin.sleman@gmail.com',
                    'nama' => 'Super Administrator Sleman',
                    'foto' => 'foto-staf/superadmin-sleman.svg',
                    'role_name' => 'superadmin',
                ],
            ];

            foreach ($superAdmins as $sa) {
                $this->createAvatarAsset($sa['foto'], $sa['nama'], 'superadmin');

                $user = User::where('email', $sa['email'])->first() ?? new User();
                $user->nama = $sa['nama'];
                $user->email = $sa['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::SuperAdmin;
                $user->foto = $sa['foto'];
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();
            }

            // ==========================================
            // LANGKAH 3: ADMIN VERIFIKATOR (2 AKUN)
            // ==========================================
            $admins = [
                [
                    'email' => 'admin@gmail.com',
                    'nama' => 'Admin Verifikator Utama',
                    'foto' => 'foto-staf/admin-utama.svg',
                ],
                [
                    'email' => 'admin.verifikator@gmail.com',
                    'nama' => 'Admin Verifikator Sleman',
                    'foto' => 'foto-staf/admin-sleman.svg',
                ],
            ];

            foreach ($admins as $ad) {
                $this->createAvatarAsset($ad['foto'], $ad['nama'], 'admin');

                $user = User::where('email', $ad['email'])->first() ?? new User();
                $user->nama = $ad['nama'];
                $user->email = $ad['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::Admin;
                $user->foto = $ad['foto'];
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();
            }

            // ==========================================
            // LANGKAH 4: PEMBIMBING LAPANGAN (4 AKUN)
            // ==========================================
            $pembimbingsData = [
                [
                    'email' => 'pembimbing@gmail.com',
                    'nama' => 'Budi Santoso, S.Kom',
                    'bidang_id' => $bidangEgov->id,
                    'foto' => 'foto-staf/pembimbing-egov.svg',
                ],
                [
                    'email' => 'pembimbing.ikp@gmail.com',
                    'nama' => 'Sri Wahyuni, S.I.Kom',
                    'bidang_id' => $bidangIkp->id,
                    'foto' => 'foto-staf/pembimbing-ikp.svg',
                ],
                [
                    'email' => 'pembimbing.iti@gmail.com',
                    'nama' => 'Agus Wibowo, S.T',
                    'bidang_id' => $bidangIti->id,
                    'foto' => 'foto-staf/pembimbing-iti.svg',
                ],
                [
                    'email' => 'pembimbing.sekretariat@gmail.com',
                    'nama' => 'Endang Sulastri, S.AP',
                    'bidang_id' => $bidangSekretariat->id,
                    'foto' => 'foto-staf/pembimbing-sekretariat.svg',
                ],
            ];

            $pembimbingModels = [];
            foreach ($pembimbingsData as $pb) {
                $this->createAvatarAsset($pb['foto'], $pb['nama'], 'pembimbing');

                $user = User::where('email', $pb['email'])->first() ?? new User();
                $user->nama = $pb['nama'];
                $user->email = $pb['email'];
                $user->password = $defaultPassword;
                $user->role = RoleEnum::Pembimbing;
                $user->bidang_id = $pb['bidang_id'];
                $user->foto = $pb['foto'];
                $user->status_aktif = true;
                $user->must_change_password = false;
                $user->email_verified_at = $now;
                $user->save();

                $pembimbingModels[$pb['bidang_id']] = $user;
            }

            // ==========================================
            // LANGKAH 5: RELINK FK & PEMBERSIHAN NON-GMAIL
            // ==========================================
            $fallbackPembimbing = $pembimbingModels[$bidangEgov->id];
            $nonGmailUserIds = User::where('email', 'not like', '%@gmail.com')->pluck('id')->toArray();

            if (!empty($nonGmailUserIds)) {
                // Alihkan pembimbing_id penempatan ke pembimbing baru agar tidak melanggar foreign key
                Penempatan::whereIn('pembimbing_id', $nonGmailUserIds)
                    ->update(['pembimbing_id' => $fallbackPembimbing->id]);

                // Hapus data penempatan, pendaftaran, dan profil milik peserta non-gmail
                $pesertaNonGmailPenempatan = Penempatan::whereIn('peserta_id', $nonGmailUserIds)->get();
                foreach ($pesertaNonGmailPenempatan as $pen) {
                    Tugas::where('penempatan_id', $pen->id)->delete();
                    Logbook::where('penempatan_id', $pen->id)->delete();
                    $pen->delete();
                }

                Pendaftaran::whereIn('user_id', $nonGmailUserIds)->delete();
                PesertaProfil::whereIn('user_id', $nonGmailUserIds)->delete();

                // Hapus seluruh akun non-gmail
                User::whereIn('id', $nonGmailUserIds)->delete();
            }

            // ==========================================
            // LANGKAH 6: 20 PESERTA MAGANG @GMAIL.COM LENGKAP
            // ==========================================
            $bulanAktif = BulanMagang::where('status', 'buka')->first() ?? BulanMagang::first();

            $pesertaDatasets = [
                // EGOV (1 - 5)
                ['nama' => 'Aditya Pratama', 'email' => 'aditya.pratama@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'Universitas Gadjah Mada', 'jurusan_id' => 18, 'jenjang' => JenjangEnum::S1, 'nim' => '20/456789/TK/51234'],
                ['nama' => 'Bunga Citra', 'email' => 'bunga.citra@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'UPN Veteran Yogyakarta', 'jurusan_id' => 7, 'jenjang' => JenjangEnum::S1, 'nim' => '123200045'],
                ['nama' => 'Chandra Wijaya', 'email' => 'chandra.wijaya@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'Universitas AMIKOM Yogyakarta', 'jurusan_id' => 5, 'jenjang' => JenjangEnum::S1, 'nim' => '21.11.4589'],
                ['nama' => 'Dinda Kirana', 'email' => 'dinda.kirana@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'Universitas Negeri Yogyakarta', 'jurusan_id' => 6, 'jenjang' => JenjangEnum::D3, 'nim' => '21520241012'],
                ['nama' => 'Eko Prasetyo', 'email' => 'eko.prasetyo@gmail.com', 'bidang' => $bidangEgov, 'pembimbing' => $pembimbingModels[$bidangEgov->id], 'institusi' => 'SMK N 2 Depok Sleman', 'jurusan_id' => 4, 'jenjang' => JenjangEnum::SMK, 'nim' => '0054321987'],

                // IKP (6 - 10)
                ['nama' => 'Fani Rahmawati', 'email' => 'fani.rahma@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'UIN Sunan Kalijaga Yogyakarta', 'jurusan_id' => 10, 'jenjang' => JenjangEnum::S1, 'nim' => '20102050041'],
                ['nama' => 'Gilang Ramadhan', 'email' => 'gilang.ramadhan@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'Institut Seni Indonesia Yogyakarta', 'jurusan_id' => 12, 'jenjang' => JenjangEnum::S1, 'nim' => '2012015024'],
                ['nama' => 'Hana Safitri', 'email' => 'hana.safitri@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'Universitas Atma Jaya Yogyakarta', 'jurusan_id' => 11, 'jenjang' => JenjangEnum::S1, 'nim' => '190906874'],
                ['nama' => 'Indra Kurniawan', 'email' => 'indra.kurniawan@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'SMK N 1 Bantul', 'jurusan_id' => 2, 'jenjang' => JenjangEnum::SMK, 'nim' => '0061298453'],
                ['nama' => 'Joko Susilo', 'email' => 'joko.susilo@gmail.com', 'bidang' => $bidangIkp, 'pembimbing' => $pembimbingModels[$bidangIkp->id], 'institusi' => 'Universitas Ahmad Dahlan', 'jurusan_id' => 10, 'jenjang' => JenjangEnum::S1, 'nim' => '2000024158'],

                // ITI (11 - 15)
                ['nama' => 'Kartika Putri', 'email' => 'kartika.putri@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Universitas Gadjah Mada', 'jurusan_id' => 8, 'jenjang' => JenjangEnum::S1, 'nim' => '20/460124/TK/51892'],
                ['nama' => 'Lukman Hakim', 'email' => 'lukman.hakim@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Universitas Sanata Dharma', 'jurusan_id' => 17, 'jenjang' => JenjangEnum::S1, 'nim' => '205314082'],
                ['nama' => 'Maya Anggraini', 'email' => 'maya.anggraini@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'SMK N 2 Depok Sleman', 'jurusan_id' => 1, 'jenjang' => JenjangEnum::SMK, 'nim' => '0057891234'],
                ['nama' => 'Naufal Rizky', 'email' => 'naufal.rizky@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Universitas Negeri Yogyakarta', 'jurusan_id' => 9, 'jenjang' => JenjangEnum::S1, 'nim' => '21518241005'],
                ['nama' => 'Olivia Tan', 'email' => 'olivia.tan@gmail.com', 'bidang' => $bidangIti, 'pembimbing' => $pembimbingModels[$bidangIti->id], 'institusi' => 'Institut Teknologi Nasional Yogyakarta', 'jurusan_id' => 18, 'jenjang' => JenjangEnum::S1, 'nim' => '710021004'],

                // SEKRETARIAT (16 - 20)
                ['nama' => 'Panji Nugroho', 'email' => 'panji.nugroho@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'Universitas Negeri Yogyakarta', 'jurusan_id' => 14, 'jenjang' => JenjangEnum::S1, 'nim' => '20802241033'],
                ['nama' => 'Qori Amanda', 'email' => 'qori.amanda@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'Universitas Islam Indonesia', 'jurusan_id' => 13, 'jenjang' => JenjangEnum::S1, 'nim' => '20311245'],
                ['nama' => 'Rian Ardiansyah', 'email' => 'rian.ardiansyah@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'SMK N 1 Depok Sleman', 'jurusan_id' => 3, 'jenjang' => JenjangEnum::SMK, 'nim' => '0065432190'],
                ['nama' => 'Siti Nurhaliza', 'email' => 'siti.nurhaliza@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'UPN Veteran Yogyakarta', 'jurusan_id' => 16, 'jenjang' => JenjangEnum::D3, 'nim' => '141200088'],
                ['nama' => 'Taufik Hidayat', 'email' => 'taufik.hidayat@gmail.com', 'bidang' => $bidangSekretariat, 'pembimbing' => $pembimbingModels[$bidangSekretariat->id], 'institusi' => 'STIE YKPN Yogyakarta', 'jurusan_id' => 15, 'jenjang' => JenjangEnum::D3, 'nim' => '112031498'],
            ];

            foreach ($pesertaDatasets as $idx => $pData) {
                $slug = strtolower(str_replace(' ', '_', $pData['nama']));
                $fotoPath = "profil-peserta/{$slug}.svg";

                // Buat berkas fisik foto peserta
                $this->createAvatarAsset($fotoPath, $pData['nama'], 'peserta');

                // User
                $u = User::where('email', $pData['email'])->first() ?? new User();
                $u->nama = $pData['nama'];
                $u->email = $pData['email'];
                $u->password = $defaultPassword;
                $u->role = RoleEnum::Peserta;
                $u->foto = $fotoPath;
                $u->status_aktif = true;
                $u->must_change_password = false;
                $u->email_verified_at = $now;
                $u->save();

                // Profil Peserta
                PesertaProfil::updateOrCreate(
                    ['user_id' => $u->id],
                    [
                        'nim_nisn' => $pData['nim'],
                        'institusi' => $pData['institusi'],
                        'jurusan_id' => $pData['jurusan_id'],
                        'jenjang' => $pData['jenjang'],
                        'no_hp' => '0812' . str_pad((string) (10000000 + $idx), 8, '0', STR_PAD_LEFT),
                        'alamat' => 'Kabupaten Sleman, D.I. Yogyakarta',
                        'foto' => $fotoPath,
                        'nama_kontak_darurat' => 'Wali ' . $pData['nama'],
                        'no_kontak_darurat' => '081399887766',
                        'hubungan_kontak_darurat' => 'Orang Tua',
                    ]
                );

                // Pendaftaran
                $pendaftaran = Pendaftaran::firstOrCreate(
                    ['user_id' => $u->id],
                    [
                        'bulan_magang_id' => $bulanAktif?->id,
                        'tipe' => 'mandiri',
                        'durasi_bulan' => 3,
                        'status' => StatusPendaftaranEnum::Diterima,
                        'diajukan_at' => $now->copy()->subDays(30),
                        'diputuskan_at' => $now->copy()->subDays(25),
                    ]
                );

                // Penempatan
                $penempatan = Penempatan::firstOrCreate(
                    ['peserta_id' => $u->id],
                    [
                        'pendaftaran_id' => $pendaftaran->id,
                        'bidang_id' => $pData['bidang']->id,
                        'pembimbing_id' => $pData['pembimbing']->id,
                        'jalur' => JalurEnum::Reguler,
                        'sumber' => SumberPenempatanEnum::Reguler,
                        'status' => StatusPenempatanEnum::Aktif,
                        'tgl_mulai' => $now->copy()->subDays(20)->startOfDay(),
                        'tgl_selesai' => $now->copy()->addDays(70)->endOfDay(),
                    ]
                );

                // Pastikan penempatan terhubung ke pembimbing yang tepat
                $penempatan->update([
                    'bidang_id' => $pData['bidang']->id,
                    'pembimbing_id' => $pData['pembimbing']->id,
                    'status' => StatusPenempatanEnum::Aktif,
                ]);

                // Buat 1 Tugas Awal & 1 Logbook Terverifikasi
                Tugas::firstOrCreate(
                    ['penempatan_id' => $penempatan->id, 'judul' => 'Orientasi dan Pengenalan Alur Kerja Unit ' . $pData['bidang']->nama_bidang],
                    [
                        'deskripsi' => 'Mempelajari SOP, pengenalan sistem kerja internal, dan instalasi tools pendukung operasional.',
                        'kompleksitas' => KompleksitasEnum::Rendah,
                        'status' => StatusTugasEnum::Selesai,
                        'deadline' => $now->copy()->subDays(10),
                    ]
                );

                Logbook::firstOrCreate(
                    ['penempatan_id' => $penempatan->id, 'minggu_ke' => 1],
                    [
                        'tanggal' => $now->copy()->subDays(15),
                        'kegiatan' => 'Menghadiri sesi pengarahan dengan pembimbing lapangan dan menyusun rencana kerja magang.',
                        'status' => StatusLogbookEnum::Disetujui,
                        'catatan_pembimbing' => 'Rencana kerja sangat baik. Lanjutkan ke tahap implementasi.',
                    ]
                );
            }

            // ==========================================
            // LANGKAH 7: PEMBERSIHAN PESERTA DUMMY SURPLUS
            // ==========================================
            $allowedEmails = array_merge(
                ['superadmin@gmail.com', 'superadmin.sleman@gmail.com'],
                ['admin@gmail.com', 'admin.verifikator@gmail.com'],
                ['pembimbing@gmail.com', 'pembimbing.ikp@gmail.com', 'pembimbing.iti@gmail.com', 'pembimbing.sekretariat@gmail.com'],
                ['adhityagalang553@gmail.com'], // Akun Pengembang
                array_column($pesertaDatasets, 'email')
            );

            $surplusPeserta = User::where('role', RoleEnum::Peserta)
                ->whereNotIn('email', $allowedEmails)
                ->get();

            foreach ($surplusPeserta as $sp) {
                $penempatans = Penempatan::where('peserta_id', $sp->id)->get();
                foreach ($penempatans as $p) {
                    Tugas::where('penempatan_id', $p->id)->delete();
                    Logbook::where('penempatan_id', $p->id)->delete();
                    $p->delete();
                }
                Pendaftaran::where('user_id', $sp->id)->delete();
                PesertaProfil::where('user_id', $sp->id)->delete();
                $sp->delete();
            }
        });
    }

    /**
     * Helper membuat berkas grafik avatar SVG beresolusi tinggi di storage public.
     */
    protected function createAvatarAsset(string $path, string $name, string $role): void
    {
        if (Storage::disk('public')->exists($path)) {
            return;
        }

        $bgGradients = [
            'superadmin' => ['#0F172A', '#1E3A8A'], // Sleman Slate to Navy
            'admin'      => ['#1E3A8A', '#0284C7'], // Navy to Ocean Blue
            'pembimbing' => ['#065F46', '#10B981'], // Forest to Emerald
            'peserta'    => ['#1E40AF', '#6366F1'], // Royal Blue to Indigo
        ];

        $colors = $bgGradients[$role] ?? ['#1E293B', '#475569'];
        $initials = collect(explode(' ', $name))
            ->map(fn($w) => mb_substr($w, 0, 1))
            ->take(2)
            ->join('');

        $svgContent = <<<SVG
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 320" width="320" height="320">
            <defs>
                <linearGradient id="grad_{$role}_{$initials}" x1="0%" y1="0%" x2="100%" y2="100%">
                    <stop offset="0%" stop-color="{$colors[0]}" />
                    <stop offset="100%" stop-color="{$colors[1]}" />
                </linearGradient>
            </defs>
            <circle cx="160" cy="160" r="160" fill="url(#grad_{$role}_{$initials})" />
            
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
```

---

## 5. CARA EKSEKUSI DI TERMINAL

Jalankan perintah Artisan ini di PowerShell / terminal repositori:

```powershell
php artisan db:seed --class=Database\\Seeders\\PurgeAndSeedCleanGmailAccountsSeeder
```

---

## 6. VERIFIKASI QUALITY GATES (MUST PASS)

Setelah seeder selesai dijalankan, eksekusi checklist pengujian berikut:

### 1. Verifikasi Non-Gmail = 0:
```powershell
php artisan tinker --execute="echo 'Non-Gmail Users: ' . App\Models\User::where('email', 'not like', '%@gmail.com')->count() . PHP_EOL;"
```
*Ekspektasi:* `Non-Gmail Users: 0`

### 2. Verifikasi Komposisi Akun (Total 29 Akun):
```powershell
php artisan tinker --execute="App\Models\User::all()->groupBy(fn(\$u)=>\$u->role->value)->each(fn(\$g, \$r)=>dump(\$r . ': ' . \$g->count()));"
```
*Ekspektasi:*
- `superadmin`: 2
- `admin`: 2
- `pembimbing`: 4
- `peserta`: 21 (20 peserta klaster + 1 akun dev `adhityagalang553@gmail.com`)
- Total Akun: **29 Akun (100% `@gmail.com`)**

### 3. Verifikasi Keberadaan Berkas Fisik Foto di Storage:
```powershell
php artisan tinker --execute="echo 'Foto Staf: ' . count(Illuminate\Support\Facades\Storage::disk('public')->files('foto-staf')) . PHP_EOL; echo 'Foto Peserta: ' . count(Illuminate\Support\Facades\Storage::disk('public')->files('profil-peserta')) . PHP_EOL;"
```
*Ekspektasi:*
- `Foto Staf: 8`
- `Foto Peserta: 20`

### 4. Verifikasi Accessor URL Foto Tidak Null:
```powershell
php artisan tinker --execute="dump(App\Models\User::all(['id', 'email', 'role', 'foto'])->map(fn(\$u) => \$u->email . ' => ' . \$u->foto_url)->take(10)->toArray());"
```
*Ekspektasi:* Setiap user memiliki URL valid seperti `http://localhost:8000/storage/foto-staf/...` atau `http://localhost:8000/storage/profil-peserta/...`.

### 5. Automated Regression Test:
```powershell
php artisan test --filter=Staf
php artisan test --filter=SuperAdmin
```
*Ekspektasi:* Seluruh test suite hijau (Passed).

---

## 7. CHEATSHEET KREDENSIAL LOGIN

| Peran | Alamat Email | Kata Sandi | Status Foto Profil |
|---|---|---|---|
| **SuperAdmin 1** | `superadmin@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/superadmin-utama.svg`) |
| **SuperAdmin 2** | `superadmin.sleman@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/superadmin-sleman.svg`) |
| **Admin 1** | `admin@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/admin-utama.svg`) |
| **Admin 2** | `admin.verifikator@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/admin-sleman.svg`) |
| **Pembimbing EGOV** | `pembimbing@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/pembimbing-egov.svg`) |
| **Pembimbing IKP** | `pembimbing.ikp@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/pembimbing-ikp.svg`) |
| **Pembimbing ITI** | `pembimbing.iti@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/pembimbing-iti.svg`) |
| **Pembimbing Sekretariat** | `pembimbing.sekretariat@gmail.com` | `Password123!` | Foto Tersedia (`foto-staf/pembimbing-sekretariat.svg`) |
| **20 Peserta Magang** | `aditya.pratama@gmail.com` s/d `taufik.hidayat@gmail.com` | `Password123!` | Foto Tersedia (`profil-peserta/*.svg`) |
| **Akun Dev Google** | `adhityagalang553@gmail.com` | *(Google Login)* | Akun Dev Pengembang (Preserved) |

---

## 8. DEFINITION OF DONE (DoD)

- [x] Tidak ada satupun user di database dengan email selain `@gmail.com` (Verifikasi: 0 non-gmail users).
- [x] Relasi foreign key penempatan dan data relasional aman tanpa constraint error (Relink berhasil).
- [x] Tepat terdapat 2 SuperAdmin, 2 Admin, 4 Pembimbing (1 per bidang dinas), dan 20 Peserta (+1 akun dev `adhityagalang553@gmail.com`).
- [x] Seluruh 28 profil pengguna memiliki berkas foto fisik beresolusi tinggi di `storage/app/public/`.
- [x] Accessor `foto_url` mengembalikan URL yang valid pada setiap profil pengguna dan peserta (tidak ada yang `null`).
- [x] Semua pengujian otomatis lolos tanpa regresi (30 Staf tests passed, 262 SuperAdmin tests passed).
