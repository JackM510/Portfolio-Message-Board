import(window.API.jsCheckboxes).then(({ validateCheckboxes }) => {
    document.addEventListener("DOMContentLoaded", function () {  
        // Event Listeners for delete account checkboxes
        const checkboxes = document.querySelectorAll('.required-checkbox');
        const deleteBtn = document.getElementById('delete-btn');
        //checkboxes.forEach(cb => cb.addEventListener('change', validateCheckboxes(deleteBtn, checkboxes)));

        checkboxes.forEach(cb => 
            cb.addEventListener('change', () => validateCheckboxes(deleteBtn, checkboxes))
        );

        
        /*function validateCheckboxes() {
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            if (allChecked) {
                deleteBtn.classList.remove("disabled");
            } else {
                deleteBtn.classList.add("disabled");
            }
        }*/

        // AJAX request for updating email address
        document.getElementById("update-email-form").addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch(API.updateEmail, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); 
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
                console.log("Server Response:", data);
                location.reload();
                sessionStorage.setItem("openPanel", "collapse-pw");
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    })
});