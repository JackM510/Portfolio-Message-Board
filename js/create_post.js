document.addEventListener("DOMContentLoaded", function () {  
    
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

    // Set img-upload as the image uploaded by the user
    document.getElementById("image-upload").addEventListener("change", function(event) {
        const file = event.target.files[0]; // Get the selected file

        if (file) {
            const reader = new FileReader(); // Create a FileReader object
            reader.onload = function(e) {
                document.getElementById("new-post-img").src = e.target.result; // Set the image source to the selected file
            };
            reader.readAsDataURL(file); // Convert the file into a Data URL
        }
    });
});