/**
 * Admin Penempatan Antrean Show Module
 * CSP Compliant (no inline scripts or inline event handlers)
 */

document.addEventListener('DOMContentLoaded', () => {
    const pembimbingSelect = document.getElementById('antrean-pembimbing-id');
    const bidangSelect = document.getElementById('antrean-bidang-id');

    if (pembimbingSelect && bidangSelect) {
        pembimbingSelect.addEventListener('change', () => {
            const selectedOption = pembimbingSelect.options[pembimbingSelect.selectedIndex];
            const bidangId = selectedOption ? selectedOption.getAttribute('data-bidang-id') : '';

            if (bidangId) {
                bidangSelect.value = bidangId;
            }
        });
    }

    function activateMemberTab(memberKey) {
        if (!memberKey) return;

        document.querySelectorAll('.antrean-member-tab[data-member-key]').forEach((tab) => {
            const isMatch = tab.getAttribute('data-member-key') === memberKey;
            tab.classList.toggle('is-active', isMatch);
            tab.setAttribute('aria-selected', isMatch ? 'true' : 'false');
        });

        document.querySelectorAll('.antrean-member-panel[data-member-panel]').forEach((panel) => {
            const isMatch = panel.getAttribute('data-member-panel') === memberKey;
            if (isMatch) {
                panel.removeAttribute('hidden');
            } else {
                panel.setAttribute('hidden', '');
            }
        });
    }

    document.addEventListener('click', (e) => {
        const trigger = e.target.closest('[data-action="select-member-tab"]');
        if (!trigger) return;

        e.preventDefault();
        const memberKey = trigger.getAttribute('data-member-key');
        activateMemberTab(memberKey);

        const scrollTargetId = trigger.getAttribute('data-scroll-target');
        if (scrollTargetId) {
            const targetEl = document.getElementById(scrollTargetId);
            if (targetEl) {
                targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});
