document.querySelectorAll('[data-password-target]').forEach((button) => {
    button.addEventListener('click', () => {
        const input = document.getElementById(button.dataset.passwordTarget);
        if (!input) return;
        const reveal = input.type === 'password';
        input.type = reveal ? 'text' : 'password';
        button.setAttribute('aria-pressed', String(reveal));
        button.setAttribute('aria-label', reveal ? 'Hide password' : 'Show password');
        button.innerHTML = `<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>${reveal ? '<path d="m3 3 18 18"/>' : ''}</svg>`;
    });
});

(() => {
    const toggle = document.querySelector('.theme-toggle');
    if (!toggle) return;
    const root = document.documentElement;
    const colorMeta = document.querySelector('meta[name="theme-color"]');
    const sync = (dark) => {
        root.classList.toggle('dark-mode', dark);
        toggle.setAttribute('aria-pressed', String(dark));
        toggle.setAttribute('aria-label', dark ? 'Switch to light mode' : 'Switch to dark mode');
        toggle.title = dark ? 'Switch to light mode' : 'Switch to dark mode';
        if (colorMeta) colorMeta.content = dark ? '#07080c' : '#f7f8fc';
    };
    sync(root.classList.contains('dark-mode'));
    toggle.addEventListener('click', () => {
        const dark = !root.classList.contains('dark-mode');
        sync(dark);
        try { localStorage.setItem('travel-guide-theme', dark ? 'dark' : 'light'); } catch (e) {}
    });
})();

(() => {
    const header = document.querySelector('.navbar');
    const menuToggle = document.querySelector('.mobile-menu-toggle');
    const progress = document.querySelector('.scroll-progress');
    if (header && menuToggle) {
        const closeMenu = () => {
            header.classList.remove('mobile-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Open navigation menu');
        };
        menuToggle.addEventListener('click', () => {
            const open = !header.classList.contains('mobile-open');
            header.classList.toggle('mobile-open', open);
            menuToggle.setAttribute('aria-expanded', String(open));
            menuToggle.setAttribute('aria-label', open ? 'Close navigation menu' : 'Open navigation menu');
        });
        header.querySelectorAll('nav a').forEach((link) => link.addEventListener('click', closeMenu));
        document.addEventListener('keydown', (event) => { if (event.key === 'Escape') closeMenu(); });
        document.addEventListener('click', (event) => { if (!header.contains(event.target)) closeMenu(); });
        window.matchMedia('(min-width: 641px)').addEventListener?.('change', closeMenu);
    }
    let frame = 0;
    const updateScrollUI = () => {
        frame = 0;
        const y = window.scrollY || 0;
        header?.classList.toggle('is-scrolled', y > 24);
        if (progress) {
            const max = document.documentElement.scrollHeight - window.innerHeight;
            progress.style.transform = `scaleX(${max > 0 ? Math.min(1, y / max) : 0})`;
        }
    };
    const requestUpdate = () => {
        if (!frame) frame = window.requestAnimationFrame(updateScrollUI);
    };
    window.addEventListener('scroll', requestUpdate, { passive: true });
    window.addEventListener('resize', requestUpdate);
    updateScrollUI();

    const motionOff = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const revealTargets = document.querySelectorAll('.home-container > section, .post-grid > article, .admin-section, .review-card, .wishlist-card');
    if (!motionOff && 'IntersectionObserver' in window && revealTargets.length) {
        revealTargets.forEach((element, index) => {
            element.classList.add('reveal');
            element.style.transitionDelay = `${Math.min(index % 4, 3) * 70}ms`;
        });
        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;
                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
        revealTargets.forEach((element) => observer.observe(element));
    }
})();
