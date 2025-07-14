import { predictLines } from "./utils/textarea.js";

document.addEventListener("DOMContentLoaded", function () {

    // Profile container elements
    const form = document.getElementById("update-profile");
    const details = document.getElementById("profile-details");

    // Profile form elements
    let profilePictureInput = document.getElementById("profile-picture-input");
    let firstNameInput = document.getElementById("first-name-input");
    let lastNameInput = document.getElementById("last-name-input");
    let occupationInput = document.getElementById("occupation-input");
    let locationInput = document.getElementById("location-input");
    let bioTextarea = document.getElementById("bio-textarea");
    let originalValues = {};

    // Profile details edit icon
    document.getElementById("edit-icon").addEventListener("click", function () {

        // Get original values of inputs
        originalValues = {
            first: firstNameInput.value,
            last: lastNameInput.value,
            occupation: occupationInput.value,
            location: locationInput.value,
            bio: bioTextarea.value
        };

        //Remove disabled attribute from form elements
        firstNameInput.removeAttribute("disabled");
        lastNameInput.removeAttribute("disabled");
        occupationInput.removeAttribute("disabled");
        locationInput.removeAttribute("disabled");
        bioTextarea.removeAttribute("disabled");

        // Hide profile details
        details.style.display = 'none';
        details.classList.remove("d-flex");
        // Show the 'update profile' form and hide the profile details div
        form.style.display = 'block'; // Display 'Update Profile' form
        form.classList.add("d-flex", "flex-column", "justify-content-center");
        // Textarea height
        const lines = predictLines(bioTextarea);
        bioTextarea.setAttribute("rows", lines);
    });

   

    // Cancel button when editing profile details
    document.getElementById("profile-cancel-btn").addEventListener("click", function () {

        // Add disabled attribute to form elements
        firstNameInput.setAttribute("disabled", "true");
        lastNameInput.setAttribute("disabled", "true");
        occupationInput.setAttribute("disabled", "true");
        locationInput.setAttribute("disabled", "true");
        bioTextarea.setAttribute("disabled", "true");

        firstNameInput.value = originalValues.first;
        lastNameInput.value = originalValues.last;
        occupationInput.value = originalValues.occupation;
        locationInput.value = originalValues.location;
        bioTextarea.value = originalValues.bio;

        details.style.display = 'block'; // Display profile details
        details.classList.add("d-flex");
        form.style.display = 'none'; // Hide 'Update Profile' form
        form.classList.remove("d-flex", "flex-column", "justify-content-center");
    });

    // Event listener for profile picture image btn
    document.getElementById('profile-picture-btn').addEventListener('click', function () {
        // Make the input active and call click()
        profilePictureInput.removeAttribute("disabled");
        profilePictureInput.click();
      });
      
    // Event listener for profile picture file input
    document.getElementById("profile-picture-input").addEventListener("change", function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profile-picture-img").src = e.target.result; // Update image preview
            };
            reader.readAsDataURL(file);
        }
    });

    // Edit profile AJAX for edit_profile.php
    document.getElementById("profile-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission

            const formData = new FormData(this);

            fetch("actions/edit_profile.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    //details.scrollIntoView({ behavior: 'smooth'});
                    location.reload();
                    //window.scrollTo({ top: 0, behavior: 'smooth' });
                    

                } else {
                    alert("Error editing profile: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
    });

});