// Fade containers on same page
export function fadeEl(el) {
        el.classList.remove('fade-in');  // reset
        void el.offsetWidth;             // force reflow
        el.classList.add('fade-in');     // trigger transition
}

// Fade all body elements on page load
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('fade-in');
});