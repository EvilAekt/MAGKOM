/**
 * Peserta Pendaftaran Index Module
 * Handles New Application Modal, Duration Chips, and History Accordion
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    function openModalPendaftaran() {
        const modal = document.getElementById('pendaftaranModal');
        if (modal) {
            modal.classList.add('open');
            updateEstimasiPeriode();
        }
    }

    function closeModalPendaftaran() {
        const modal = document.getElementById('pendaftaranModal');
        if (modal) modal.classList.remove('open');
    }

    function handleKuotaChange(select) {
        const selectedOption = select.options[select.selectedIndex];
        const bulanId = selectedOption ? selectedOption.getAttribute('data-bulan-id') : '';
        const inputBulan = document.getElementById('bulan_magang_id');
        if (inputBulan) inputBulan.value = bulanId || '';
        updateEstimasiPeriode();
    }

    function openModalWithKuota(kuotaId) {
        const select = document.getElementById('kuota_id');
        if (select) {
            select.value = kuotaId;
            handleKuotaChange(select);
        }
        openModalPendaftaran();
    }

    function updateEstimasiPeriode() {
        const selectKuota = document.getElementById('kuota_id');
        const selectDurasi = document.getElementById('durasi_bulan');
        const previewBox = document.getElementById('previewPeriodeBox');
        const previewText = document.getElementById('previewPeriodeText');

        if (!selectKuota || !selectDurasi || !previewBox || !previewText) return;

        const selectedOption = selectKuota.options[selectKuota.selectedIndex];
        const bulanMulaiStr = selectedOption ? selectedOption.getAttribute('data-bulan-mulai') : null;
        const durasi = parseInt(selectDurasi.value, 10);

        if (bulanMulaiStr && durasi) {
            const startDate = new Date(bulanMulaiStr);
            const endDate = new Date(startDate.getFullYear(), startDate.getMonth() + durasi, 0);

            const options = { day: 'numeric', month: 'long', year: 'numeric' };
            const startFmt = startDate.toLocaleDateString('id-ID', options);
            const endFmt = endDate.toLocaleDateString('id-ID', options);

            previewText.textContent = `${startFmt} s/d ${endFmt} (${durasi} Bulan)`;
            previewBox.style.display = 'block';
        } else {
            previewBox.style.display = 'none';
        }
    }

    function selectDurationChip(months) {
        const select = document.getElementById('durasi_bulan');
        if (select) {
            select.value = months;
            updateEstimasiPeriode();
        }
        const chips = document.querySelectorAll('.duration-chip-btn');
        chips.forEach((chip) => {
            const chipMonths = parseInt(chip.dataset.duration, 10);
            if (chipMonths === months) {
                chip.classList.add('is-active');
            } else {
                chip.classList.remove('is-active');
            }
        });
    }

    // Initialize kuota if preselected
    const select = document.getElementById('kuota_id');
    if (select && select.value) {
        handleKuotaChange(select);
    }

    // --- Delegated Click Listeners ---
    document.addEventListener('click', (e) => {
        const target = e.target;

        const openBtn = target.closest('[data-action="open-modal-pendaftaran"]');
        if (openBtn) {
            e.preventDefault();
            openModalPendaftaran();
            return;
        }

        const openWithKuotaBtn = target.closest('[data-action="open-modal-with-kuota"]');
        if (openWithKuotaBtn) {
            e.preventDefault();
            const kuotaId = openWithKuotaBtn.dataset.kuotaId;
            openModalWithKuota(kuotaId);
            return;
        }

        const closeBtn = target.closest('[data-action="close-modal-pendaftaran"]');
        if (closeBtn) {
            e.preventDefault();
            closeModalPendaftaran();
            return;
        }

        const chipBtn = target.closest('[data-action="select-duration-chip"]');
        if (chipBtn) {
            e.preventDefault();
            const months = parseInt(chipBtn.dataset.duration, 10);
            if (months) selectDurationChip(months);
            return;
        }

        const btnToggleRiwayat = target.closest('#btnToggleRiwayat');
        if (btnToggleRiwayat) {
            e.preventDefault();
            const riwayatBody = document.getElementById('riwayatAccordionBody');
            if (riwayatBody) {
                const isHidden = riwayatBody.style.display === 'none';
                riwayatBody.style.display = isHidden ? 'block' : 'none';
                const svg = btnToggleRiwayat.querySelector('svg');
                if (svg) {
                    svg.style.transform = isHidden ? 'rotate(0deg)' : 'rotate(180deg)';
                }
            }
            return;
        }

        const modal = document.getElementById('pendaftaranModal');
        if (target === modal) {
            closeModalPendaftaran();
            return;
        }
    });

    document.addEventListener('change', (e) => {
        if (e.target.id === 'kuota_id') {
            handleKuotaChange(e.target);
        } else if (e.target.id === 'durasi_bulan') {
            updateEstimasiPeriode();
        }
    });

    document.addEventListener('submit', (e) => {
        if (e.target.id === 'formPendaftaranBaru') {
            const btn = document.getElementById('btnSubmitPendaftaran');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `<span style="display:inline-block;width:14px;height:14px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:spin 0.6s linear infinite;margin-right:8px;"></span> Menyimpan...`;
            }
        }
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            closeModalPendaftaran();
        }
    });
});
