import { predictLines } from "./utils/textarea.js";

document.addEventListener("DOMContentLoaded", function () {  
    
    // New post functionality
    const newPostForm = document.getElementById("new-post-form");
    const textarea = document.getElementById("new-post-textarea");
    const postImg = document.getElementById("new-post-img");
    const imgUpload = document.getElementById("image-upload");
    const imgUploadBtn = document.getElementById("image-upload-btn");
    const buttonGroup = document.getElementById("new-post-btn-group");
    const cancelPostBtn = document.getElementById("cancel-post-btn");

    // New post buttons visible
    if (textarea) {
        textarea.addEventListener("click", function () {
            imgUploadBtn.style.display ="block";
            buttonGroup.style.display = "flex";
        });

        // Textarea height event listener
        textarea.addEventListener("input", () => {
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);     
        });
    }

    if (imgUpload) {
        // Check if a file was uploaded by the user
        if (imgUpload) {
            imgUpload.addEventListener("change", function (event) {
                if (event.target.files.length > 0) {
                    const file = event.target.files[0];
                    const reader = new FileReader();
        
                    reader.onload = function(e) {
                        postImg.src = e.target.result;
                        postImg.style.display = "block";
                    };
        
                    reader.readAsDataURL(file);
                    buttonGroup.style.display = "flex";
                } else {
                    postImg.src = "";
                    postImg.style.display = "none";
                }
            });
        }
        
    }

    if (cancelPostBtn) {
        // New post cancel button
        cancelPostBtn.addEventListener("click", function () {
            postImg.src = "";
            postImg.style.display = "none";
            imgUpload.value = "";  

            textarea.value = "";
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);

            imgUploadBtn.style.display = "none";
            buttonGroup.style.display = "none";
            document.getElementById("new-post-form").reset();
        });
    }

    // Hide new post components when clicked outside of new post layout
    document.addEventListener("click", function (event) {
        if (!newPostForm.contains(event.target)) {
            postImg.src = "";
            postImg.style.display = "none";
            imgUpload.value = "";
            textarea.value = "";
            
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);

            imgUploadBtn.style.display = "none";
            buttonGroup.style.display = "none";
            document.getElementById("new-post-form").reset();
        }
    });

    // Add a post AJAX for add_post.php
    if (newPostForm) {
        newPostForm.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission

            const formData = new FormData(this);

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
    }
});