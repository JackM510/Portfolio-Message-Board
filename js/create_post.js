document.addEventListener("DOMContentLoaded", function () {  
    
    // New post functionality
    const container = document.getElementById("new-post-form");
    const textarea = document.getElementById("new-post-textarea");
    const postImg = document.getElementById("new-post-img");
    const imgUpload = document.getElementById("image-upload");
    const imgUploadBtn = document.getElementById("image-upload-btn");
    const buttonGroup = document.getElementById("new-post-btn-group");

    // New post buttons visible
    if (textarea) {
        textarea.addEventListener("click", function () {
            imgUploadBtn.style.display ="block";
            buttonGroup.style.display = "flex";
        });
    }

    if (imgUpload) {
        imgUpload.addEventListener("change", function () {
            if (this.files.length > 0) { // Check if a file was uploaded
                buttonGroup.style.display = "flex";
            }
        });
    }

    // Hide new post components when clicked outside of new post layout
    document.addEventListener("click", function (event) {
        if (!container.contains(event.target)) {
            postImg.src = "";
            imgUpload.value = "";
            textarea.value = "";
            imgUploadBtn.style.display = "none";
            buttonGroup.style.display = "none";
            document.getElementById("new-post-form").reset();
        }
    });

    // New post cancel button
    document.getElementById("cancel-post-btn").addEventListener("click", function () {
        postImg.src = "";
        imgUpload.value = "";
        textarea.value = "";
        imgUploadBtn.style.display = "none";
        buttonGroup.style.display = "none";
        document.getElementById("new-post-form").reset();
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
        } else {
            document.getElementById("new-post-img").src = e.target.result; 
        }
    });

    // Add a post AJAX for add_post.php
    document.getElementById("new-post-form").addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission

            const formData = new FormData(this);
    
            // Debugging - Log the values
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }

            fetch("actions/add_post.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error adding post: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
            
    });
    
});