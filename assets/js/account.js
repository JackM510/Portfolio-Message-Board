document.addEventListener("DOMContentLoaded", function () { 
    // Declare module variables
    let validateCheckboxes;
    // Load JS modules dynamically
    import(window.API.jsCheckboxes)
        .then(mod => validateCheckboxes = mod.validateCheckboxes)
        .catch(err => console.error("Checkbox module failed to load:", err));

    const updateEmail = document.getElementById('update-email-form');
    const updatePassword = document.getElementById('update-pw-form');

    // Checkboxes & delete btn
    const checkboxes = document.querySelectorAll('.required-checkbox');
    const deleteBtn = document.getElementById('delete-btn');
    checkboxes.forEach(cb => cb.addEventListener('change', () => validateCheckboxes(deleteBtn, checkboxes)));

    // AJAX request for updating email
    updateEmail.addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch(API.updateEmail, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            location.reload();
            sessionStorage.setItem("openPanel", "collapse-email");
        })
        .catch(error => console.error("Fetch Error:", error));
    });

    // AJAX request for updating password
    updatePassword.addEventListener("submit", function(event) {
        event.preventDefault(); 
        const formData = new FormData(this);
        fetch(API.updatePassword, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            location.reload();
            sessionStorage.setItem("openPanel", "collapse-pw");
        })
        .catch(error => console.error("Fetch Error:", error));
    });
})