# TASK BRIEF SENIOR DEVELOPER: TOGGLE VISIBILITAS KATA SANDI (TOMBOL MATA) & FITUR SALIN KREDENSIAL AWAL AKUN STAF INTERNAL

> **Dari:** Lead Architect / Senior Software Engineer  
> **Kepada:** Junior Developer / AI Agent (Gemini 3.8 Flash / Claude 3.5 Sonnet / dsb.)  
> **Target Repositori:** `MAGKOM` (Portal Manajemen Magang Dinas Kominfo Sleman)  
> **Status:** READY FOR IMPLEMENTATION  
> **Tingkat Kesulitan:** Menengah-Rendah (*Focused UI/UX & Controller Refinement*)  
> **Dokumentasi Terkait:** `AGENTS.md`, `docs/07-DESIGN-SYSTEM-MAGKOM.md`, `docs/05-KEAMANAN-RBAC-DAN-AUDIT-TRAIL.md`, `docs/plans/2026-11-09-salin-kredensial-awal-peserta-jalur-khusus.md`

---

## 1. 🎯 RINGKASAN MISI (MISSION OBJECTIVE)

SuperAdmin memerlukan peningkatan pengalaman pengguna (UX) dan efisiensi operasional pada modul **Manajemen Staf Internal (`/superadmin/staf`)**:

1. **Tombol Mata (Toggle Password Visibility) di Modal Tambah Staf Baru:**
   - Pada form popup/modal *"Tambah Akun Staf Baru"*, input **"Kata Sandi Awal"** saat ini bertipe `password` statis (hanya menampilkan titik-titik hitam).
   - SuperAdmin kesulitan memeriksa kebenaran pengetikan kata sandi sebelum menekan tombol simpan.
   - Diperlukan tombol interaktif berikon mata di sisi kanan input untuk menampilkan/menyembunyikan kata sandi secara instan (*show/hide password*), lengkap dengan atribut aksesibilitas (`aria-label`).
   - *(Bonus Konsistensi):* Terapkan juga tombol mata yang sama pada modal *"Reset Kata Sandi Staf"*.

2. **Fitur "Salin Akun" & Status Kredensial Awal di Tabel Daftar Akun Staf:**
   - Pada tabel *"Daftar Akun Pengguna Internal"*, saat ini hanya tersedia kolom *Status Akun* dan *Aksi* (Edit, Sandi Baru, Reset, Nonaktifkan).
   - SuperAdmin harus mencatat atau menyalin kata sandi awal secara manual dari pesan flash setelah akun dibuat atau diregenerasi.
   - Kita harus menerapkan paritas penuh dengan tabel *"Daftar Peserta Magang Jalur Khusus"* (`/superadmin/slot-khusus`):
     - Tambahkan kolom **`Kredensial Awal`**.
     - Sediakan tombol **`[📋 Salin Akun]`** (`.sa-btn-copy-kredensial`) yang langsung menyalin format teks ramah WhatsApp ke clipboard.
     - Sediakan indikator status kredensial:
       - 🟡 **`Sandi Awal Aktif`** (`.sa-badge-cred-active`): Jika staf belum melakukan aktivasi/login pertama (`must_change_password = true`).
       - 🟢 **`Sandi Diubah Staf`** (`.sa-badge-cred-changed`): Jika staf sudah mengganti kata sandi mandiri (`must_change_password = false`).

---

## 2. 🔍 ANALISIS PERBANDINGAN & BUKTI VISUAL

