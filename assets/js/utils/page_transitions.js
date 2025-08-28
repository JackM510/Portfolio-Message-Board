// Function to fade a container
export function fadeEl(el) {
    el.classList.remove('fade-in');  // reset any fade-in
    void el.offsetWidth;             // force reflow
    el.classList.add('fade-in');     // trigger fade-in
}

// Fade body elements on page load
document.addEventListener('DOMContentLoaded', () => {
    document.body.classList.add('fade-in');
});