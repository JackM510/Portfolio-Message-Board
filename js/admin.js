function validateCheckboxes(deleteBtn, checkboxes) {
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    if (allChecked) {
        deleteBtn.classList.remove("disabled");
    } else {
        deleteBtn.classList.add("disabled");
    }
}

function hideEchoFlash() {
    document.querySelectorAll('.success-flash').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.error-flash').forEach(el => el.style.display = 'none');
}

function closeAllPanels() {
    document.querySelectorAll('.accordion .collapse').forEach(panel => {
        if (panel.classList.contains('show')) {
            panel.classList.remove('show');
        }
    });
}



document.addEventListener("DOMContentLoaded", () => {
    const profileSearch = document.getElementById("user-search");
    const profileView = document.getElementById("view-profile");
   
    // search bar event listener
    document.getElementById("user-search-input").addEventListener("input", function() {
        const searchTerm = this.value.toLowerCase();
        document.querySelectorAll(".user-row").forEach(row => {
          const rowText = row.textContent.toLowerCase();
          row.style.display = rowText.includes(searchTerm) ? "" : "none";
        });
      });

    // Ajax for returning a users profile information 
    document.querySelectorAll(".user-row").forEach(row => {
        row.addEventListener("click", () => {
        const profileId = row.dataset.userId;
    
        fetch("actions/get_profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ profile_id: profileId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const u = data.user;
                // Set users details
                document.getElementById("profile-picture-img").src = u.profile_picture;
                document.querySelector("#first-name-input").value = u.first_name;
                document.querySelector("#last-name-input").value = u.last_name;
                document.querySelector("#profileid-input").value = u.profile_id;
                document.querySelector("#email-input").value = u.email;
                document.querySelector("#role-input").value = u.role;
                document.querySelector("#joined-date-input").value = u.created_at;

                // Pass the users profile_id to each form
                document.querySelector("#hidden-email-input").value = u.profile_id;
                document.querySelector("#hidden-pw-input").value = u.profile_id;
                document.querySelector("#hidden-delete-input").value = u.profile_id;

                // Event Listeners for delete account checkboxes
                const checkboxes = document.querySelectorAll('.required-checkbox');
                const deleteBtn = document.getElementById('delete-btn');
                checkboxes.forEach(cb => {
                    cb.addEventListener('change', () => validateCheckboxes(deleteBtn, checkboxes));
                  });
                  
   
                // Update view
                closeAllPanels();
                hideEchoFlash();
                
                document.getElementById("user-search").style.display = "none";
                document.getElementById("view-profile").style.display = "block";
            } else {
            alert("User not found.");
            }
        });
        });
    });

    // AJAX for returning a users profile information after their profile_id is stored in session data
    const storedProfileId = sessionStorage.getItem("selectedProfileId");
    if (storedProfileId) {
        fetch("actions/get_profile.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: new URLSearchParams({ profile_id: storedProfileId })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
            const u = data.user;
            // Repopulate view-profile fields
            document.getElementById("profile-picture-img").src = u.profile_picture;
            document.querySelector("#first-name-input").value = u.first_name;
            document.querySelector("#last-name-input").value = u.last_name;
            document.querySelector("#profileid-input").value = u.profile_id;
            document.querySelector("#email-input").value = u.email;
            document.querySelector("#role-input").value = u.role;
            document.querySelector("#joined-date-input").value = u.created_at;
            document.querySelector("#hidden-email-input").value = u.profile_id;
            document.querySelector("#hidden-pw-input").value = u.profile_id;
            document.querySelector("#hidden-delete-input").value = u.profile_id;

            document.getElementById("user-search").style.display = "none";
            document.getElementById("view-profile").style.display = "block";
            }
            // Optional: clear sessionStorage after use
            sessionStorage.removeItem("selectedProfileId");
        });
    }

    // Return btn to user_search
    document.getElementById("return-btn").addEventListener("click", () => {
        // Update view
        document.getElementById("user-search").style.display = "block";
        document.getElementById("view-profile").style.display = "none";
    });

    // Keep track of any open accordian cards
    window.addEventListener("DOMContentLoaded", () => {
        const id = sessionStorage.getItem("openPanel");
        if (id) {
          const el = document.getElementById(id);
          if (el) {
            new bootstrap.Collapse(el, {
              toggle: true
            });
            
            // Wait briefly for it to expand, then scroll into view
            setTimeout(() => {
                el.scrollIntoView({ behavior: "smooth", block: "start" });
            }, 300); // Delay allows accordion animation to complete
          }
          sessionStorage.removeItem("openPanel");
        }
    });

    // Ajax for updating a users email address
    document.getElementById("reset-email-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission

        const formData = new FormData(this);

        fetch("actions/update_email.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); // Log server response

            const profileId = document.getElementById("hidden-email-input").value;
            sessionStorage.setItem("selectedProfileId", profileId);
            sessionStorage.setItem("openPanel", "collapse-email"); // if needed
            location.reload();
        })
        .catch(error => console.error("Fetch Error:", error));
    });


    // Ajax for reseting a users password
    document.getElementById("reset-pw-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission

        const formData = new FormData(this);

        fetch("actions/update_password.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); // Log server response

            const profileId = document.getElementById("hidden-pw-input").value;
            sessionStorage.setItem("selectedProfileId", profileId);
            sessionStorage.setItem("openPanel", "collapse-pw"); // if needed
            location.reload();
        })
        .catch(error => console.error("Fetch Error:", error));
    });

    // Ajax for deleting a users account
    document.getElementById("delete-user-form").addEventListener("submit", function(event) {
        event.preventDefault(); // Stop default form submission

        const formData = new FormData(this);

        fetch("actions/delete_user.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            console.log("Server Response:", data); // Log server response
            location.reload();
        })
        .catch(error => console.error("Fetch Error:", error));
    });
    
    
});




  
  