/**
 * ==============================================================================
 * Scroll-Craft Motion Service (MagKom Sleman)
 * GPU-accelerated IntersectionObserver scroll reveal engine with lifecycle
 * session tracking (one-time entrance & in-page interaction lock).
 * ==============================================================================
 */

const SESSION_KEY = 'magkom_last_page_path';

export function initScrollCraft(root = document, options = {}) {
    if (typeof window === 'undefined') return;

    const currentPath = window.location.pathname;
    let lastPath = null;
    try {
        lastPath = sessionStorage.getItem(SESSION_KEY);
    } catch (e) {
        lastPath = null;
    }

    const isSamePageInteraction = (lastPath === currentPath) && !options.forceReplay;

    // Respect OS prefers-reduced-motion (WCAG 2.1 AA) OR in-page interaction bypass
    const prefersReducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion || isSamePageInteraction) {
        root.querySelectorAll('[data-scroll-reveal]').forEach((el) => {
            el.classList.add('is-revealed', 'sc-instant-reveal', 'sc-settled');
        });
        return;
    }

    // Fresh page entrance: record current path in sessionStorage
    try {
        sessionStorage.setItem(SESSION_KEY, currentPath);
    } catch (e) {}

    const revealElements = root.querySelectorAll('[data-scroll-reveal]:not(.is-revealed)');
    if (!revealElements.length) return;

    if (!('IntersectionObserver' in window)) {
        revealElements.forEach((el) => {
            el.classList.add('is-revealed', 'sc-instant-reveal', 'sc-settled');
        });
        return;
    }

    const observer = new IntersectionObserver((entries, obs) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                const target = entry.target;
                target.classList.add('is-revealed');
                obs.unobserve(target);

                // Freeze transitions once entrance completes so in-page actions never trigger motion loop
                const onEnd = (e) => {
                    if (e.target === target && (e.propertyName === 'opacity' || e.propertyName === 'transform')) {
                        target.classList.add('sc-settled');
                        target.removeEventListener('transitionend', onEnd);
                    }
                };
                target.addEventListener('transitionend', onEnd);

                // Safety fallback timeout in case transitionend does not fire
                setTimeout(() => {
                    target.classList.add('sc-settled');
                }, 900);
            }
        });
    }, {
        threshold: 0.1,
        rootMargin: '0px 0px -40px 0px'
    });

    revealElements.forEach((el) => observer.observe(el));
}

/**
 * Reset session tracking (used for manual re-arming or test suites)
 */
export function resetScrollCraftSession() {
    try {
        sessionStorage.removeItem(SESSION_KEY);
    } catch (e) {}
}

// Global window exposure
if (typeof window !== 'undefined') {
    window.initScrollCraft = initScrollCraft;
    window.resetScrollCraftSession = resetScrollCraftSession;
}
