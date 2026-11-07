/**
 * MAGKOM: Penilaian Magang Module (ES Modules)
 * Handles client-side form filters for Pembimbing Penilaian
 */

import { initScrollCraft } from '../../services/scroll-craft.js';
import { animateTileStats } from '../../services/pembimbing-motion.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    animateTileStats();
    // Optional client-side enhancement for quick filter changes
    const selectFilters = document.querySelectorAll('.penilaian-filter-form select');
    selectFilters.forEach((select) => {
        select.addEventListener('change', () => {
            select.closest('form')?.submit();
        });
    });
});
