(function () {
    'use strict';

    function escapeSelector(value) {
        if (window.CSS && typeof window.CSS.escape === 'function') {
            return window.CSS.escape(value);
        }

        return String(value).replace(/["\\]/g, '\\$&');
    }

    function findIframe(embedId) {
        if (!embedId) {
            return null;
        }

        return document.querySelector('.shaw-external-page__frame[data-embed-id="' + escapeSelector(embedId) + '"]');
    }

    function normalizeOrigin(src) {
        try {
            return new URL(src, window.location.href).origin;
        } catch (error) {
            return '';
        }
    }

    function applyFrameHeight(iframe, height) {
        var nextHeight = Math.max(320, Math.ceil(height));
        var currentHeight = Number(iframe.dataset.currentHeight || 0);

        if (Math.abs(currentHeight - nextHeight) < 2) {
            return;
        }

        iframe.style.height = nextHeight + 'px';
        iframe.style.minHeight = nextHeight + 'px';
        iframe.setAttribute('scrolling', 'no');
        iframe.dataset.currentHeight = String(nextHeight);

        var wrapper = iframe.closest('.shaw-external-page');
        if (wrapper) {
            wrapper.classList.add('shaw-external-page--auto-height');
        }
    }

    function scrollWrapperToTop(iframe) {
        var wrapper = iframe.closest('.shaw-external-page');
        if (!wrapper) {
            return;
        }

        var top = wrapper.getBoundingClientRect().top + window.scrollY - 110;
        window.scrollTo({
            top: Math.max(0, top),
            left: 0,
            behavior: 'auto'
        });
    }

    window.addEventListener('message', function (event) {
        var data = event.data;
        if (!data || data.type !== 'shawedu:embed:resize') {
            return;
        }

        var iframe = findIframe(data.embedId);
        if (!iframe) {
            return;
        }

        var expectedOrigin = iframe.dataset.externalOrigin || normalizeOrigin(iframe.getAttribute('src') || '');
        if (expectedOrigin && event.origin && expectedOrigin !== event.origin) {
            return;
        }

        var height = Number(data.height);
        if (!Number.isFinite(height) || height <= 0) {
            return;
        }

        var nextPathname = typeof data.pathname === 'string' ? data.pathname : '';
        if (nextPathname && iframe.dataset.lastPathname !== nextPathname) {
            iframe.dataset.lastPathname = nextPathname;
            scrollWrapperToTop(iframe);
        }

        applyFrameHeight(iframe, height);
    });
})();
