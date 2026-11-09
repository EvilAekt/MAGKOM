/**
 * MagKom - SuperAdmin Detail Arsip Alumni Pagination
 * Pure Vanilla JavaScript ES Module for 10-Item Pagination Parity
 */

function setupListPagination(containerEl, paginationEl, itemSelector, perPage = 10) {
    if (!containerEl || !paginationEl) return;

    const items = Array.from(containerEl.querySelectorAll(itemSelector));
    const totalItems = items.length;
    if (totalItems === 0) {
        paginationEl.innerHTML = '';
        return;
    }

    let currentPage = 1;

    function render() {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalItems);

        // Toggle item visibility
        items.forEach((item, idx) => {
            if (idx >= startIndex && idx < endIndex) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });

        // Build pagination container
        const nav = document.createElement('nav');
        nav.className = 'magkom-pagination-nav';
        nav.setAttribute('role', 'navigation');
        nav.setAttribute('aria-label', 'Navigasi Halaman');

        // Summary
        const summary = document.createElement('div');
        summary.className = 'magkom-pagination-summary';
        summary.innerHTML = `<span>Menampilkan <strong>${totalItems > 0 ? startIndex + 1 : 0}</strong> sampai <strong>${endIndex}</strong> dari <strong>${totalItems}</strong> entri</span>`;
        nav.appendChild(summary);

        // Buttons list only if totalPages > 1
        if (totalPages > 1) {
            const list = document.createElement('ul');
            list.className = 'magkom-pagination-list';

            // Tombol Sebelumnya
            const prevLi = document.createElement('li');
            prevLi.className = 'magkom-page-item' + (currentPage === 1 ? ' disabled' : '');
            if (currentPage === 1) {
                prevLi.setAttribute('aria-disabled', 'true');
                prevLi.innerHTML = '<span class="magkom-page-link">&laquo; Sebelumnya</span>';
            } else {
                const prevA = document.createElement('a');
                prevA.className = 'magkom-page-link';
                prevA.href = '#';
                prevA.innerHTML = '&laquo; Sebelumnya';
                prevA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage--;
                    render();
                });
                prevLi.appendChild(prevA);
            }
            list.appendChild(prevLi);

            // Page numbers
            for (let p = 1; p <= totalPages; p++) {
                const pageLi = document.createElement('li');
                pageLi.className = 'magkom-page-item' + (p === currentPage ? ' active' : '');
                if (p === currentPage) {
                    pageLi.setAttribute('aria-current', 'page');
                    pageLi.innerHTML = `<span class="magkom-page-link">${p}</span>`;
                } else {
                    const pageA = document.createElement('a');
                    pageA.className = 'magkom-page-link';
                    pageA.href = '#';
                    pageA.textContent = p.toString();
                    pageA.addEventListener('click', (e) => {
                        e.preventDefault();
                        currentPage = p;
                        render();
                    });
                    pageLi.appendChild(pageA);
                }
                list.appendChild(pageLi);
            }

            // Tombol Berikutnya
            const nextLi = document.createElement('li');
            nextLi.className = 'magkom-page-item' + (currentPage === totalPages ? ' disabled' : '');
            if (currentPage === totalPages) {
                nextLi.setAttribute('aria-disabled', 'true');
                nextLi.innerHTML = '<span class="magkom-page-link">Berikutnya &raquo;</span>';
            } else {
                const nextA = document.createElement('a');
                nextA.className = 'magkom-page-link';
                nextA.href = '#';
                nextA.innerHTML = 'Berikutnya &raquo;';
                nextA.addEventListener('click', (e) => {
                    e.preventDefault();
                    currentPage++;
                    render();
                });
                nextLi.appendChild(nextA);
            }
            list.appendChild(nextLi);

            nav.appendChild(list);
        }

        paginationEl.innerHTML = '';
        paginationEl.appendChild(nav);
    }

    render();
}

export function initArsipDetailPagination() {
    // 1. Pagination Tugas
    const tugasListEl = document.getElementById('arsipTugasList');
    const tugasPaginationEl = document.getElementById('arsipTugasPagination');
    if (tugasListEl && tugasPaginationEl) {
        setupListPagination(tugasListEl, tugasPaginationEl, '.arsip-task-card', 10);
    }

    // 2. Pagination Logbook
    const logbookTableBody = document.getElementById('arsipLogbookTableBody');
    const logbookPaginationEl = document.getElementById('arsipLogbookPagination');
    if (logbookTableBody && logbookPaginationEl) {
        setupListPagination(logbookTableBody, logbookPaginationEl, '.arsip-logbook-row', 10);
    }
}

// Auto init on DOM ready
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initArsipDetailPagination);
    } else {
        initArsipDetailPagination();
    }
}

