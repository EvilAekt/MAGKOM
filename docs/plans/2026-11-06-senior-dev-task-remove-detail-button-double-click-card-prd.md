# [SENIOR DEV TASK] Penghapusan Tombol "Detail >" dan Transisi ke Interaksi Double-Click pada Card Tugas Mentee

**ID Task**: `TASK-TUGAS-DOUBLE-CLICK-CARD-01`  
**Target Eksekutor**: Junior Full-Stack Developer / AI Model Ringan (Gemini 3.8 Flash / Claude Haiku / Cursor)  
**Author / Reviewer**: Senior Lead Full-Stack Architect  
**Repositori**: `MAGKOM` (Portal Manajemen Magang Diskominfo Sleman)  
**Status**: Ready for Implementation (Plan / PRD)  
**Tanggal**: 06 November 2026  

---

## 1. Background & Analisis Kebutuhan Pengguna

Berdasarkan evaluasi antarmuka dan feedback pengguna pada halaman **Tugas & Progres Magang** (`/pembimbing/tugas`):

### 1.1 Masalah: Tombol "Detail >" Memadati Baris Tugas (Visual Clutter)
- **Kondisi Eksisting**:  
  Setiap baris tugas mentee (`.classroom-task-row`) memiliki elemen tombol eksplisit di ujung kanan:
  ```html
  <a href="..." class="btn-tugas-action">
      <span>Detail</span>
      <svg ...><polyline points="9 18 15 12 9 6"></polyline></svg>
  </a>
  ```
- **Keluhan Pengguna**:  
  Tombol `Detail >` tersebut memakan ruang horizontal yang signifikan, terutama ketika daftar tugas cukup banyak atau dibuka pada layar tablet/laptop 13-inch. Selain itu, baris terlihat ramai dengan badge status dan tombol bersisian.
- **Kebutuhan Pengguna**:  
  Tombol `Detail >` dihilangkan sepenuhnya. Sebagai gantinya, pengguna dapat langsung masuk ke halaman detail tugas cukup dengan **melakukan double-click (klik dua kali)** pada kartu baris tugas tersebut.

---

## 2. Prinsip Desain & UX Standar Senior Developer

1. **Clean & Minimalist Layout**:
   - Sisi kanan kartu baris tugas (`.task-row-right`) kini hanya berisi informasi tenggat waktu (`.task-row-due`) dan badge status pill (`.task-status-pill`). Tampilan menjadi lebih lapang, bersih, dan modern.
2. **Affordance & Visual Feedback yang Jelas**:
   - Pengguna harus langsung tahu bahwa kartu tersebut dapat diinteraksikan. Kursor diubah menjadi `cursor: pointer;`, dan saat di-hover kartu menampilkan efek elevasi halus (`transform: translateY(-1px)`, border biru Sleman `#01347C`, dan shadow lembut).
   - Ditambahkan atribut `title="Klik dua kali (double click) untuk membuka detail tugas"` dan `user-select: none;` agar double-click yang cepat tidak memicu highlight seleksi teks biru yang mengganggu.
3. **Aksesibilitas Ganda (WCAG 2.1 AA)**:
   - **Single-Click pada Judul**: Judul tugas tetap berupa tautan `<a href="..." class="task-row-title">`. Pengguna yang ingin klik biasa (single-click) pada judul atau klik kanan/tengah untuk membuka tab baru tetap bisa melakukannya tanpa hambatan.
   - **Keyboard Navigation**: Kartu diberi `tabindex="0"`, `role="button"`, dan didukung tombol `Enter` untuk membuka detail saat difokuskan via navigasi keyboard.
4. **Kepatuhan Aturan Repositori (`AGENTS.md`)**:
   - ❌ **DILARANG** menulis tag `<style>` atau `<script>` di file Blade.
   - ❌ **DILARANG** menggunakan inline event handler seperti `ondblclick="..."` di elemen HTML.
   - ✅ Seluruh styling dikelola di `resources/css/pages/pembimbing/tugas-index.css`.
   - ✅ Seluruh interaksi JavaScript dikelola di `resources/js/pages/pembimbing/tugas-index.js`.
   - ✅ Kompilasi aset menggunakan Vite (`npm run build`).

---

## 3. Rincian Perubahan Langkah demi Langkah (Step-by-Step Task)

### Step 1: Penyesuaian Markup Blade (`resources/views/pembimbing/tugas/index.blade.php`)

