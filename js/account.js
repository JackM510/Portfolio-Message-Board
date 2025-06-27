document.addEventListener("DOMContentLoaded", function () {  

    // Event Listeners for delete account checkboxes
    const checkboxes = document.querySelectorAll('.required-checkbox');
    const deleteBtn = document.getElementById('delete-btn');

    function validateCheckboxes() {
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        
        if (allChecked) {
            deleteBtn.classList.remove("disabled");
        } else {
            deleteBtn.classList.add("disabled");
        }
    }

    checkboxes.forEach(cb => cb.addEventListener('change', validateCheckboxes));


    // AJAX request for updating email address
    document.getElementById("update-email-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission

        const formData = new FormData(this);

        fetch("actions/update_email.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); // Log server response
            location.reload();
            document.getElementById("collapse-pw").classList.add("show");
        })
        .catch(error => console.error("Fetch Error:", error));
    });



    // AJAX request for updating password
    document.getElementById("update-pw-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission

        const formData = new FormData(this);

        fetch("actions/update_password.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); // Log server response
            if (data.trim() === "success") {
                location.reload();
            } else {
                alert("Error updating password: " + data);
            }
        })
        .catch(error => console.error("Fetch Error:", error));
});







});