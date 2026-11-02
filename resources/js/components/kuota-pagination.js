/**
 * MagKom Dashboard - Interactive Kuota Pagination (Image 5 Parity)
 * Pure Vanilla JavaScript ES Module for CSP and Performance
 */

export function initKuotaPagination() {
    const wrapper = document.getElementById('kuotaPaginationWrapper');
    const goalList = document.getElementById('kuotaGoalList');
    if (!wrapper || !goalList) return;

    const items = Array.from(goalList.querySelectorAll('.kuota-goal-item'));
    if (!items.length) {
        wrapper.style.display = 'none';
        return;
    }

    let currentPage = 1;
    let perPage = 10;
    const totalItems = items.length;

    const startRowEl = document.getElementById('kuotaStartRow');
    const endRowEl = document.getElementById('kuotaEndRow');
    const totalRowsEl = document.getElementById('kuotaTotalRows');
    const currentPageEl = document.getElementById('kuotaCurrentPage');
    const totalPagesEl = document.getElementById('kuotaTotalPages');
    const pageNumbersContainer = document.getElementById('kuotaPageNumbers');
    const btnPrev = document.getElementById('kuotaBtnPrev');
    const btnNext = document.getElementById('kuotaBtnNext');
    const perPagePills = wrapper.querySelectorAll('.kuota-per-page-pill');

    function render() {
        const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
        if (currentPage > totalPages) currentPage = totalPages;
        if (currentPage < 1) currentPage = 1;

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalItems);

        // Update item visibility
        items.forEach((item, index) => {
            if (index >= startIndex && index < endIndex) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });

        // Update counters
        if (startRowEl) startRowEl.textContent = (startIndex + 1).toString();
        if (endRowEl) endRowEl.textContent = endIndex.toString();
        if (totalRowsEl) totalRowsEl.textContent = totalItems.toString();
        if (currentPageEl) currentPageEl.textContent = currentPage.toString();
        if (totalPagesEl) totalPagesEl.textContent = totalPages.toString();

        // Update Prev / Next buttons
        if (btnPrev) {
            if (currentPage <= 1) {
                btnPrev.classList.add('disabled');
                btnPrev.disabled = true;
            } else {
                btnPrev.classList.remove('disabled');
                btnPrev.disabled = false;
            }
            btnPrev.style.removeProperty('background');
            btnPrev.style.removeProperty('borderColor');
            btnPrev.style.removeProperty('color');
            btnPrev.style.removeProperty('cursor');
            btnPrev.style.removeProperty('opacity');
        }
        if (btnNext) {
            if (currentPage >= totalPages) {
                btnNext.classList.add('disabled');
                btnNext.disabled = true;
            } else {
                btnNext.classList.remove('disabled');
                btnNext.disabled = false;
            }
            btnNext.style.removeProperty('background');
            btnNext.style.removeProperty('borderColor');
            btnNext.style.removeProperty('color');
            btnNext.style.removeProperty('cursor');
            btnNext.style.removeProperty('opacity');
        }

        // Render page numbers (Sliding window 1, 2, 3, 4... exactly like Image 5)
        if (pageNumbersContainer) {
            pageNumbersContainer.innerHTML = '';

            let startPage = Math.max(1, currentPage - 2);
            let endPage = Math.min(totalPages, Math.max(startPage + 3, currentPage + 2));
            if (endPage - startPage < 3 && totalPages >= 4) {
                startPage = Math.max(1, endPage - 3);
            }

            // First page if window doesn't start at 1
            if (startPage > 1) {
                const btn1 = createPageBtn(1);
                pageNumbersContainer.appendChild(btn1);
                if (startPage > 2) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'kuota-page-ellipsis';
                    ellipsis.textContent = '…';
                    ellipsis.style.padding = '0 0.25rem';
                    ellipsis.style.color = '#94A3B8';
                    pageNumbersContainer.appendChild(ellipsis);
                }
            }

            // Visible range
            for (let p = startPage; p <= endPage; p++) {
                const btn = createPageBtn(p);
                pageNumbersContainer.appendChild(btn);
            }

            // Last page if window doesn't reach totalPages
            if (endPage < totalPages) {
                if (endPage < totalPages - 1) {
                    const ellipsis = document.createElement('span');
                    ellipsis.className = 'kuota-page-ellipsis';
                    ellipsis.textContent = '…';
                    ellipsis.style.padding = '0 0.25rem';
                    ellipsis.style.color = '#94A3B8';
                    pageNumbersContainer.appendChild(ellipsis);
                }
                const btnLast = createPageBtn(totalPages);
                pageNumbersContainer.appendChild(btnLast);
            }
        }

        // Update per-page pill active states
        perPagePills.forEach(pill => {
            const size = parseInt(pill.getAttribute('data-per-page'), 10);
            if (size === perPage) {
                pill.classList.add('active');
            } else {
                pill.classList.remove('active');
            }
            pill.style.removeProperty('background');
            pill.style.removeProperty('color');
            pill.style.removeProperty('font-weight');
        });
    }

    function createPageBtn(pageNum) {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'kuota-page-num' + (pageNum === currentPage ? ' active' : '');
        btn.textContent = pageNum.toString();
        btn.setAttribute('aria-label', `Lembar ${pageNum}`);

        if (pageNum === currentPage) {
            btn.setAttribute('aria-current', 'page');
        }

        btn.addEventListener('click', () => {
            currentPage = pageNum;
            render();
        });
        return btn;
    }

    // Attach Prev / Next handlers
    if (btnPrev) {
        btnPrev.addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                render();
            }
        });
    }

    if (btnNext) {
        btnNext.addEventListener('click', () => {
            const totalPages = Math.max(1, Math.ceil(totalItems / perPage));
            if (currentPage < totalPages) {
                currentPage++;
                render();
            }
        });
    }

    // Attach Per-Page switcher handlers
    perPagePills.forEach(pill => {
        pill.addEventListener('click', () => {
            const newSize = parseInt(pill.getAttribute('data-per-page'), 10);
            if (newSize && newSize !== perPage) {
                perPage = newSize;
                currentPage = 1;
                render();
            }
        });
    });

    // Initial render
    render();
}

// Global exposure for reliability
if (typeof window !== 'undefined') {
    window.initKuotaPagination = initKuotaPagination;
}

// Auto-initialize on DOM ready if elements exist
if (typeof document !== 'undefined') {
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initKuotaPagination);
    } else {
        initKuotaPagination();
    }
}
