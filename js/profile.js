document.addEventListener("DOMContentLoaded", function () {  
    // Profile details edit icon
    document.getElementById("edit-icon").addEventListener("click", function () {
        let profileName = document.getElementById("profile-name");
        let profileLocation = document.getElementById("profile-location");
        let profileBio = document.getElementById("profile-bio");
    
        if (!this.classList.contains("editing")) {
            // Enable editing
            profileName.removeAttribute("disabled");
            profileLocation.removeAttribute("disabled");
            profileBio.removeAttribute("disabled");
    
            // Change edit icon to save (tick)
            this.innerHTML = '<i class="bi bi-check-lg"></i>';
            this.classList.add("editing");
        } else {
            // Submit form when tick is clicked
            document.getElementById("profile-form").submit();
        }
    });


    const textarea = document.getElementById("new-post-textarea");
    const buttonGroup = document.getElementById("new-post-btn-group");

    document.getElementById("new-post-textarea").addEventListener("click", function () {
        buttonGroup.style.display = "block";
    });

    document.addEventListener("click", function(event) {
        if (!textarea.contains(event.target) && !buttonGroup.contains(event.target)) {
            buttonGroup.style.display = "none";
        }
    });

    // New post cancel button
    document.getElementById("cancel-post-btn").addEventListener("click", function () {
        textarea.value = "";
        buttonGroup.style.display = "none";
    });
});