# SENIOR DEVELOPER TASK & TECHNICAL PRD
## Redesain Komponen Tautan Referensi pada Halaman Edit Tugas Mentee

- **Tingkat Kesulitan**: Medium (UI/UX Engineering, Frontend Component Styling, & DOM Templating)
- **Target Implementer**: Junior Developer / AI Model (Gemini 3.8 Flash / Claude Haiku / Cursor)
- **Reviewer**: Lead Senior Full-Stack Developer & UI/UX Specialist
- **Target Halaman**: Portal Pembimbing — `Edit Tugas Mentee` (`/pembimbing/tugas/{id}/edit`)
- **File Target Utama**:
  1. `resources/js/pages/pembimbing/tugas-edit.js` (Perbaikan template rendering kartu tautan & sanitasi XSS)
  2. `resources/css/pages/pembimbing/tugas-edit.css` (Styling kartu tautan, hover state, responsive, & Dark Mode)
- **Referensi Desain**: Standar Google Classroom Workspace di `resources/css/pages/pembimbing/tugas-create.css`
- **Tangkapan Layar Masalah**: `media_1793977734348_863f2889.png` & `media_1793977745574_479b2dd9.png`

---

## 1. Problem Statement & Root Cause Analysis (RCA)

### 1.1 Masalah Tampilan Saat Ini (User Pain Points)
Berdasarkan tangkapan layar pengguna:
1. **Tombol Hapus Rusak & Mengganggu**: Tombol hapus tautan muncul sebagai elemen HTML bawaan browser berbentuk kotak kecil abu-abu tipis bertuliskan huruf `✕`, posisinya jatuh ke bawah icon rantai tanpa alignment yang wajar.
2. **Tidak Memiliki Card Pembungkus**: URL tautan (`https://magkom.slemankab.go.id/tasks/spec`) dan teks `Tautan Referensi` tercetak telanjang di bawah tombol lampiran tanpa background, tanpa padding, tanpa border pemisah, dan tanpa layout flexbox.
3. **Typography & Hierarki Buruk**: URL biru cerah menempel langsung dengan teks label tanpa spasi atau kontainer pembeda.
4. **Resiko Layout Rusak**: Tidak ada pembatasan teks (`text-overflow: ellipsis`), sehingga URL yang panjang akan meluber ke luar batas container.

### 1.2 Root Cause Analysis (RCA) di Tingkat Kode
Mari kita telusuri file `resources/js/pages/pembimbing/tugas-edit.js` pada baris 334–351:
```javascript
function renderAttachedLink(url) {
    if (!attachedContainer) return;
    attachedContainer.innerHTML = `
        <div class="tugas-attached-item">
            <div class="tugas-attached-left">
                <div class="tugas-attached-icon">
                    <svg ...></svg>
                </div>
                <div class="tugas-attached-text">
                    <a href="${url}" target="_blank" ...>${url}</a>
                    <span class="tugas-attached-sub">Tautan Referensi</span>
                </div>
            </div>
            <button type="button" class="tugas-attached-remove" id="btnRemoveAttachedLink" ...>
                <svg ...></svg>
            </button>
        </div>
    `;
}
```

**Temuan Kritis**:
1. Class `.tugas-attached-item`, `.tugas-attached-icon`, `.tugas-attached-text`, dan `.tugas-attached-remove` **TIDAK TERDEFINISI** di dalam file stylesheet `resources/css/pages/pembimbing/tugas-edit.css` maupun `tugas-create.css`.
2. Browser terpaksa merender elemen `<button>` menggunakan *User-Agent Stylesheet* bawaan sistem operasi (kotak bevel 3D default Windows).
3. Di halaman sejenis (`Buat Tugas Baru` / `tugas-create`), komponen ini sudah dibuat dengan nama class:
   - `.tugas-attached-card`
   - `.tugas-file-badge.badge-link`
   - `.tugas-attached-info`
   - `.tugas-attached-name`
   - `.tugas-attached-meta`
   - `.tugas-btn-remove-attachment`
4. Di `tugas-edit.js`, variabel `url` diinterpolasikan langsung ke `innerHTML` tanpa fungsi sanitasi `escapeHtml()`, yang berpotensi melanggar standar keamanan OWASP Top 10 (A03: Injection).

---

## 2. Arsitektur Komponen Visual (Before vs After)

