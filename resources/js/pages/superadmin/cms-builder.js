/**
 * MAGKOM CMS Super Admin - Visual Form Builders & Theming Interactions
 * Menangani dynamic repeaters untuk Navigasi, FAQ, Footer, Kebijakan, dan Color Pickers
 * Sesuai aturan PSR-12, ESLint, dan arsitektur tanpa inline script di Blade.
 * Sistem Animasi Universal (Scroll-Craft + GSAP Paritas Pembimbing)
 */
import { initSuperAdminPageMotion } from '../../services/superadmin-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initSuperAdminPageMotion();
    initRepeaters();
    initColorPickers();
});

function initRepeaters() {
    document.addEventListener('click', (event) => {
        const target = event.target.closest('[data-action]');
        if (!target) return;

        const action = target.getAttribute('data-action');

        // 1. Navigasi Navbar Repeater
        if (action === 'add-nav-item') {
            event.preventDefault();
            addNavItem();
        }

        // 2. Footer Social Media Repeater
        if (action === 'add-medsos-item') {
            event.preventDefault();
            addMedsosItem();
        }

        // 3. Footer Tautan Terkait Repeater
        if (action === 'add-tautan-item') {
            event.preventDefault();
            addTautanItem();
        }

        // 4. Footer Tautan Akun Repeater
        if (action === 'add-akun-item') {
            event.preventDefault();
            addAkunItem();
        }

        // 5. FAQ Kategori & Q/A Items
        if (action === 'add-faq-category') {
            event.preventDefault();
            addFaqCategory();
        }

        if (action === 'add-faq-question') {
            event.preventDefault();
            const categoryCard = target.closest('.faq-category-card');
            if (categoryCard) {
                addFaqQuestion(categoryCard);
            }
        }

        if (action === 'remove-faq-category') {
            event.preventDefault();
            const categoryCard = target.closest('.faq-category-card');
            if (categoryCard && confirm('Hapus seluruh kategori FAQ ini beserta pertanyaannya?')) {
                categoryCard.remove();
                reindexFaq();
            }
        }

        if (action === 'remove-faq-item') {
            event.preventDefault();
            const item = target.closest('.faq-question-item');
            if (item) {
                const categoryCard = target.closest('.faq-category-card');
                item.remove();
                if (categoryCard) reindexFaqQuestions(categoryCard);
            }
        }

        // 6. Kebijakan Privasi Section & Bullet Items
        if (action === 'add-kebijakan-section') {
            event.preventDefault();
            addKebijakanSection();
        }

        if (action === 'remove-kebijakan-section') {
            event.preventDefault();
            const sectionCard = target.closest('.kebijakan-section-card');
            if (sectionCard && confirm('Hapus pasal kebijakan privasi ini?')) {
                sectionCard.remove();
                reindexKebijakan();
            }
        }

        if (action === 'add-kebijakan-item') {
            event.preventDefault();
            const sectionCard = target.closest('.kebijakan-section-card');
            if (sectionCard) {
                addKebijakanBulletItem(sectionCard);
            }
        }

        if (action === 'remove-kebijakan-item') {
            event.preventDefault();
            const item = target.closest('.kebijakan-bullet-item');
            if (item) item.remove();
        }

        // Generic Remove Row (untuk tabel datar)
        if (action === 'remove-row') {
            event.preventDefault();
            const row = target.closest('tr') || target.closest('.repeater-row');
            if (row) {
                row.remove();
            }
        }
    });
}

/**
 * 1. Menambahkan baris menu navigasi navbar
 */
