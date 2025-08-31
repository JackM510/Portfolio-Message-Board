import(window.API.jsCheckboxes).then(({ validateCheckboxes }) => {
    document.addEventListener("DOMContentLoaded", function () {  
        // Checkboxes & delete btn
        const checkboxes = document.querySelectorAll('.required-checkbox');
        const deleteBtn = document.getElementById('delete-btn');
        checkboxes.forEach(cb => cb.addEventListener('change', () => validateCheckboxes(deleteBtn, checkboxes)));

        // AJAX request for updating email
        document.getElementById("update-email-form").addEventListener("submit", function(event) {
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
        document.getElementById("update-pw-form").addEventListener("submit", function(event) {
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
});