| Area | Kondisi Saat Ini (Current) | Kondisi Target yang Diharapkan (Target) |
| :--- | :--- | :--- |
| **Modal Tambah Staf** | Input `Kata Sandi Awal` hanya bertipe `password` tertutup tanpa tombol preview. | Input dibungkus container relatif dengan tombol mata (`.toggle-password-btn`) di ujung kanan yang dapat di-toggle `password` ↔ `text`. |
| **Modal Reset Sandi** | Input `Kata Sandi Baru` hanya bertipe `password` tertutup. | Memiliki tombol mata (`.toggle-password-btn`) yang konsisten. |
| **Tabel Staf Internal** | Kolom hanya: *Nama & Email*, *Peran Akses*, *Unit Kerja / Bidang*, *Status Akun*, *Aksi*. Tidak ada tombol salin akun. | Menambahkan kolom *Kredensial Awal* yang memuat tombol `[Salin Akun]` dan badge status (`Sandi Awal Aktif` / `Sandi Diubah Staf`). |
| **Backend `StafController`** | Saat `store()`, `regenerateTemporaryPassword()`, dan `resetPassword()`, nilai teks sandi tidak disimpan ke kolom `users.password_awal`. | Nilai plain password disimpan ke `$staf->password_awal` yang otomatis dienkripsi secara transparan oleh Eloquent casting. |

---

## 3. 🛡️ ATURAN ARSITEKTUR & KEAMANAN WAJIB (NON-NEGOTIABLE)

Sebagai Junior Developer / AI Agent, Anda **WAJIB** membaca dan mematuhi aturan berikut sebelum menyentuh file kode:

1. **Prinsip Enkripsi Simetris (Zero Plaintext in Database):**
   - Kolom `users.password_awal` **BUKAN** teks biasa. Model `App\Models\User` telah mengonfigurasi casting:
     ```php
     'password_awal' => 'encrypted',
     ```
   - Laravel secara otomatis mengenkripsi string menggunakan AES-256-CBC berbasis `APP_KEY` sebelum disimpan ke MySQL, dan mendekripsinya secara transparan saat diakses via `$user->password_awal`.
   - **JANGAN PERNAH** menggunakan fungsi hash manual atau plain SQL insert tanpa Eloquent model.

2. **Siklus Hidup Kredensial & BR-15:**
   - Kolom `password_awal` hanya relevan selama `must_change_password = true`.
   - Di `app/Http/Controllers/Auth/PasswordController.php`, begitu staf masuk dan mengganti kata sandinya, sistem sudah otomatis mengeksekusi:
     ```php
     $user->forceFill([
         'password' => Hash::make($request->input('password')),
         'must_change_password' => false,
         'password_awal' => null, // Kredensial awal dihapus permanen!
     ])->save();
     ```
   - Maka jika `must_change_password == false`, tombol salin akan menyalin teks peringatan bahwa kata sandi sudah diperbarui dan tidak berlaku lagi.

3. **Strict Blade Rule (Aturan Mutlak Repository MAGKOM):**
   - **DILARANG KERAS** menulis tag `<style>` di dalam Blade.
   - **DILARANG KERAS** menulis tag `<script>` di dalam Blade.
   - **DILARANG KERAS** menulis inline event handler seperti `onclick="togglePassword()"`, `onchange="..."`, dsb.
   - Semua manipulasi DOM wajib melalui file JavaScript berekstensi `.js` di `resources/js/` menggunakan Event Delegation.
   - Semua CSS styling wajib berada di `resources/css/` (sudah tersedia kelas-kelas bawaan di `resources/css/pages/superadmin/superadmin-common.css`).

---

## 4. 📋 SPESIFIKASI TEKNIS & PERUBAHAN BERKAS

Berikut adalah daftar 4 berkas yang akan disentuh beserta perubahannya:

```
app/Http/Controllers/SuperAdmin/StafController.php  <-- Update 3 method untuk isi password_awal
resources/views/superadmin/staf/index.blade.php    <-- Update Blade: tombol mata & kolom kredensial
resources/js/pages/superadmin/staf-index.js        <-- Tambah handler toggle password & copy clipboard
tests/Feature/SuperAdminStafCredentialCopyTest.php <-- Buat unit/feature test baru
```

---

### 🔹 TUGAS 1: Backend Controller (`app/Http/Controllers/SuperAdmin/StafController.php`)

Perbarui 3 method di controller ini agar kolom `password_awal` terisi nilai kredensial baru:

#### 1.1 Method `store(Request $request)` (Sekitar Baris 110-120)
Saat membuat akun staf baru, tambahkan penugasan `$staf->password_awal = $plainPassword;`:
```php
$staf = DB::transaction(function () use ($validated, $plainPassword, $roleEnum, $bidangId, $request) {
    $staf = new User;
    $staf->nama = $validated['nama'];
    $staf->email = $validated['email'];
    $staf->password = Hash::make($plainPassword);
    $staf->password_awal = $plainPassword; // <-- TAMBAHKAN INI (tersimpan otomatis terenkripsi)
    $staf->role = $roleEnum;
    $staf->bidang_id = $bidangId;
    $staf->email_verified_at = now();
    $staf->must_change_password = true;
    $staf->status_aktif = true;
    $staf->dibuat_oleh = $request->user()->id;
    $staf->save();
```

#### 1.2 Method `resetPassword(User $staf, Request $request)` (Sekitar Baris 222-226)
Saat SuperAdmin mereset kata sandi manual staf, update juga `password_awal`:
```php
$staf->update([
    'password' => Hash::make($validated['password_baru']),
    'password_awal' => $validated['password_baru'], // <-- TAMBAHKAN INI
    'must_change_password' => true,
    'remember_token' => null,
]);
```

#### 1.3 Method `regenerateTemporaryPassword(User $staf, Request $request)` (Sekitar Baris 327-331)
Saat SuperAdmin mengklik tombol "Sandi Baru" (regenerasi instan), update juga `password_awal`:
```php
$staf->update([
    'password' => Hash::make($tempPassword),
    'password_awal' => $tempPassword, // <-- TAMBAHKAN INI
    'must_change_password' => true,
    'remember_token' => null,
]);
```

---

### 🔹 TUGAS 2: Frontend Blade View (`resources/views/superadmin/staf/index.blade.php`)

#### 2.1 Tambahkan Kolom "Kredensial Awal" pada Header Tabel (Sekitar Baris 169-177)
Ubah header tabel dari 5 kolom menjadi 6 kolom:
```blade
<thead class="sa-table-head">
    <tr class="headings">
        <th>{!! __('Nama & Email') !!}</th>
        <th style="width: 180px;">Peran Akses</th>
        <th>{{ __('Unit Kerja / Bidang') }}</th>
        <th style="width: 120px; text-align: center;">Status Akun</th>
        <th style="width: 150px; text-align: center;">{{ __('Kredensial Awal') }}</th>
        <th style="text-align: right; width: 230px;">Aksi</th>
    </tr>
</thead>
```

#### 2.2 Tambahkan Sel Data "Kredensial Awal" pada Baris Tabel (Sekitar Baris 247)
Tepat setelah kolom `Status Akun` (`</td>`) dan sebelum kolom `Aksi` (`<td>`), sisipkan sel berikut:
```blade
<td style="padding: 1rem 1rem; text-align: center; white-space: nowrap;">
    <button type="button" 
            class="sa-btn-copy-kredensial" 
            data-nama="{{ $staf->nama }}"
            data-email="{{ $staf->email }}"
            data-role="{{ $staf->role->label() }}"
            data-sandi="{{ $staf->password_awal ?? '' }}"
            data-status="{{ $staf->must_change_password ? 'aktif' : 'changed' }}"
            data-login-url="{{ route('login') }}"
            title="{{ __('Salin Akun & Kata Sandi Awal untuk WhatsApp') }}">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
        <span>{{ __('Salin Akun') }}</span>
    </button>
    <div style="margin-top: 0.35rem;">
        @if($staf->must_change_password)
            <span class="sa-badge-cred-active" title="{{ __('Staf belum aktivasi / belum mengganti kata sandi awal') }}">
                <span style="width: 5px; height: 5px; border-radius: 50%; background: #D97706;"></span>
                {{ __('Sandi Awal Aktif') }}
            </span>
        @else
            <span class="sa-badge-cred-changed" title="{{ __('Staf sudah memperbarui kata sandi pribadi; sandi awal tidak berlaku lagi untuk login') }}">
                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                {{ __('Sandi Diubah Staf') }}
            </span>
        @endif
    </div>
</td>
```