function addNavItem() {
    const tbody = document.getElementById('nav-items-tbody');
    if (!tbody) return;

    const index = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.className = 'repeater-row';
    tr.innerHTML = `
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="nav_items[${index}][label]" placeholder="Contoh: Galeri Magang" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="nav_items[${index}][url]" placeholder="Contoh: #galeri atau /galeri" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem; text-align: center;">
            <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                Hapus
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

/**
 * 2. Menambahkan baris media sosial
 */
function addMedsosItem() {
    const tbody = document.getElementById('medsos-items-tbody');
    if (!tbody) return;

    const index = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.className = 'repeater-row';
    tr.innerHTML = `
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="footer_medsos[${index}][platform]" placeholder="Contoh: YouTube" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="footer_medsos[${index}][label]" placeholder="Contoh: Diskominfo Sleman TV" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem;">
            <input type="url" name="footer_medsos[${index}][url]" placeholder="https://youtube.com/..." required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem; text-align: center;">
            <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                Hapus
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

/**
 * 3. Menambahkan baris tautan terkait
 */
function addTautanItem() {
    const tbody = document.getElementById('tautan-items-tbody');
    if (!tbody) return;

    const index = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.className = 'repeater-row';
    tr.innerHTML = `
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="footer_tautan[${index}][label]" placeholder="Contoh: Portal Satu Data" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="footer_tautan[${index}][url]" placeholder="https://..." required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem; text-align: center;">
            <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                Hapus
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

/**
 * 4. Menambahkan baris tautan akun
 */
function addAkunItem() {
    const tbody = document.getElementById('akun-items-tbody');
    if (!tbody) return;

    const index = tbody.querySelectorAll('tr').length;
    const tr = document.createElement('tr');
    tr.className = 'repeater-row';
    tr.innerHTML = `
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="footer_akun[${index}][label]" placeholder="Contoh: Masuk Peserta" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem;">
            <input type="text" name="footer_akun[${index}][url]" placeholder="/login" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem;">
        </td>
        <td style="padding: 0.75rem 0.5rem; text-align: center;">
            <button type="button" data-action="remove-row" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                Hapus
            </button>
        </td>
    `;
    tbody.appendChild(tr);
}

/**
 * 5. Menambahkan kategori FAQ baru
 */
function addFaqCategory() {
    const container = document.getElementById('faq-categories-container');
    if (!container) return;

    const catIndex = container.querySelectorAll('.faq-category-card').length;
    const card = document.createElement('div');
    card.className = 'faq-category-card';
    card.style.cssText = 'background: #FFFFFF; border: 1.5px solid var(--neutral-300, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 1rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
    card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 250px;">
                <div style="width: 140px;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">ID Kategori (Slug)</label>
                    <input type="text" name="faq_categories[${catIndex}][id]" placeholder="contoh: umum" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.8125rem;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Nama Kategori</label>
                    <input type="text" name="faq_categories[${catIndex}][nama]" placeholder="Contoh: Pertanyaan Umum Magang" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; font-weight: 600;">
                </div>
            </div>
            <button type="button" data-action="remove-faq-category" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; padding: 0.4rem 0.85rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                Hapus Kategori
            </button>
        </div>

        <div class="faq-questions-container" style="display: flex; flex-direction: column; gap: 0.75rem;">
            <!-- Questions appended here -->
        </div>

        <div>
            <button type="button" data-action="add-faq-question" style="background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 0.45rem 1rem; font-size: 0.8125rem; font-weight: 700; cursor: pointer; display: inline-flex; align-items: center; gap: 0.35rem;">
                + Tambah Pertanyaan di Kategori Ini
            </button>
        </div>
    `;
    container.appendChild(card);
    addFaqQuestion(card);
}

/**
 * Menambahkan pertanyaan dan jawaban di dalam kategori FAQ
 */
function addFaqQuestion(categoryCard) {
    const questionsContainer = categoryCard.querySelector('.faq-questions-container');
    if (!questionsContainer) return;

    const allCategories = Array.from(document.querySelectorAll('.faq-category-card'));
    const catIndex = allCategories.indexOf(categoryCard);
    const itemIndex = questionsContainer.querySelectorAll('.faq-question-item').length;

    const item = document.createElement('div');
    item.className = 'faq-question-item';
    item.style.cssText = 'background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 8px; padding: 0.875rem; display: flex; flex-direction: column; gap: 0.5rem;';
    item.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
            <input type="text" name="faq_categories[${catIndex}][items][${itemIndex}][q]" placeholder="Pertanyaan: Contoh, Apakah pendaftaran berbayar?" required style="width: 100%; height: 38px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; font-weight: 600;">
            <button type="button" data-action="remove-faq-item" style="background: #FEE2E2; color: #B91C1C; border: 1px solid #FCA5A5; border-radius: 6px; height: 36px; padding: 0 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer; flex-shrink: 0;">
                Hapus Q&A
            </button>
        </div>
        <textarea name="faq_categories[${catIndex}][items][${itemIndex}][a]" rows="2" placeholder="Jawaban: Contoh, Tidak. Seluruh program magang Diskominfo Sleman 100% gratis..." required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.65rem; font-size: 0.8125rem; font-family: inherit; line-height: 1.4;"></textarea>
    `;
    questionsContainer.appendChild(item);
}

function reindexFaq() {
    const cards = document.querySelectorAll('.faq-category-card');
    cards.forEach((card, catIndex) => {
        const idInput = card.querySelector('input[name*="[id]"]');
        const namaInput = card.querySelector('input[name*="[nama]"]');
        if (idInput) idInput.name = `faq_categories[${catIndex}][id]`;
        if (namaInput) namaInput.name = `faq_categories[${catIndex}][nama]`;
        reindexFaqQuestions(card, catIndex);
    });
}

function reindexFaqQuestions(card, catIndex = null) {
    if (catIndex === null) {
        const allCards = Array.from(document.querySelectorAll('.faq-category-card'));
        catIndex = allCards.indexOf(card);
    }
    const questions = card.querySelectorAll('.faq-question-item');
    questions.forEach((qItem, itemIndex) => {
        const qInput = qItem.querySelector('input[name*="[q]"]');
        const aInput = qItem.querySelector('textarea[name*="[a]"]');
        if (qInput) qInput.name = `faq_categories[${catIndex}][items][${itemIndex}][q]`;
        if (aInput) aInput.name = `faq_categories[${catIndex}][items][${itemIndex}][a]`;
    });
}

/**
 * 6. Menambahkan pasal baru pada Kebijakan Privasi
 */
function addKebijakanSection() {
    const container = document.getElementById('kebijakan-sections-container');
    if (!container) return;

    const secIndex = container.querySelectorAll('.kebijakan-section-card').length;
    const card = document.createElement('div');
    card.className = 'kebijakan-section-card';
    card.style.cssText = 'background: #FFFFFF; border: 1.5px solid var(--neutral-300, #E2E8F0); border-radius: 10px; padding: 1.25rem; margin-bottom: 1.25rem; display: flex; flex-direction: column; gap: 0.875rem; box-shadow: 0 1px 3px rgba(0,0,0,0.05);';
    card.innerHTML = `
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid var(--neutral-200); padding-bottom: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
            <div style="display: flex; gap: 0.75rem; flex: 1; min-width: 250px;">
                <div style="width: 80px;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Nomor</label>
                    <input type="number" name="kebijakan_sections[${secIndex}][nomor]" value="${secIndex + 1}" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.5rem; font-size: 0.875rem; text-align: center; font-weight: 700;">
                </div>
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-600); margin-bottom: 0.25rem;">Judul Pasal</label>
                    <input type="text" name="kebijakan_sections[${secIndex}][judul]" placeholder="Contoh: Hak Pemilik Data Pribadi" required style="width: 100%; height: 36px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.75rem; font-size: 0.875rem; font-weight: 700;">
                </div>
            </div>
            <button type="button" data-action="remove-kebijakan-section" style="background: #FEF2F2; color: #DC2626; border: 1px solid #FCA5A5; border-radius: 6px; padding: 0.4rem 0.85rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                Hapus Bagian
            </button>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.25rem;">Teks Isi / Paragraf Konten</label>
            <textarea name="kebijakan_sections[${secIndex}][konten]" rows="3" placeholder="Tuliskan penjelasan pasal..." required style="width: 100%; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0.75rem; font-size: 0.8125rem; font-family: inherit; line-height: 1.45;"></textarea>
        </div>

        <div>
            <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--neutral-700); margin-bottom: 0.35rem;">Daftar Butir Poin / Sub-items (Opsional)</label>
            <div class="kebijakan-bullets-container" style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;">
                <!-- Bullets appended here -->
            </div>
            <button type="button" data-action="add-kebijakan-item" style="background: #F1F5F9; color: #334155; border: 1px solid #CBD5E1; border-radius: 6px; padding: 0.35rem 0.75rem; font-size: 0.75rem; font-weight: 700; cursor: pointer;">
                + Tambah Butir Poin
            </button>
        </div>
    `;
    container.appendChild(card);
}

function addKebijakanBulletItem(sectionCard) {
    const bulletsContainer = sectionCard.querySelector('.kebijakan-bullets-container');
    if (!bulletsContainer) return;

    const allCards = Array.from(document.querySelectorAll('.kebijakan-section-card'));
    const secIndex = allCards.indexOf(sectionCard);

    const row = document.createElement('div');
    row.className = 'kebijakan-bullet-item';
    row.style.cssText = 'display: flex; gap: 0.5rem; align-items: center;';
    row.innerHTML = `
        <span style="color: var(--neutral-500); font-weight: 700;">&bull;</span>
        <input type="text" name="kebijakan_sections[${secIndex}][items][]" placeholder="Teks butir poin..." style="flex: 1; height: 34px; border: 1px solid var(--neutral-400); border-radius: 6px; padding: 0 0.65rem; font-size: 0.8125rem;">
        <button type="button" data-action="remove-kebijakan-item" style="background: #FEE2E2; color: #DC2626; border: none; border-radius: 6px; width: 34px; height: 34px; cursor: pointer; font-weight: 700;">&times;</button>
    `;
    bulletsContainer.appendChild(row);
}

function reindexKebijakan() {
    const cards = document.querySelectorAll('.kebijakan-section-card');
    cards.forEach((card, secIndex) => {
        const noInput = card.querySelector('input[name*="[nomor]"]');
        const judulInput = card.querySelector('input[name*="[judul]"]');
        const kontenInput = card.querySelector('textarea[name*="[konten]"]');
        if (noInput) noInput.name = `kebijakan_sections[${secIndex}][nomor]`;
        if (judulInput) judulInput.name = `kebijakan_sections[${secIndex}][judul]`;
        if (kontenInput) kontenInput.name = `kebijakan_sections[${secIndex}][konten]`;

        const bullets = card.querySelectorAll('input[name*="[items]"]');
        bullets.forEach((b) => {
            b.name = `kebijakan_sections[${secIndex}][items][]`;
        });
    });
}

/**
 * 7. Sinkronisasi Color Picker dan input teks HEX
 */
function initColorPickers() {
    const colorPairs = document.querySelectorAll('[data-color-pair]');
    colorPairs.forEach((pair) => {
        const picker = pair.querySelector('input[type="color"]');
        const textInput = pair.querySelector('input[type="text"]');

        if (!picker || !textInput) return;

        picker.addEventListener('input', () => {
            textInput.value = picker.value.toUpperCase();
        });

        textInput.addEventListener('input', () => {
            const hex = textInput.value.trim();
            if (/^#[0-9A-F]{6}$/i.test(hex)) {
                picker.value = hex;
            }
        });
    });
}

