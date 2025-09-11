document.addEventListener("DOMContentLoaded", function() {
    
    const signup = document.getElementById("signup-container");
    const login = document.getElementById("login-container");
    const profile = document.getElementById("profile-container");
    const signupTab = document.getElementById("signup-tab");
    const signupForm = document.getElementById("signup-form");
    const profileForm = document.getElementById("profile-form");
    const profilePictureInput = document.getElementById("profile-picture-input");
    const profilePictureBtn = document.getElementById("profile-picture-btn");
    const profilePictureImg = document.getElementById("profile-picture-img");

    // Declare module variables
    let predictLines;
    let fadeEl;
    import(window.API.jsPredictLines)
        .then(mod => predictLines = mod.predictLines)
        .catch(err => console.error("Predict lines module failed to load:", err));
    
    // FadeEl after page load
    import(window.API.jsFadeEl)
        .then(mod => {
        fadeEl = mod.fadeEl;
            // Check which tab to fade in
            const target = sessionStorage.getItem("fadeEl");
            if (target) {
                switch (target) {
                    case "signup":
                        fadeEl(signup);
                        break;
                    case "profile":
                        fadeEl(profile);
                        break;
                }
                sessionStorage.removeItem("fadeEl");
            }
        })
        .catch(err => console.error("FadeEl module failed to load:", err));
    
    // Show signup tab
    signupTab.addEventListener("click", () => {
        login.style.display = "none";
        profile.style.display = "none";
        signup.style.display = "block";
        fadeEl(signup);
    });

    // Show login tab
    document.querySelectorAll(".login-tab").forEach(tab => {
        tab.addEventListener("click", () => {
            signup.style.display = "none";
            profile.style.display = "none";
            login.style.display = "block";
            fadeEl(login);
        });
    });
    
    // Profile picture image btn
    profilePictureBtn.addEventListener('click', function () {
        profilePictureInput.removeAttribute("disabled");
        profilePictureInput.click();
      });
      
    // Profile picture file input
    profilePictureInput.addEventListener("change", function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                profilePictureImg.src = e.target.result; // Show img when uploaded
            };
            reader.readAsDataURL(file);
        }
    });

    // Responsive textarea (#bio-textarea)
    document.querySelectorAll("textarea").forEach(textarea => {
        textarea.addEventListener("input", () => {
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);
        });
    });
    
    // Signup form AJAX for add_user.php
    signupForm.addEventListener("submit", function(event) {
        event.preventDefault();
        const formData = new FormData(this);
        fetch(API.addUser, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                location.reload();
            } else {
                sessionStorage.setItem("fadeEl", "signup");
                location.reload();
            }
        })
        .catch(error => console.error("Fetch Error:", error));
    });

    // Add profile AJAX for add_profile.php
    profileForm.addEventListener("submit", function(event) {
        event.preventDefault();
        // Check a profile picture was added before proceeding
        const file = profilePictureInput.files[0];
        if (!file) {
            alert("Please upload a profile picture.");
            return;
        }

        const formData = new FormData(this);
        fetch(API.addProfile, {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data.trim() === "success") {
                location.reload();
            } else {
                sessionStorage.setItem("fadeEl", "profile");
                location.reload();
            }
        })
        .catch(error => console.error("Fetch Error:", error));
    });
});