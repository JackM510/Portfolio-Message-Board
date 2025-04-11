document.addEventListener("DOMContentLoaded", function () {  
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
});