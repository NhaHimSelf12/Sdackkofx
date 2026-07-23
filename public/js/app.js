/* FX Command - lightweight UI motion (no dependencies) */
(function () {
    var doc = document.documentElement;
    doc.classList.add('js');

    var reduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    // Trigger the "loaded" state (confidence bars grow in)
    requestAnimationFrame(function () {
        requestAnimationFrame(function () {
            doc.classList.add('loaded');
        });
    });

    if (reduced) {
        return;
    }

    // Staggered reveal for grids of small cards and list items
    var items = document.querySelectorAll('.market-card, .strategy-grid > .card, .news-item, .ai-item');
    if ('IntersectionObserver' in window && items.length) {
        var io = new IntersectionObserver(function (entries) {
            var visible = entries.filter(function (e) { return e.isIntersecting; });
            visible.forEach(function (entry, i) {
                var el = entry.target;
                el.style.transitionDelay = Math.min(i * 55, 440) + 'ms';
                el.classList.add('in-view');
                el.addEventListener('transitionend', function () {
                    el.style.transitionDelay = '0ms';
                }, { once: true });
                io.unobserve(el);
            });
        }, { threshold: 0.15, rootMargin: '0px 0px -20px 0px' });

        items.forEach(function (el) {
            el.classList.add('anim');
            io.observe(el);
        });
    }

    // Count-up animation for plain integer stat values
    document.querySelectorAll('.stat-value').forEach(function (el) {
        var raw = el.textContent.trim();
        if (!/^\d{1,5}$/.test(raw)) {
            return;
        }
        var target = parseInt(raw, 10);
        var duration = 900;
        var start = null;
        el.textContent = '0';
        function tick(t) {
            if (start === null) {
                start = t;
            }
            var p = Math.min(1, (t - start) / duration);
            var eased = 1 - Math.pow(1 - p, 3);
            el.textContent = String(Math.round(target * eased));
            if (p < 1) {
                requestAnimationFrame(tick);
            }
        }
        requestAnimationFrame(tick);
    });
})();
