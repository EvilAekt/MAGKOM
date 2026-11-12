/**
 * Controls for publishing Draft periods and opening period creation modal on /admin/kuota.
 */

function syncUbahBulanStatusToOpen() {
    if (typeof window.setStatusUbahBulan === 'function') {
        window.setStatusUbahBulan('dibuka');
        return;
    }

    const statusInput = document.getElementById('statusUbahInput');
    const btnDraft = document.getElementById('btnUbahStatusDraft');
    const btnDibuka = document.getElementById('btnUbahStatusDibuka');
    const iconDraft = document.getElementById('checkIconUbahDraft');
    const iconDibuka = document.getElementById('checkIconUbahDibuka');

    if (statusInput instanceof HTMLInputElement) {
        statusInput.value = 'dibuka';
    }
    if (btnDraft) {
        btnDraft.classList.remove('active');
    }
    if (btnDibuka) {
        btnDibuka.classList.add('active');
    }
    if (iconDraft) {
        iconDraft.style.display = 'none';
    }
    if (iconDibuka) {
        iconDibuka.style.display = 'inline';
    }
}

function openPublishDraftModal() {
    if (typeof window.openUbahBulanModal === 'function') {
        window.openUbahBulanModal();
    } else {
        const modal = document.getElementById('modalUbahBulan');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }

    syncUbahBulanStatusToOpen();

    const submitBtn = document.getElementById('btnSubmitUbahBulan');
    if (submitBtn instanceof HTMLElement) {
        window.requestAnimationFrame(() => submitBtn.focus());
    }
}

function openCreateBulanModal() {
    if (typeof window.openBuatBulanModal === 'function') {
        window.openBuatBulanModal();
        return;
    }

    const modal = document.getElementById('modalBuatBulan');
    if (modal) {
        modal.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }
}

export function initKuotaDraftControls() {
    const root = document.querySelector('.adm-kuota-revamp-wrap');
    if (!root || document.documentElement.dataset.kuotaDraftBound === '1') {
        return;
    }

    document.documentElement.dataset.kuotaDraftBound = '1';

    root.addEventListener('click', (event) => {
        const target = event.target instanceof Element ? event.target.closest('[data-kuota-action]') : null;
        if (!(target instanceof HTMLElement)) {
            return;
        }

        const action = target.dataset.kuotaAction;
        if (action === 'publish-draft') {
            openPublishDraftModal();
        } else if (action === 'open-buat-bulan') {
            openCreateBulanModal();
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initKuotaDraftControls);
} else {
    initKuotaDraftControls();
}
