document.addEventListener("DOMContentLoaded", function () {  

    // Keep track of any open accordian cards
    window.addEventListener("DOMContentLoaded", () => {
        const id = sessionStorage.getItem("openPanel");
        if (id) {
          const el = document.getElementById(id);
          if (el) {
            new bootstrap.Collapse(el, {
              toggle: true
            });
            
            el.scrollIntoView({ behavior: "smooth", block: "start" });
          }
          sessionStorage.removeItem("openPanel");
        }
    });
      



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
            sessionStorage.setItem("openPanel", "collapse-email"); // or "collapse-pw", etc.
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
            location.reload();
            sessionStorage.setItem("openPanel", "collapse-pw"); // or "collapse-pw", etc.
        })
        .catch(error => console.error("Fetch Error:", error));
});







});