/**
 * MAGKOM: Logbook Review Module (ES Modules)
 * Handles client-side checkbox delegation, bulk actions, and review modal
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Bulk Selection & Floating Action Bar
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const rowCheckboxes = document.querySelectorAll('.logbook-checkbox');
    const bulkBar = document.getElementById('bulk-action-bar');
    const bulkCountSpan = document.getElementById('bulk-selected-count');
    const bulkHiddenInputs = document.getElementById('bulk-hidden-inputs');
    const btnCancelBulk = document.getElementById('btnCancelBulk');

    function updateBulkBar() {
        const checkedBoxes = document.querySelectorAll('.logbook-checkbox:checked');
        if (checkedBoxes.length > 0) {
            if (bulkCountSpan) bulkCountSpan.textContent = checkedBoxes.length.toString();
            if (bulkHiddenInputs) {
                bulkHiddenInputs.innerHTML = '';
                checkedBoxes.forEach((cb) => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'logbook_ids[]';
                    input.value = cb.value;
                    bulkHiddenInputs.appendChild(input);
                });
            }
            if (bulkBar) bulkBar.style.display = 'flex';
        } else {
            if (bulkBar) bulkBar.style.display = 'none';
            if (bulkHiddenInputs) bulkHiddenInputs.innerHTML = '';
        }
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', () => {
            const isChecked = selectAllCheckbox.checked;
            rowCheckboxes.forEach((cb) => {
                if (!cb.disabled) {
                    cb.checked = isChecked;
                }
            });
            updateBulkBar();
        });
    }

    rowCheckboxes.forEach((cb) => {
        cb.addEventListener('change', () => {
            const activeBoxes = Array.from(rowCheckboxes).filter((c) => !c.disabled);
            const allChecked = activeBoxes.length > 0 && activeBoxes.every((c) => c.checked);
            const someChecked = activeBoxes.some((c) => c.checked);
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = allChecked;
                selectAllCheckbox.indeterminate = someChecked && !allChecked;
            }
            updateBulkBar();
        });
    });

    if (btnCancelBulk) {
        btnCancelBulk.addEventListener('click', (e) => {
            e.preventDefault();
            rowCheckboxes.forEach((cb) => { cb.checked = false; });
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
            updateBulkBar();
        });
    }

    // 2. Review Modal Operations
    const reviewModal = document.getElementById('modal-review-logbook');
    const formReview = document.getElementById('form-review-logbook');
    const modalSubtitle = document.getElementById('review-modal-subtitle');
    const statusSelect = document.getElementById('review-status-select');
    const catatanTextarea = document.getElementById('review-catatan-textarea');
    const catatanStar = document.getElementById('catatan-required-star');
    const cannedSelect = document.getElementById('quick-canned-select');

    function handleStatusChange(status) {
        if (!catatanStar || !catatanTextarea) return;
        if (status === 'perlu_revisi') {
            catatanStar.style.display = 'inline';
            catatanTextarea.setAttribute('required', 'required');
        } else {
            catatanStar.style.display = 'none';
            catatanTextarea.removeAttribute('required');
        }
    }

    if (statusSelect) {
        statusSelect.addEventListener('change', () => {
            handleStatusChange(statusSelect.value);
        });
    }

    if (cannedSelect && catatanTextarea) {
        cannedSelect.addEventListener('change', () => {
            if (cannedSelect.value) {
                catatanTextarea.value = cannedSelect.value;
            }
        });
    }

    // Open review modal via data attributes
    document.querySelectorAll('.btn-open-review-modal').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const logId = btn.getAttribute('data-log-id');
            const nama = btn.getAttribute('data-peserta-nama') || 'Peserta';
            const tanggal = btn.getAttribute('data-tanggal') || '';
            const status = btn.getAttribute('data-status') || 'terkirim';
            const catatan = btn.getAttribute('data-catatan') || '';

            if (modalSubtitle) {
                modalSubtitle.textContent = `${nama} • Tanggal: ${tanggal}`;
            }
            if (formReview && logId) {
                formReview.action = `/pembimbing/logbook/${logId}/review`;
            }
            if (statusSelect) {
                statusSelect.value = status;
                handleStatusChange(status);
            }
            if (catatanTextarea) {
                catatanTextarea.value = catatan;
            }
            if (cannedSelect) {
                cannedSelect.value = '';
            }
            if (reviewModal) {
                reviewModal.style.display = 'flex';
            }
        });
    });

    // Close review modal
    document.querySelectorAll('[data-modal-close]').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const target = btn.getAttribute('data-modal-close');
            const modal = target ? document.querySelector(target) : btn.closest('.modal-overlay');
            if (modal) modal.style.display = 'none';
        });
    });

    if (reviewModal) {
        reviewModal.addEventListener('click', (e) => {
            if (e.target === reviewModal) {
                reviewModal.style.display = 'none';
            }
        });
    }
});