#### 2.3 Tambahkan Tombol Mata pada Modal Tambah Staf Baru (`#modalTambahStaf`)
Cari field kata sandi awal (sekitar baris 361-370) dan ganti menjadi:
```blade
<div>
    <label for="tambahPassword" style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
        {{ __('Kata Sandi Awal') }} <span style="color: var(--red-600);">*</span>
    </label>
    <div style="position: relative;">
        <input type="password" 
               id="tambahPassword" 
               name="password" 
               required 
               placeholder="{{ __('Minimal 8 karakter (huruf & angka)') }}" 
               style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 2.75rem 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
        <button type="button" 
                class="toggle-password-btn" 
                data-toggle-target="tambahPassword" 
                aria-label="{{ __('Lihat kata sandi') }}" 
                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: var(--neutral-500); padding: 6px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </button>
    </div>
    <span style="font-size: 0.75rem; color: var(--neutral-600); margin-top: 0.375rem; display: block; line-height: 1.4;">
        {{ __('Staf akan diwajibkan mengganti kata sandi pada saat pertama kali login') }} (flag <code>must_change_password</code> {{ __('aktif') }}).
    </span>
</div>
```

#### 2.4 Tambahkan Tombol Mata pada Modal Reset Sandi (`#modalResetSandi`)
Cari field kata sandi baru (sekitar baris 402-407) dan ganti menjadi:
```blade
<div>
    <label for="resetPasswordBaru" style="display: block; font-size: 0.8125rem; font-weight: 700; color: var(--neutral-800); margin-bottom: 0.375rem;">
        {{ __('Kata Sandi Baru') }} <span style="color: var(--red-600);">*</span>
    </label>
    <div style="position: relative;">
        <input type="password" 
               id="resetPasswordBaru" 
               name="password_baru" 
               required 
               placeholder="{{ __('Minimal 8 karakter kombinasi huruf & angka)') }}" 
               style="width: 100%; height: 42px; border: 1px solid var(--neutral-400); border-radius: 8px; padding: 0 2.75rem 0 0.875rem; font-size: 0.875rem; font-family: inherit;">
        <button type="button" 
                class="toggle-password-btn" 
                data-toggle-target="resetPasswordBaru" 
                aria-label="{{ __('Lihat kata sandi') }}" 
                style="position: absolute; right: 8px; top: 50%; transform: translateY(-50%); background: transparent; border: none; cursor: pointer; color: var(--neutral-500); padding: 6px; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px;">
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        </button>
    </div>
</div>
```

---

### 🔹 TUGAS 3: JavaScript Interaksi (`resources/js/pages/superadmin/staf-index.js`)

Buka `resources/js/pages/superadmin/staf-index.js` dan tambahkan fungsionalitas berikut:

#### 3.1 Import MagKomToast di Atas Berkas
```javascript
import { MagKomToast } from '../../components/toast.js';
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';
```

#### 3.2 Tambahkan Fungsi `togglePasswordVisibility(fieldId, btn)`
```javascript
export function togglePasswordVisibility(fieldId, btn) {
    const input = document.getElementById(fieldId);
    if (!input || !btn) return;

    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';

    if (isPassword) {
        btn.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path>
            </svg>
        `;
        btn.setAttribute('aria-label', 'Sembunyikan kata sandi');
    } else {
        btn.innerHTML = `
            <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
        `;
        btn.setAttribute('aria-label', 'Lihat kata sandi');
    }
}
```

#### 3.3 Tambahkan Handler Salin Kredensial via Event Delegation
Di dalam listener `document.addEventListener('click', async (event) => { ... })`:
```javascript
// Trigger Toggle Password Visibility (Mata)
const toggleBtn = event.target.closest('.toggle-password-btn');
if (toggleBtn) {
    event.preventDefault();
    const targetId = toggleBtn.dataset.toggleTarget;
    if (targetId) {
        togglePasswordVisibility(targetId, toggleBtn);
    }
    return;
}