Lokasi file: [`resources/views/pembimbing/tugas/index.blade.php`](file:///g:/MAGKOM/resources/views/pembimbing/tugas/index.blade.php)  
Cari elemen `.classroom-task-row` di baris 241 s/d 285.

#### Kode Eksisting (Sebelum):
```blade
<div class="classroom-task-row">
    <div class="task-row-left">
        ...
        <div class="task-row-title-wrap">
            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="task-row-title">
                {{ $tugas->judul }}
            </a>
        </div>
    </div>

    <div class="task-row-right">
        <div class="task-row-due {{ $isTugasOverdue ? 'overdue' : '' }}">
            ...
        </div>

        <div class="task-status-pill">
            ...
        </div>

        <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="btn-tugas-action">
            <span>Detail</span>
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </a>
    </div>
</div>
```

#### Kode Target (Sesudah):
Hapus elemen `<a class="btn-tugas-action">...</a>`, dan sematkan atribut interaktif pada wrapper `.classroom-task-row`:
```blade
<div class="classroom-task-row" 
     data-task-url="{{ route('pembimbing.tugas.show', $tugas->id) }}"
     tabindex="0"
     role="button"
     aria-label="Tugas: {{ $tugas->judul }}. Klik dua kali untuk membuka detail."
     title="Klik dua kali (double click) untuk membuka detail tugas">
    <div class="task-row-left">
        <div class="task-row-icon {{ $isTugasSelesai ? 'icon-selesai' : ($hasTugasPending ? 'icon-pending' : 'icon-default') }}" aria-hidden="true">
            @if($isTugasSelesai)
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            @elseif($hasTugasPending)
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
            @else
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="9" y1="12" x2="15" y2="12"></line><line x1="9" y1="16" x2="13" y2="16"></line></svg>
            @endif
        </div>
        <div class="task-row-title-wrap">
            <a href="{{ route('pembimbing.tugas.show', $tugas->id) }}" class="task-row-title">
                {{ $tugas->judul }}
            </a>
        </div>
    </div>

    <div class="task-row-right">
        <div class="task-row-due {{ $isTugasOverdue ? 'overdue' : '' }}">
            @if($isTugasOverdue)
                <span>⚠️ Lewat: {{ $tugas->deadline?->translatedFormat('d M Y') ?? $tugas->deadline?->format('d M Y') }}</span>
            @elseif($tugas->deadline)
                <span>Tenggat: {{ $tugas->deadline->translatedFormat('d M Y') ?? $tugas->deadline->format('d M Y') }}</span>
            @else
                <span>Tanpa tenggat</span>
            @endif
        </div>

        <div class="task-status-pill">
            @if($isTugasSelesai)
                <x-gentelella.badge variant="success">Selesai</x-gentelella.badge>
            @elseif($hasTugasPending)
                <x-gentelella.badge variant="warning">Review</x-gentelella.badge>
            @else
                <x-gentelella.badge variant="info">Ditugaskan</x-gentelella.badge>
            @endif
        </div>
    </div>
</div>
```

---

### Step 2: Implementasi Interaksi JavaScript (`resources/js/pages/pembimbing/tugas-index.js`)

Lokasi file: [`resources/js/pages/pembimbing/tugas-index.js`](file:///g:/MAGKOM/resources/js/pages/pembimbing/tugas-index.js)  
Tambahkan event listener global dengan pola event delegation (sehingga otomatis aman untuk elemen statis maupun dinamis):

```javascript
    // 7. Event Delegation: Double Click & Keyboard Access pada Card Baris Tugas
    if (!window._pembimbingTaskRowDblClickInitialized) {
        window._pembimbingTaskRowDblClickInitialized = true;

        // A. Double Click untuk membuka detail tugas
        document.addEventListener('dblclick', function (e) {
            const taskRow = e.target.closest('.classroom-task-row[data-task-url]');
            if (!taskRow) return;

            // Jika double-click dilakukan pada elemen interactive native (misal link judul atau tombol), biarkan native handler
            if (e.target.closest('a, button')) return;

            const taskUrl = taskRow.getAttribute('data-task-url');
            if (taskUrl) {
                window.location.href = taskUrl;
            }
        });

        // B. Aksesibilitas Keyboard: Tekan tombol Enter pada card yang difokuskan
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') {
                const taskRow = e.target.closest('.classroom-task-row[data-task-url]');
                // Pastikan target fokus langsung adalah baris card (bukan anchor link judul di dalamnya)
                if (taskRow && e.target === taskRow) {
                    e.preventDefault();
                    const taskUrl = taskRow.getAttribute('data-task-url');
                    if (taskUrl) {
                        window.location.href = taskUrl;
                    }
                }
            }
        });
    }
```

---

### Step 3: Penyempurnaan CSS (`resources/css/pages/pembimbing/tugas-index.css`)

Lokasi file: [`resources/css/pages/pembimbing/tugas-index.css`](file:///g:/MAGKOM/resources/css/pages/pembimbing/tugas-index.css)  
Terapkan styling visual affordance, cursor pointer, dan pencegahan seleksi teks saat double-click:

```css
.classroom-task-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    padding: 12px 18px;
    background: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    cursor: pointer;
    user-select: none; /* Mencegah seleksi teks yang tidak disengaja saat double click */
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.classroom-task-row:hover {
    background: #F8FAFC;
    border-color: #01347C;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(1, 52, 124, 0.08);
}

.classroom-task-row:focus-visible {
    outline: none;
    border-color: #01347C;
    box-shadow: 0 0 0 3px rgba(1, 52, 124, 0.2);
}

.classroom-task-row:active {
    transform: translateY(0);
}

/* Responsif: Menjaga jarak tombol dan badge di mobile */
@media (max-width: 640px) {
    .classroom-task-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
        padding: 12px 14px;
    }

    .task-row-right {
        width: 100%;
        justify-content: space-between;
    }
}
```

---

## 4. Rencana Pengujian (Test Plan & Assertions)

Lokasi file: [`tests/Feature/PembimbingFocusedMenteeTugasViewTest.php`](file:///g:/MAGKOM/tests/Feature/PembimbingFocusedMenteeTugasViewTest.php)

Tambahkan method pengujian khusus untuk memverifikasi penghapusan tombol Detail dan ketersediaan atribut double-click:

```php
public function test_baris_tugas_tidak_lagi_menampilkan_tombol_detail_dan_memiliki_atribut_double_click(): void
{
    // Akses halaman tugas dengan parameter masuk mentee penempatan1
    $response = $this->actingAs($this->pembimbing)->get(route('pembimbing.tugas.index', [
        'masuk' => $this->penempatan1->id,
    ]));

    $response->assertOk();

    // 1. Tombol Detail tidak ada lagi di baris kartu tugas
    $response->assertDontSee('<span class="btn-tugas-action">Detail</span>', false);
    $response->assertDontSee('class="btn-tugas-action"', false);

    // 2. Baris kartu tugas memiliki atribut data-task-url untuk interaksi double-click
    $response->assertSee('data-task-url="'.route('pembimbing.tugas.show', $this->tugas1->id).'"', false);
    $response->assertSee('title="Klik dua kali (double click) untuk membuka detail tugas"', false);
    $response->assertSee('role="button"', false);
    $response->assertSee('tabindex="0"', false);
}
```

---

## 5. Checklist Verifikasi & Quality Gates

| No | Pemeriksaan | Perintah / Uji | Target Hasil |
| :---: | :--- | :--- | :--- |
| 1 | **Blade Check** | Visual code review | Tidak ada tag `<style>` atau `<script>` di Blade |
| 2 | **Build Asset** | `npm run build` | Berhasil tanpa error |
| 3 | **Code Style** | `./vendor/bin/pint --test` | 100% Passed |
| 4 | **Static Analysis** | `./vendor/bin/phpstan analyse` | No errors |
| 5 | **Feature Test** | `php artisan test tests/Feature/PembimbingFocusedMenteeTugasViewTest.php` | 100% Green (Passing) |
| 6 | **Browser UX Check** | Double click kartu tugas | Halaman berpindah ke `pembimbing/tugas/{id}` |
| 7 | **Accessibility Check** | Keyboard `Tab` + `Enter` | Halaman berpindah ke `pembimbing/tugas/{id}` |

---

## 6. Pertanyaan Konfirmasi Sebelum Eksekusi

Mohon konfirmasi kesesuaian rencana di atas:
1. Apakah tombol Detail di sub-view **Tabel Administratif** (`#mainTugasTable`) tetap dibiarkan ada sebagai tombol di kolom aksi tabel, ataukah Anda ingin kolom aksi di tabel administratif juga disesuaikan?
2. Apakah rencana implementasi ini sudah disetujui untuk dieksekusi?

