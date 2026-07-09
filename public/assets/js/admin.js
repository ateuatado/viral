/**
 * Viral Admin — Client-side JavaScript
 * ─────────────────────────────────────
 */

'use strict';

/* ==========================================================================
   1. Sidebar toggle (mobile)
   ========================================================================== */
document.addEventListener('DOMContentLoaded', () => {
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');

    const openSidebar = () => {
        sidebar?.classList.add('show');
        overlay?.classList.add('show');
        document.body.style.overflow = 'hidden';
    };

    const closeSidebar = () => {
        sidebar?.classList.remove('show');
        overlay?.classList.remove('show');
        document.body.style.overflow = '';
    };

    toggleBtn?.addEventListener('click', () => {
        sidebar?.classList.contains('show') ? closeSidebar() : openSidebar();
    });

    overlay?.addEventListener('click', closeSidebar);

    // Close sidebar when pressing Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeSidebar();
    });
});

/* ==========================================================================
   2. CSRF helper for AJAX / fetch calls
   ========================================================================== */
const ViralCSRF = (() => {
    const meta = () => document.querySelector('meta[name="csrf-token"]');

    return {
        token: ()  => meta()?.content ?? '',
        header: () => ({ 'X-CSRF-TOKEN': meta()?.content ?? '' }),
        field: ()  => {
            const t = meta()?.content ?? '';
            return `<input type="hidden" name="csrf_test_name" value="${t}">`;
        },
        /** Returns headers object ready for fetch() */
        fetchHeaders: (extra = {}) => ({
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': meta()?.content ?? '',
            ...extra,
        }),
    };
})();

/* ==========================================================================
   3. Confirm dialog for destructive actions
   ========================================================================== */
document.addEventListener('click', (e) => {
    const btn = e.target.closest('[data-confirm]');
    if (!btn) return;

    const message = btn.dataset.confirm || 'Tem certeza que deseja continuar?';

    if (!confirm(message)) {
        e.preventDefault();
        e.stopImmediatePropagation();
    }
});

/* ==========================================================================
   4. Toast notifications helper
   ========================================================================== */
const ViralToast = (() => {
    const container = () => document.getElementById('toastContainer');

    const iconMap = {
        success: 'bi-check-circle-fill',
        danger:  'bi-exclamation-triangle-fill',
        warning: 'bi-exclamation-circle-fill',
        info:    'bi-info-circle-fill',
    };

    /**
     * Show a toast notification.
     * @param {string} message
     * @param {'success'|'danger'|'warning'|'info'} type
     * @param {number} duration  Auto-dismiss ms (0 = manual close only)
     */
    const show = (message, type = 'info', duration = 4000) => {
        const el = document.createElement('div');
        el.className = `toast-msg toast-${type}`;
        el.innerHTML = `
            <i class="bi ${iconMap[type] ?? iconMap.info}"></i>
            <span>${message}</span>
            <button class="toast-close" aria-label="Fechar">&times;</button>
        `;

        el.querySelector('.toast-close').addEventListener('click', () => dismiss(el));

        container()?.appendChild(el);

        if (duration > 0) {
            setTimeout(() => dismiss(el), duration);
        }

        return el;
    };

    const dismiss = (el) => {
        el.style.opacity = '0';
        el.style.transform = 'translateX(30px)';
        setTimeout(() => el.remove(), 250);
    };

    return { show };
})();

/* ==========================================================================
   5. Auto-slug generator
   ========================================================================== */
const ViralSlug = (() => {
    const translitMap = {
        'á':'a','à':'a','ã':'a','â':'a','ä':'a',
        'é':'e','è':'e','ê':'e','ë':'e',
        'í':'i','ì':'i','î':'i','ï':'i',
        'ó':'o','ò':'o','õ':'o','ô':'o','ö':'o',
        'ú':'u','ù':'u','û':'u','ü':'u',
        'ç':'c','ñ':'n',
    };

    /**
     * Convert a campaign name into a URL-safe slug.
     * @param {string} text
     * @returns {string}
     */
    const generate = (text) => {
        return text
            .toLowerCase()
            .split('')
            .map(c => translitMap[c] ?? c)
            .join('')
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-+|-+$/g, '')
            .substring(0, 100);
    };

    /**
     * Bind auto-slug from a source input to a target input.
     * @param {string} sourceSelector  CSS selector for the name input
     * @param {string} targetSelector  CSS selector for the slug input
     */
    const bind = (sourceSelector, targetSelector) => {
        const src = document.querySelector(sourceSelector);
        const tgt = document.querySelector(targetSelector);
        if (!src || !tgt) return;

        let manuallyEdited = false;

        tgt.addEventListener('input', () => { manuallyEdited = true; });

        src.addEventListener('input', () => {
            if (!manuallyEdited) {
                tgt.value = generate(src.value);
            }
        });
    };

    return { generate, bind };
})();

/* ==========================================================================
   6. Expose globals
   ========================================================================== */
window.ViralCSRF  = ViralCSRF;
window.ViralToast = ViralToast;
window.ViralSlug  = ViralSlug;
