import { fadeEl } from "./utils/page_transitions.js";
import { predictLines } from "./utils/textarea.js";

document.addEventListener("DOMContentLoaded", function () {

    // Profile container elements
    const details = document.getElementById("profile-details");
    const form = document.getElementById("update-profile");
    // Profile Details elements
    let editIcon = document.getElementById("edit-icon");
    
    // Profile form elements
    let cancelBtn = document.getElementById("profile-cancel-btn");
    let profilePicBtn = document.getElementById("profile-picture-btn");
    let profilePicInput = document.getElementById("profile-picture-input");
    let profileForm = document.getElementById("profile-form");

    // Profile form input elements
    let profilePictureInput = document.getElementById("profile-picture-input");
    let firstNameInput = document.getElementById("first-name-input");
    let lastNameInput = document.getElementById("last-name-input");
    let occupationInput = document.getElementById("occupation-input");
    let locationInput = document.getElementById("location-input");
    let bioTextarea = document.getElementById("bio-textarea");
    let originalValues = {};

    function cancelEdit() {
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
        
        fadeEl(details);

         form.style.display = 'none'; // Hide 'Update Profile' form
         form.classList.remove("d-flex", "flex-column", "justify-content-center");
    }
    
    function editOutsideClick(e) {
        // If click target is NOT inside the form or the edit icon...
        if (!form.contains(e.target)) {
            cancelEdit();
        }
    }
    
    // Profile details edit icon
    if (editIcon) {
        editIcon.addEventListener("click", function (e) {
            e.stopPropagation();
            
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

            fadeEl(form);

            // Textarea height
            const lines = predictLines(bioTextarea);
            bioTextarea.setAttribute("rows", lines);

            // Listen for clicks outside of the profile form area to hide the layout
            document.addEventListener("click", editOutsideClick);
        });
    }

    // Cancel button when editing profile details
    if (cancelBtn) {
        cancelBtn.addEventListener("click", cancelEdit);
    }    
    
    // Event listener for profile picture image btn
    if (profilePicBtn) {
        profilePicBtn.addEventListener('click', function () {
            profilePictureInput.removeAttribute("disabled");
            profilePictureInput.click();
        });
    }
    
    // Event listener for profile picture file input
    if (profilePicInput) {
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
    }
    
    // Edit profile AJAX for edit_profile.php
    if (profileForm) {
        document.getElementById("profile-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission

            const formData = new FormData(this);

            fetch(API.editProfile, {
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
    }
});