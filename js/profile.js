document.addEventListener("DOMContentLoaded", function () {  
    // Profile details edit icon
    document.getElementById("edit-icon").addEventListener("click", function () {

        let profilePictureImg = document.getElementById("profile-picture");
        let originalImgSrc = profilePictureImg.src;

        let profilePicture = document.getElementById("profile-image-upload");
        let profileName = document.getElementById("profile-name");
        let profileLocation = document.getElementById("profile-location");
        let profileBio = document.getElementById("profile-bio");

        let editIcon = document.getElementById('edit-icon');
        let cancelIcon = document.getElementById('cancel-icon');
    
        if (!this.classList.contains("editing")) {
            // Enable editing
            profilePicture.removeAttribute("disabled");
            profileName.removeAttribute("disabled");
            profileLocation.removeAttribute("disabled");
            profileBio.removeAttribute("disabled");
    
            // Change edit icon to save (tick)
            this.innerHTML = '<i class="bi bi-check-lg"></i>';
            this.classList.add("editing");
            this.style = "color:green";

            // close icon
            cancelIcon.style.display = "block";

        } else {
            // Submit form when tick is clicked
            document.getElementById("profile-details-submit").click();
        }

        cancelIcon.addEventListener("click", function () {
             // ✅ Reset form inputs to their original values
            document.getElementById("profile-form").reset();

            // ✅ Disable inputs again (returning to default state)
            profilePicture.setAttribute("disabled", "true");
            profileName.setAttribute("disabled", "true");
            profileLocation.setAttribute("disabled", "true");
            profileBio.setAttribute("disabled", "true");

            // ✅ Switch back to the pencil edit icon
            editIcon.innerHTML = '<i class="bi bi-pencil"></i>';
            editIcon.classList.remove("editing");
            editIcon.style = "color:black";

            // ✅ Hide the cancel icon again
            cancelIcon.style.display = "none";

            // ✅ Clear the file input (so users don't accidentally submit the wrong image)
            profilePicture.value = "";
            profilePictureImg.src = originalImgSrc;
        });

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