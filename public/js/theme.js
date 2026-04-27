/**
 * Theme Switcher Script
 * Handles Dark/Light mode toggle and persistence
 */
(function() {
    // 1. Check for saved theme in localStorage
    const savedTheme = localStorage.getItem('theme') || 'light';
    document.documentElement.setAttribute('data-theme', savedTheme);

    // 2. Global function to toggle theme
    window.toggleTheme = function() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
        
        // Update icons if they exist
        updateThemeIcons(newTheme);
    };

    // 3. Update toggle icons and text visually
    function updateThemeIcons(theme) {
        const icons = document.querySelectorAll('.theme-toggle-icon');
        icons.forEach(icon => {
            if (theme === 'dark') {
                icon.classList.remove('fa-moon');
                icon.classList.add('fa-sun');
            } else {
                icon.classList.remove('fa-sun');
                icon.classList.add('fa-moon');
            }
        });

        const textEls = document.querySelectorAll('#theme-text');
        textEls.forEach(el => {
            el.textContent = theme === 'dark' ? 'Kunduzgi rejim' : 'Tungi rejim';
        });
    }

    // Initialize icons on DOM load
    document.addEventListener('DOMContentLoaded', () => {
        updateThemeIcons(localStorage.getItem('theme') || 'light');
    });
})();
