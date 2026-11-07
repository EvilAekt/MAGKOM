/**
 * MAGKOM: Peserta Directory Module (ES Modules)
 * Handles client-side enhancements for Pembimbing Peserta Directory
 */

import { initScrollCraft } from '../../services/scroll-craft.js';

document.addEventListener('DOMContentLoaded', () => {
    initScrollCraft();
    // Optional instant search debounce or URL tab handling if needed
    const searchInput = document.querySelector('.peserta-search-input');
    if (searchInput) {
        searchInput.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                searchInput.value = '';
                searchInput.closest('form')?.submit();
            }
        });
    }
});
