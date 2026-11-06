# [SENIOR DEV TASK] Mengubah Validasi & Logika Catatan Logbook Menjadi Opsional (Bisa Dikosongkan/Dihapus)

**ID Task**: `TASK-LOGBOOK-NULLABLE-NOTES-01`  
**Target Eksekutor**: Junior Full-Stack Developer / AI Model Ringan (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Author / Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation (Plan / PRD)  
**Tanggal**: 06 November 2026  

---

## 1. Background & Analisis Root Cause

### 1.1 Masalah Pengguna (*User Problem*)
Ketika pembimbing lapangan membuka form inline catatan logbook peserta:
1. Pembimbing ingin **menghapus catatan** yang sebelumnya sudah diberikan, lalu menekan tombol **[Simpan Catatan]**.
2. Atau pembimbing tidak sengaja mengklik **[Beri Catatan]** dan ingin menyimpan tanpa catatan.
3. Namun browser memunculkan popup validasi HTML5:  
   **`! Please fill out this field.`**
4. Dan di backend, request ditolak dengan pesan error validasi:  
   `The catatan pembimbing field is required.`

### 1.2 Root Cause Analysis (RCA)
1. **Frontend (Blade Markup)**:
   Pada `resources/views/pembimbing/logbook/index.blade.php`:
   - Textarea memiliki atribut HTML `required`:
     ```blade
     <textarea name="catatan_pembimbing" ... required class="logbook-note-clean-textarea">
     ```
   - Label memuat tanda bintang merah wajib: `<span class="required-star">*</span>`.
   - Akibatnya browser secara otomatis memblokir form submission sebelum data dikirim ke server.
2. **Backend Form Request (`ReviewLogbookRequest`)**:
   Pada `app/Http/Requests/ReviewLogbookRequest.php`:
   - Rule validasi mengunci kolom catatan menjadi `required`:
     ```php
     'catatan_pembimbing' => [
         'required',
         'string',
         'min:3',
         'max:2000',
     ],
     ```
   - Padahal aturan bisnis **AC-09-4** hanya mewajibkan catatan pembimbing **JIKA status logbook ditetapkan sebagai `perlu_revisi`**.
   - Pada konteks pemberian/pengubahan catatan apresiasi/harian, catatan bersifat **opsional** dan pembimbing berhak mengosongkan/menghapus catatan yang sudah ada.

---

## 2. Target Solusi & Perilaku yang Diharapkan (Expected Behavior)

| Skenario | Tindakan Pembimbing | Perilaku Sistem yang Diharapkan |
|---|---|---|
| **A. Hapus Catatan Lama** | Membuka entri berstatus [Ubah Catatan], menghapus seluruh teks di textarea hingga kosong, lalu klik [Simpan Catatan]. | Form berhasil disubmit tanpa blokir browser. Backend meng-update `catatan_pembimbing = NULL`. Setelah reload/render, kotak preview catatan hilang, dan tombol kembali menjadi **[Beri Catatan]**. |
| **B. Beri Catatan Baru** | Mengetik catatan (min 3 karakter) lalu klik [Simpan Catatan]. | Data tersimpan normal, kotak catatan bergaris kiri biru muncul, tombol berubah menjadi **[Ubah Catatan]**. |
| **C. Logbook Perlu Revisi** | Pembimbing menetapkan status `perlu_revisi` (via API / proses review formal). | Validasi tetap wajib mengisi catatan (`Rule::requiredIf(...)`) sesuai aturan bisnis AC-09-4. |

---

## 3. Spesifikasi Teknis Perubahan (Step-by-Step Task)

### Step 1: Penyesuaian Backend Request (`app/Http/Requests/ReviewLogbookRequest.php`)

Gunakan conditional validation `Rule::requiredIf` atau penanganan nullable:
- Kolom `catatan_pembimbing` menjadi **`nullable`**.
- Wajib diisi **HANYA JIKA** statusnya adalah `perlu_revisi`.
- Jika diisi (tidak kosong): tetap terikat `min:3` dan `max:2000`.

**Lokasi Berkas**: [`app/Http/Requests/ReviewLogbookRequest.php`](file:///g:/MAGKOM/app/Http/Requests/ReviewLogbookRequest.php)

```php
namespace App\Http\Requests;

use App\Enums\StatusLogbookEnum;
use App\Models\Logbook;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;

class ReviewLogbookRequest extends FormRequest
{
    public function authorize(): bool
    {
        $logbook = $this->route('logbook');
        if ($logbook instanceof Logbook) {
            return $this->user()?->can('review', $logbook) ?? false;
        }

        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('catatan') && ! $this->has('catatan_pembimbing')) {
            $this->merge([
                'catatan_pembimbing' => $this->input('catatan'),
            ]);
        }
    }

    public function rules(): array
    {
        $isPerluRevisi = $this->input('status') === StatusLogbookEnum::PerluRevisi->value 
            || $this->input('status') === 'perlu_revisi';

        return [
            'catatan_pembimbing' => [
                Rule::requiredIf($isPerluRevisi),
                'nullable',
                'string',
                Rule::when(filled($this->input('catatan_pembimbing')), ['min:3', 'max:2000']),
            ],
            'status' => ['sometimes', 'nullable', new Enum(StatusLogbookEnum::class)],
        ];
    }

    public function messages(): array
    {
        return [
            'catatan_pembimbing.required' => 'Catatan pembimbing wajib diisi jika status logbook memerlukan revisi.',
            'catatan_pembimbing.min' => 'Catatan pembimbing minimal berisi 3 karakter jika diisi.',
            'catatan_pembimbing.max' => 'Catatan pembimbing maksimal berisi 2000 karakter.',
        ];
    }
}
```

---

### Step 2: Penyesuaian Controller (`app/Http/Controllers/Pembimbing/LogbookReviewController.php`)

Pastikan nilai string kosong (`""` atau hanya spasi) disimpan sebagai `NULL` di database:

**Lokasi Berkas**: [`app/Http/Controllers/Pembimbing/LogbookReviewController.php`](file:///g:/MAGKOM/app/Http/Controllers/Pembimbing/LogbookReviewController.php)

Pada method `review`:
```php
        Gate::authorize('review', $logbook);

        $rawCatatan = $request->validated('catatan_pembimbing');
        $catatan = filled($rawCatatan) ? trim((string) $rawCatatan) : null;
        $nilaiLama = $logbook->only(['status', 'catatan_pembimbing']);

        $updatePayload = [
            'catatan_pembimbing' => $catatan,
        ];

        if ($request->filled('status')) {
            $updatePayload['status'] = $request->validated('status');
        }

        $logbook->update($updatePayload);
```

---

### Step 3: Penyesuaian Frontend Blade (`resources/views/pembimbing/logbook/index.blade.php`)

1. **Hapus Atribut `required`**:
   Hapus atribut `required` dari `<textarea name="catatan_pembimbing" ...>`.
2. **Hapus Bintang Merah Wajib**:
   Hapus tanda `<span class="required-star" aria-hidden="true">*</span>`.
   Ubah label menjadi: `<span>Catatan &amp; Arahan Pembimbing (Opsional)</span>` atau `<span>Catatan &amp; Arahan Pembimbing</span>`.

**Lokasi Berkas**: [`resources/views/pembimbing/logbook/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/logbook/index.blade.php)

```blade
{{-- Material Floating Card Field ala Google Classroom --}}
<div class="logbook-note-field-filled {{ $hasInitialNote ? 'has-value' : '' }}">
    <label for="textarea-note-{{ $log->id }}" class="logbook-note-floating-label">
        <span>Catatan &amp; Arahan Pembimbing</span>
    </label>
    <textarea name="catatan_pembimbing" 
              id="textarea-note-{{ $log->id }}" 
              rows="3" 
              class="logbook-note-clean-textarea">{{ $log->catatan_pembimbing ?? '' }}</textarea>
</div>
```

---

### Step 4: Penyesuaian Styling CSS (`resources/css/pages/pembimbing/logbook-index.css`)

Pastikan styling label bersih tanpa sisa selector bintang merah yang tidak perlu.

---

### Step 5: Penyesuaian Unit & Feature Test

Tambahkan dan verifikasi test case:
1. `PembimbingFocusedMenteeLogbookViewTest`:
   - Memastikan textarea tidak memiliki atribut `required`.
   - Memastikan pembimbing dapat submit form review logbook dengan `catatan_pembimbing` kosong (`""`) dan tersimpan sebagai `null`.
2. Regression Test:
   - `PembimbingFullJobDeskTest`: status `perlu_revisi` tanpa catatan tetap ditolak validasi (AC-09-4).
   - `PembimbingPortalFeatureTest`: status `perlu_revisi` tanpa catatan tetap ditolak 422 validation error.

---

## 4. Matriks Pengujian & Quality Gates

| Komponen Uji | Perintah | Target Hasil |
|---|---|---|
| **Vite Asset** | `npm run build` | Sukses tanpa error kompilasi. |
| **Code Style** | `./vendor/bin/pint --test` | Lolos formatting PSR-12. |
| **Feature Test** | `php artisan test tests/Feature/PembimbingFocusedMenteeLogbookViewTest.php` | 100% Passed. |
| **Regression Suite** | `php artisan test --filter=Logbook` | 39 test passed (0 failed). |

---

## 5. Panduan Prompt Eksekusi untuk AI Ringan (Gemini 3.8 Flash)

```markdown
Tolong eksekusi perubahan logika catatan logbook sesuai rencana docs/plans/2026-11-06-nullable-catatan-logbook-senior-task.md:
1. Di app/Http/Requests/ReviewLogbookRequest.php, ubah rule catatan_pembimbing menjadi nullable, dan hanya required jika status == 'perlu_revisi'.
2. Di app/Http/Controllers/Pembimbing/LogbookReviewController.php, pastikan nilai string kosong disimpan sebagai null di database.
3. Di resources/views/pembimbing/logbook/index.blade.php, hapus atribut required pada textarea dan hilangkan tanda bintang merah pada label.
4. Jalankan `npm run build`, `./vendor/bin/pint --test`, dan `php artisan test --filter=Logbook`.
```