// Trigger Salin Kredensial Akun Staf
const copyBtn = event.target.closest('.sa-btn-copy-kredensial');
if (copyBtn) {
    event.preventDefault();

    const nama = copyBtn.dataset.nama || 'Staf';
    const email = copyBtn.dataset.email || '-';
    const role = copyBtn.dataset.role || 'Staf Internal';
    const sandi = copyBtn.dataset.sandi || '';
    const status = copyBtn.dataset.status || 'aktif';
    const loginUrl = copyBtn.dataset.loginUrl || window.location.origin + '/login';

    let statusText = 'Demi keamanan akun dinas, Anda wajib mengganti kata sandi ini saat pertama kali login ke portal MagKom.';
    if (status === 'changed') {
        statusText = 'Staf telah memperbarui kata sandi pribadinya di portal. Kata sandi awal ini sudah kadaluwarsa/tidak berlaku lagi untuk login.';
    }

    const sandiDisplay = sandi ? sandi : '(Tidak tersimpan / Akun lama)';

    const textToCopy = `*AKUN STAF INTERNAL DISKOMINFO SLEMAN (MAGKOM)*
Nama: ${nama}
Email: ${email}
Peran Akses: ${role}
Kata Sandi Awal: ${sandiDisplay}
Tautan Login: ${loginUrl}

*Catatan:* ${statusText}`;

    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(textToCopy);
        } else {
            const textArea = document.createElement('textarea');
            textArea.value = textToCopy;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            document.execCommand('copy');
            textArea.remove();
        }

        const originalHTML = copyBtn.innerHTML;
        copyBtn.classList.add('is-copied');
        copyBtn.innerHTML = `
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>Tersalin!</span>
        `;

        setTimeout(() => {
            copyBtn.classList.remove('is-copied');
            copyBtn.innerHTML = originalHTML;
        }, 2500);

        if (MagKomToast) {
            MagKomToast.show({
                type: 'success',
                title: 'Kredensial Disalin!',
                message: `Format akun untuk ${nama} berhasil disalin ke clipboard.`,
            });
        }
    } catch (err) {
        console.error('Gagal menyalin kredensial staf:', err);
    }
    return;
}
```

---

### 🔹 TUGAS 4: Automated Testing (`tests/Feature/SuperAdminStafCredentialCopyTest.php`)

Buat berkas test baru `tests/Feature/SuperAdminStafCredentialCopyTest.php` untuk membuktikan fungsionalitas ini secara tuntas:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\RoleEnum;
use App\Models\Bidang;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class SuperAdminStafCredentialCopyTest extends TestCase
{
    use RefreshDatabase;

    protected User $superAdmin;
    protected Bidang $bidang;

    protected function setUp(): void
    {
        parent::setUp();

        $this->superAdmin = User::factory()->create([
            'role' => RoleEnum::SuperAdmin,
            'status_aktif' => true,
        ]);

        $this->bidang = Bidang::create([
            'nama_bidang' => 'Sistem Informasi & Keamanan Siber',
            'aktif' => true,
        ]);
    }

    public function test_superadmin_can_create_staff_and_initial_password_is_saved_encrypted(): void
    {
        $plainPassword = 'PasswordStaf123!';

        $response = $this->actingAs($this->superAdmin)->post(route('superadmin.staf.store'), [
            'nama' => 'Drs. Hendro Wibowo',
            'email' => 'hendro.wibowo@sleman.go.id',
            'role' => RoleEnum::Pembimbing->value,
            'bidang_id' => $this->bidang->id,
            'password' => $plainPassword,
        ]);

        $response->assertRedirect(route('superadmin.staf.index'));
        $response->assertSessionHas('success');

        $staf = User::where('email', 'hendro.wibowo@sleman.go.id')->first();
        $this->assertNotNull($staf);
        $this->assertTrue($staf->must_change_password);

        // Verifikasi enkripsi di DB mentah
        $rawPasswordAwal = DB::table('users')->where('id', $staf->id)->value('password_awal');
        $this->assertNotEmpty($rawPasswordAwal);
        $this->assertNotEquals($plainPassword, $rawPasswordAwal);

        // Verifikasi transparansi Eloquent decrypt
        $this->assertSame($plainPassword, $staf->fresh()->password_awal);
    }

    public function test_regenerating_staff_password_updates_password_awal(): void
    {
        $staf = User::factory()->create([
            'role' => RoleEnum::Admin,
            'status_aktif' => true,
            'password_awal' => 'Lama12345!',
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.regenerate-password', $staf));

        $response->assertRedirect(route('superadmin.staf.index'));
        $response->assertSessionHas('success');

        $stafFresh = $staf->fresh();
        $this->assertTrue($stafFresh->must_change_password);
        $this->assertNotEmpty($stafFresh->password_awal);
        $this->assertNotEquals('Lama12345!', $stafFresh->password_awal);
    }

    public function test_manual_password_reset_updates_password_awal(): void
    {
        $staf = User::factory()->create([
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'status_aktif' => true,
            'password_awal' => null,
            'must_change_password' => false,
        ]);

        $newPassword = 'ResetSandiBaru2026!';

        $response = $this->actingAs($this->superAdmin)
            ->post(route('superadmin.staf.reset-password', $staf), [
                'password_baru' => $newPassword,
            ]);

        $response->assertRedirect(route('superadmin.staf.index'));
        $stafFresh = $staf->fresh();

        $this->assertTrue($stafFresh->must_change_password);
        $this->assertSame($newPassword, $stafFresh->password_awal);
    }

    public function test_staf_table_renders_copy_button_and_status_badges(): void
    {
        // 1. Staf dengan sandi awal aktif
        $stafAktif = User::factory()->create([
            'nama' => 'Agus Purnomo',
            'email' => 'agus.purnomo@sleman.go.id',
            'role' => RoleEnum::Admin,
            'password_awal' => 'AgusPass123!',
            'must_change_password' => true,
        ]);

        // 2. Staf yang sudah ganti sandi
        $stafChanged = User::factory()->create([
            'nama' => 'Ratna Dewi',
            'email' => 'ratna.dewi@sleman.go.id',
            'role' => RoleEnum::Pembimbing,
            'bidang_id' => $this->bidang->id,
            'password_awal' => null,
            'must_change_password' => false,
        ]);

        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.staf.index'));
        $response->assertOk();

        // Verifikasi tombol salin kredensial awal
        $response->assertSee('sa-btn-copy-kredensial', false);
        $response->assertSee('Salin Akun');
        $response->assertSee('Kredensial Awal');

        // Verifikasi badge status
        $response->assertSee('Sandi Awal Aktif');
        $response->assertSee('Sandi Diubah Staf');
    }
}
```

