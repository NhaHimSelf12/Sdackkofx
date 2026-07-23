/* FX Command theme bootstrap: runs in <head> to avoid a flash of wrong theme. */
(function () {
    try {
        var saved = localStorage.getItem('fx-theme');
        var theme = saved === 'light' || saved === 'dark'
            ? saved
            : (window.matchMedia && window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
        document.documentElement.dataset.theme = theme;

        window.fxSetTheme = function (next) {
            document.documentElement.dataset.theme = next;
            try { localStorage.setItem('fx-theme', next); } catch (e) {}
            window.dispatchEvent(new CustomEvent('fx-theme-change', { detail: { theme: next } }));
            document.querySelectorAll('.theme-toggle .theme-label').forEach(function (el) {
                el.textContent = next === 'light' ? 'Dark mode' : 'Light mode';
            });
        };

        document.addEventListener('click', function (event) {
            var button = event.target.closest('.theme-toggle');
            if (!button) return;
            window.fxSetTheme(document.documentElement.dataset.theme === 'light' ? 'dark' : 'light');
        });

        document.addEventListener('DOMContentLoaded', function () {
            var current = document.documentElement.dataset.theme;
            document.querySelectorAll('.theme-toggle .theme-label').forEach(function (el) {
                el.textContent = current === 'light' ? 'Dark mode' : 'Light mode';
            });
        });
    } catch (e) { /* theme is progressive enhancement */ }
})();