```
[SAAT INI: BROKEN, KASAR, & TIDAK NYAMAN]
🔗  https://magkom.slemankab.go.id/tasks/spec Tautan Referensi
[x]  <-- Tombol kotak kaku bawaan browser di bawah icon!


[TARGET REDESAIN: GOOGLE CLASSROOM ATTACHMENT CARD]
┌────────────────────────────────────────────────────────────────────────┐
│  ┌──────┐                                                              │
│  │  🔗  │  magkom.slemankab.go.id/tasks/spec  ↗                       │
│  └──────┘  Tautan Referensi Eksternal                          [ ✕ ]   │
└────────────────────────────────────────────────────────────────────────┘
 • Background: #F8FAFC (Dark Mode: #1E293B)
 • Border: 1px solid #E2E8F0 (Rounded 10px)
 • Icon Badge: 40x40px Squircle Biru Muda (#E0F2FE) dengan Icon Biru (#0284C7)
 • Nama Tautan: Font semibold 14px dengan hover underline + truncate ellipsis
 • Tombol Hapus: Lingkaran 32x32px transparan, hover bulat merah lembut (#FEE2E2)
```

---

## 3. Spesifikasi Teknis & Langkah Implementasi

### 3.1 Perbaikan JavaScript (`resources/js/pages/pembimbing/tugas-edit.js`)

Perbarui fungsi `renderAttachedLink(url)` agar:
1. Menggunakan class standar `.tugas-attached-card`, `.tugas-file-badge.badge-link`, `.tugas-attached-info`, `.tugas-attached-name`, `.tugas-attached-meta`, dan `.tugas-btn-remove-attachment`.
2. Menambahkan fungsi helper sanitasi `escapeHtml(str)`.
3. Menampilkan icon eksternal link kecil (`↗`) agar pembimbing tahu tautan dapat dibuka langsung di tab baru.
4. Menambahkan ekstraksi nama domain hostname untuk teks metadata yang lebih bersih.
5. Memperbaiki juga fungsi `renderAttachedFile(name, sizeText)` agar file baru yang diunggah konsisten menggunakan format card yang sama.

#### Cuplikan Kode Rekomendasi di `tugas-edit.js`:

```javascript
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function renderAttachedLink(url) {
    if (!attachedContainer) return;
    
    let displayDomain = 'Tautan Referensi Eksternal';
    try {
        const parsed = new URL(url);
        displayDomain = parsed.hostname + ' • Tautan Referensi';
    } catch (e) {
        // Fallback jika bukan URL standar
    }

    attachedContainer.innerHTML = `
        <div class="tugas-attached-card" id="cardLinkAttachment">
            <div class="tugas-attached-left">
                <div class="tugas-file-badge badge-link" aria-hidden="true">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/>
                    </svg>
                </div>
                <div class="tugas-attached-info">
                    <a href="${escapeHtml(url)}" target="_blank" rel="noopener noreferrer" class="tugas-attached-name tugas-attached-link-name" title="${escapeHtml(url)}">
                        <span>${escapeHtml(url)}</span>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" class="tugas-external-icon">
                            <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path>
                            <polyline points="15 3 21 3 21 9"></polyline>
                            <line x1="10" y1="14" x2="21" y2="3"></line>
                        </svg>
                    </a>
                    <p class="tugas-attached-meta">${displayDomain}</p>
                </div>
            </div>
            <button type="button" class="tugas-btn-remove-attachment" id="btnRemoveAttachedLink" title="Hapus tautan ini" aria-label="Hapus tautan ini">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>
    `;

    const btnRemove = document.getElementById('btnRemoveAttachedLink');
    if (btnRemove) {
        btnRemove.addEventListener('click', () => {
            realInput.value = '';
            attachedContainer.innerHTML = '';
        });
    }
}
```

---

### 3.2 Penambahan Styling CSS (`resources/css/pages/pembimbing/tugas-edit.css`)

Tambahkan aturan CSS khusus pada `resources/css/pages/pembimbing/tugas-edit.css` untuk memastikan tampilan kartu terpasang rapi, tombol hapus melayang di kanan, dan mendukung Dark Mode:

```css
/* =========================================================================
   Lampiran Cards Styling (Tautan & File Baru)
   ========================================================================= */
.tugas-attached-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 14px;
}

.tugas-attached-card {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #E2E8F0;
    background: #F8FAFC;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
    transition: all 0.2s ease;
}

.tugas-attached-card:hover {
    border-color: #CBD5E1;
    box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
}

.tugas-attached-left {
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 0;
    flex: 1;
}

.tugas-file-badge {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    background: #DBEAFE;
    color: #1D4ED8;
    display: grid;
    place-items: center;
    flex-shrink: 0;
}

.tugas-file-badge.badge-link {
    background: #E0F2FE;
    color: #0284C7;
}

.tugas-attached-info {
    min-width: 0;
    flex: 1;
}

.tugas-attached-link-name {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    max-width: 100%;
    font-size: 0.875rem;
    font-weight: 600;
    color: #0641CC;
    text-decoration: none;
    line-height: 1.35;
}

.tugas-attached-link-name span {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 460px;
}

.tugas-attached-link-name:hover span {
    text-decoration: underline;
    color: #0534A8;
}

.tugas-external-icon {
    flex-shrink: 0;
    color: #0641CC;
}

.tugas-attached-meta {
    margin: 2px 0 0 0;
    font-size: 0.775rem;
    color: #64748B;
}

.tugas-btn-remove-attachment {
    border: none;
    background: transparent;
    color: #94A3B8;
    cursor: pointer;
    width: 34px;
    height: 34px;
    border-radius: 50%;
    display: grid;
    place-items: center;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

.tugas-btn-remove-attachment:hover {
    color: #DC2626;
    background: #FEE2E2;
    transform: scale(1.05);
}

/* Dark Mode Support */
[data-theme="dark"] .tugas-attached-card {
    background: #1E293B;
    border-color: #334155;
}

[data-theme="dark"] .tugas-attached-card:hover {
    border-color: #475569;
}

[data-theme="dark"] .tugas-file-badge.badge-link {
    background: rgba(2, 132, 199, 0.18);
    color: #38BDF8;
}

[data-theme="dark"] .tugas-attached-link-name {
    color: #60A5FA;
}

[data-theme="dark"] .tugas-attached-link-name:hover span {
    color: #93C5FD;
}

[data-theme="dark"] .tugas-external-icon {
    color: #60A5FA;
}

[data-theme="dark"] .tugas-attached-meta {
    color: #94A3B8;
}

[data-theme="dark"] .tugas-btn-remove-attachment:hover {
    color: #F87171;
    background: rgba(239, 68, 68, 0.18);
}
```

---

## 4. Batasan & Guardrails Repositori

- [ ] **DILARANG** menulis CSS di tag `<style>` Blade atau script di tag `<script>` Blade.
- [ ] **DILARANG** mengubah kontrak form input `realLinkInput` (`name="link_referensi"`). Nilai link wajib tersimpan utuh saat form di-submit.
- [ ] **NO EXTERNAL DEPENDENCIES**: Gunakan CSS native dan Vanilla JavaScript yang sudah ada.
- [ ] **SANITIZE INPUT**: Sanitasi teks link menggunakan `escapeHtml()` sebelum dirender ke DOM.

---

## 5. Rencana Verifikasi & Quality Gates

Jalankan rangkaian tes berikut setelah kode diubah:

```bash
# 1. Jalankan Feature Test Halaman Edit Tugas
php artisan test --filter=PembimbingFocusedMenteeTugasViewTest

# 2. Jalankan Feature Test Terkait Jobdesk Pembimbing
php artisan test --filter=PembimbingFullJobDeskTest

# 3. Kompilasi Aset Frontend (Vite)
npm run build

# 4. Code Style & Static Analysis
./vendor/bin/pint --test
./vendor/bin/phpstan analyse --memory-limit=1G
```

---

## 6. Checklist Definition of Done (DoD)

- [ ] Tombol hapus kotak kaku HTML telah hilang dan digantikan oleh tombol silang lingkaran modern dengan hover state merah lembut.
- [ ] Tautan referensi tampil dalam bentuk kartu horizontal modern (`.tugas-attached-card`) dengan squircle badge icon link.
- [ ] URL panjang terpotong rapi dengan ellipsis tanpa merusak grid halaman.
- [ ] Tautan dapat diklik untuk dibuka di tab baru dengan ikon indikator eksternal.
- [ ] Tombol hapus tautan berfungsi menghapus preview dan mengosongkan nilai input tersembunyi `realLinkInput`.
- [ ] Tampilan konsisten pada mode Light dan Dark.
- [ ] Kompilasi `npm run build` sukses tanpa warning / error.
- [ ] Seluruh automated test berjalan 100% hijau (*pass*).

