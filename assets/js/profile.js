document.addEventListener("DOMContentLoaded", function () {
    // Declare module variables
    let predictLines;
    let fadeEl;
    // Load JS modules dynamically
    import(window.API.jsPredictLines)
        .then(mod => predictLines = mod.predictLines)
        .catch(err => console.error("Predict lines module failed to load:", err));

    import(window.API.jsFadeEl)
        .then(mod => fadeEl = mod.fadeEl)
        .catch(err => console.error("FadeEl module failed to load:", err));

    // Profile details elements
    const details = document.getElementById("profile-details");
    const profileForm = document.getElementById("profile-form");
    const editIcon = document.getElementById("edit-icon");
    
    // Update profile elements
    const updateForm = document.getElementById("update-profile");
    const cancelBtn = document.getElementById("profile-cancel-btn");
    const profilePicImg = document.getElementById("profile-picture-img");
    const profilePicBtn = document.getElementById("profile-picture-btn");
    const profilePicInput = document.getElementById("profile-picture-input");  
    const firstNameInput = document.getElementById("first-name-input");
    const lastNameInput = document.getElementById("last-name-input");
    const occupationInput = document.getElementById("occupation-input");
    const locationInput = document.getElementById("location-input");
    const bioTextarea = document.getElementById("bio-textarea");
    let originalValues = {};

    // Cancel editing update profile details
    function cancelEdit() {
        // Add disabled attribute to form elements
        firstNameInput.setAttribute("disabled", "true");
        lastNameInput.setAttribute("disabled", "true");
        occupationInput.setAttribute("disabled", "true");
        locationInput.setAttribute("disabled", "true");
        bioTextarea.setAttribute("disabled", "true");
        // Assign original values
        firstNameInput.value = originalValues.first;
        lastNameInput.value = originalValues.last;
        occupationInput.value = originalValues.occupation;
        locationInput.value = originalValues.location;
        bioTextarea.value = originalValues.bio;
        // Hide Update profile
        updateForm.style.display = 'none';
        updateForm.classList.remove("d-flex", "flex-column", "justify-content-center");
        // Display profile details
        details.style.display = 'block'; 
        details.classList.add("d-flex");
        fadeEl(details);   
    }
    
    // If click outside update profile
    function editOutsideClick(e) {
        if (!updateForm.contains(e.target)) {
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
            // Show update profile
            updateForm.style.display = 'block';
            updateForm.classList.add("d-flex", "flex-column", "justify-content-center");
            fadeEl(updateForm);
            // Textarea height
            const lines = predictLines(bioTextarea);
            bioTextarea.setAttribute("rows", lines);
            // Listen for clicks outside of profile form
            document.addEventListener("click", editOutsideClick);
        });
    }

    // Cancel btn when editing profile details
    if (cancelBtn) {
        cancelBtn.addEventListener("click", cancelEdit);
    }    
    
    // Event listener for profile picture image btn
    if (profilePicBtn) {
        profilePicBtn.addEventListener('click', function () {
            profilePicInput.removeAttribute("disabled");
            profilePicInput.click();
        });
    }
    
    // Event listener for profile picture input
    if (profilePicInput) {
        profilePicInput.addEventListener("change", function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicImg.src = e.target.result; // Update image preview
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Edit profile AJAX for edit_profile.php
    if (profileForm) {
        profileForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch(API.editProfile, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error editing profile: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    }
});