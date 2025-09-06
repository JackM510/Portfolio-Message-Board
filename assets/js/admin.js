document.addEventListener("DOMContentLoaded", () => {

// Declare module variables
let validateCheckboxes;
let fadeEl;
// Load JS modules dynamically
import(window.API.jsCheckboxes)
    .then(mod => validateCheckboxes = mod.validateCheckboxes)
    .catch(err => console.error("Checkbox module failed to load:", err));

import(window.API.jsFadeEl)
    .then(mod => fadeEl = mod.fadeEl)
    .catch(err => console.error("FadeEl module failed to load:", err));

// Hide any echo flashes 
function hideEchoFlash() {
    document.querySelectorAll('.success-flash').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.error-flash').forEach(el => el.style.display = 'none');
}

// Format joined date field
function formatJoinedDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Add checkbox and delete button event listener
function attachEvents() {
    const checkboxes = document.querySelectorAll('.required-checkbox');
    const deleteBtn = document.getElementById('delete-btn');
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => validateCheckboxes(deleteBtn, checkboxes));
        });
}

const profileSearch = document.getElementById("user-search");
const profileView = document.getElementById("view-profile");
const resetEmailForm = document.getElementById("reset-email-form");
const resetPasswordForm = document.getElementById("reset-pw-form");
const deleteUserForm = document.getElementById("delete-user-form");
const returnSearchBtn = document.getElementById("return-btn");
// Fields & Input variables
const userSearch = document.getElementById("user-search-input");
const firstNameField = document.getElementById("first-name-input");
const lastNameField = document.getElementById("last-name-input");
const userIdField = document.getElementById("userid-input");
const profileIdField = document.getElementById("profileid-input");
const emailField = document.getElementById("email-input");
const joinedDateField = document.getElementById("joined-date-input");
const hiddenEmailInput = document.getElementById("hidden-email-input");
const hiddenPasswordInput = document.getElementById("hidden-pw-input");
const hiddenDeleteUserInput = document.getElementById("hidden-delete-userid");
const hiddenDeleteProfileInput = document.getElementById("hidden-delete-profileid");

// search bar event listener
userSearch.addEventListener("input", function() {
    const searchTerm = this.value.toLowerCase();
    document.querySelectorAll(".user-row").forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? "" : "none";
    });
});

// Ajax to return a users profile information 
document.querySelectorAll(".user-row").forEach(row => {
    row.addEventListener("click", () => {
    const userId = row.dataset.userId;

    fetch(API.getUser, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ user_id: userId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const u = data.user;
            // Set users details
            firstNameField.value = u.first_name;
            lastNameField.value = u.last_name;
            userIdField.value = u.user_id;
            profileIdField.value = u.profile_id;
            emailField.value = u.email;
            joinedDateField.value = formatJoinedDate(u.created_at);
            // Pass users user_id to each form
            hiddenEmailInput.value = u.user_id;
            hiddenPasswordInput.value = u.user_id;
            hiddenDeleteUserInput.value = u.user_id;
            hiddenDeleteProfileInput.value = u.profile_id
            attachEvents(); // Event Listeners for delete account checkboxes
            profileSearch.style.display = "none";
            profileView.style.display = "block";
            fadeEl(profileView);
            hideEchoFlash(); // Hide any prev visible echo flash
            closeAllPanels(); // Close all accordian panels when profile loaded
        } else {
            alert("User not found.");
        }
    });
    });
});

// AJAX for returning a users profile information after their profile_id is stored in session data
const storedId = sessionStorage.getItem("selectedProfileId");
if (storedId) {
    fetch(API.getUser, {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: new URLSearchParams({ user_id: storedId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            const u = data.user;
            // Repopulate view-profile fields
            firstNameField.value = u.first_name;
            lastNameField.value = u.last_name;
            userIdField.value = u.user_id;
            profileIdField.value = u.profile_id;
            emailField.value = u.email;
            joinedDateField.value = formatJoinedDate(u.created_at);
            // Pass users user_id to each form
            hiddenEmailInput.value = u.user_id;
            hiddenPasswordInput.value = u.user_id;
            hiddenDeleteUserInput.value = u.user_id;
            hiddenDeleteProfileInput.value = u.profile_id
            attachEvents(); // Event Listeners for delete account checkboxes
            profileSearch.style.display = "none";
            profileView.style.display = "block";
        }
        sessionStorage.removeItem("selectedProfileId");
    });
}

// Return btn to user_search
returnSearchBtn.addEventListener("click", () => {
    profileView.style.display = "none";
    profileSearch.style.display = "block";
    fadeEl(profileSearch);
});

// Ajax for updating a users email
resetEmailForm.addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch(API.updateEmail, {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const userId = hiddenEmailInput.value;
        sessionStorage.setItem("selectedProfileId", userId);
        location.reload();
        sessionStorage.setItem("openPanel", "collapse-email");
    })
    .catch(error => console.error("Fetch Error:", error));
});


// Ajax for reseting a users password
resetPasswordForm.addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch(API.updatePassword, {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        const profileId = hiddenPasswordInput.value;
        sessionStorage.setItem("selectedProfileId", profileId);
        location.reload();
        sessionStorage.setItem("openPanel", "collapse-pw");
    })
    .catch(error => console.error("Fetch Error:", error));
});

// Ajax for deleting a users account
deleteUserForm.addEventListener("submit", function(event) {
    event.preventDefault();
    const formData = new FormData(this);

    fetch(API.deleteUser, {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            location.reload(); // Back to search
        } else {
            const userId = hiddenDeleteUserInput.value;
            sessionStorage.setItem("selectedProfileId", userId);
            location.reload(); // Stay on user profile and flash echo
            sessionStorage.setItem("openPanel", "collapse-delete-account");
        }
    })
    .catch(error => console.error("Fetch Error:", error));
    });
});