---

## 5. ✅ QUALITY GATES & VERIFIKASI SEBELUM SELESAI

Setelah seluruh implementasi di atas selesai, jalankan urutan quality gate berikut pada terminal:

1. **Jalankan Test Suite Spesifik:**
   ```powershell
   php artisan test --filter=SuperAdminStafCredentialCopyTest
   ```
   *(Harus 100% HIJAU / PASSING).*

2. **Jalankan Regression Test Modul Staf Terkait:**
   ```powershell
   php artisan test --filter=AdvancedRoleEnhancementPhaseSixTest
   php artisan test --filter=BackendSupplementalFeaturesTest
   ```

3. **Kompilasi Aset Frontend (Vite):**
   ```powershell
   npm run build
   ```
   *(Pastikan tidak ada sintaks error pada JavaScript dan Vite manifest ter-generate bersih).*

4. **Pemeriksaan Standar Kode & Format (Pint):**
   ```powershell
   ./vendor/bin/pint --test
   ```

5. **Static Analysis (PHPStan):**
   ```powershell
   ./vendor/bin/phpstan analyse --memory-limit=1G
   ```

---

## 6. 📝 PANDUAN COMMIT (GIT CONVENTION)

Gunakan commit message imperatif sesuai standar proyek MAGKOM:

```bash
git add app/Http/Controllers/SuperAdmin/StafController.php resources/views/superadmin/staf/index.blade.php resources/js/pages/superadmin/staf-index.js tests/Feature/SuperAdminStafCredentialCopyTest.php
git commit -m "feat(superadmin): add password toggle button and credential copy feature in staff accounts"
```

---
*Dokumen ini disusun oleh Senior Developer MAGKOM sebagai panduan kerja mandiri, presisi, dan aman bagi Junior Developer atau model AI eksekutor.*
