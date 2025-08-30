// Keep track of any open accordian cards
window.addEventListener("DOMContentLoaded", () => {
    const id = sessionStorage.getItem("openPanel");
    if (!id) return;
    const el = document.getElementById(id);
    if (!el) return;

    new bootstrap.Collapse(el, { toggle: true });

    el.addEventListener("shown.bs.collapse", () => {
        el.scrollIntoView({ behavior: "smooth", block: "center" });
        sessionStorage.removeItem("openPanel");
    }, { once: true });
});