document.addEventListener("DOMContentLoaded", function () {  
    // Profile details edit icon
    document.getElementById("edit-icon").addEventListener("click", function () {
        let profilePicture = document.getElementById("profile-image-upload");
        let profileName = document.getElementById("profile-name");
        let profileLocation = document.getElementById("profile-location");
        let profileBio = document.getElementById("profile-bio");
    
        if (!this.classList.contains("editing")) {
            // Enable editing
            profilePicture.removeAttribute("disabled");
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


    // New post functionality
    const textarea = document.getElementById("new-post-textarea");
    const postImg = document.getElementById("new-post-img");
    const imgUpload = document.getElementById("image-upload");
    const buttonGroup = document.getElementById("new-post-btn-group");

    // New post buttons visible
    textarea.addEventListener("click", function () {
        buttonGroup.style.display = "flex";
    });

    imgUpload.addEventListener("change", function () {
        if (this.files.length > 0) { // Check if a file was uploaded
            buttonGroup.style.display = "flex";
        }
    });
    
    // New post buttons hidden
    document.addEventListener("click", function(event) {
        if (!textarea.contains(event.target) && !buttonGroup.contains(event.target)) {
            if (textarea.value === "" && postImg.src === "") {
                buttonGroup.style.display = "none";
            }
        }
    });

    // New post cancel button
    document.getElementById("cancel-post-btn").addEventListener("click", function () {
        postImg.src = "";
        imgUpload.value = "";
        textarea.value = "";
        buttonGroup.style.display = "none";
        //document.getElementById("new-post-form").reset();
    });
});