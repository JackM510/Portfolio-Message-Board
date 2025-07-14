import { predictLines } from "./utils/textarea.js";

document.addEventListener("DOMContentLoaded", function() {
    
    let signupTab = document.getElementById("signup-tab");
    let profilePictureInput = document.getElementById("profile-picture-input");
    let profilePictureBtn = document.getElementById("profile-picture-btn");
    
    // Show signup tab
    signupTab.addEventListener("click", () => {
        document.getElementById("login-form").style.display = "none";
        document.getElementById("profile-container").style.display = "none";
        document.getElementById("signup-container").style.display = "block";
    });

    // Show login tab
    document.querySelectorAll(".login-tab").forEach(tab => {
        tab.addEventListener("click", () => {
            document.getElementById("login-form").style.display = "block";
            document.getElementById("signup-container").style.display = "none";
            document.getElementById("profile-container").style.display = "none";
        });
    });
    
    // Event listener for profile picture image btn
    profilePictureBtn.addEventListener('click', function () {
        // Make the input active and call click()
        profilePictureInput.removeAttribute("disabled");
        profilePictureInput.click();
      });
      
    // Event listener for profile picture file input
    profilePictureInput.addEventListener("change", function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profile-picture-img").src = e.target.result; // Update image preview
            };
            reader.readAsDataURL(file);
        }
    });

    // Responsive textarea (#bio-textarea)
    document.querySelectorAll(".responsive-textarea").forEach(textarea => {
        textarea.addEventListener("input", () => {
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);
        });
    });
    
    // Signup form AJAX for add_user.php
    document.getElementById("signup-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission
    
            const formData = new FormData(this);
    
            // Debugging - Log the values
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            fetch("actions/add_user.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error adding user: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
    });

    // Add profile AJAX for add_profile.php
    document.getElementById("profile-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission

            // Need to validate that the profile picture was added before completing the request!
            const imageInput = document.getElementById("profile-picture-input");
            const file = imageInput.files[0];

            if (!file) {
                alert("Please upload a profile picture before continuing.");
                return;
            }

            const formData = new FormData(this);
    

            fetch("actions/add_profile.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error adding profile: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
    });

});