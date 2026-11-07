const storageKey = 'wms-theme';

function preferredTheme() {
    return localStorage.getItem(storageKey)
        ?? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
}

function applyTheme(theme) {
    document.documentElement.classList.toggle('dark', theme === 'dark');
    const label = document.querySelector('[data-theme-label]');
    if (label) {
        label.textContent = theme === 'dark'
            ? document.documentElement.lang === 'bn' ? 'লাইট' : 'Light'
            : document.documentElement.lang === 'bn' ? 'ডার্ক' : 'Dark';
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const theme = preferredTheme();
    applyTheme(theme);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.addEventListener('click', () => {
            const next = document.documentElement.classList.contains('dark') ? 'light' : 'dark';
            localStorage.setItem(storageKey, next);
            applyTheme(next);
        });
    });
});
