document.addEventListener("DOMContentLoaded", function () {  
    // Declare module variables
    let predictLines;
    // Load JS modules dynamically
    import(window.API.jsPredictLines)
        .then(mod => predictLines = mod.predictLines)
        .catch(err => console.error("Predict lines module failed to load:", err));

    // New post elements
    const newPostForm = document.getElementById("new-post-form");
    const textarea = document.getElementById("new-post-textarea");
    const postImg = document.getElementById("new-post-img");
    const imgUpload = document.getElementById("image-upload");
    const imgUploadBtn = document.getElementById("image-upload-btn");
    const buttonGroup = document.getElementById("new-post-btn-group");
    const cancelPostBtn = document.getElementById("cancel-post-btn");

    // Cancel a new post
    function cancelNewPost() {
        postImg.src = "";
        postImg.style.display = "none";
        imgUpload.value = "";  
        textarea.value = "";
        const lines = predictLines(textarea);
        textarea.setAttribute("rows", lines);
        imgUploadBtn.style.display = "none";
        buttonGroup.style.display = "none";
        newPostForm.reset();
    }

    // New post buttons visible
    if (textarea) {
        textarea.addEventListener("click", function () {
            imgUploadBtn.style.display ="block";
            buttonGroup.style.display = "flex";
        });

        // Textarea height
        textarea.addEventListener("input", () => {
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);     
        });
    }

    // Trigger imgUpload input when img button is clicked
    if (imgUploadBtn && imgUpload) {
        imgUploadBtn.addEventListener("click", () => {
            imgUpload.click();
        });
    }

    // Check if img was uploaded
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

    // New post cancel button
    if (cancelPostBtn) {  
        cancelPostBtn.addEventListener("click", function () {       
            cancelNewPost();
        });
    }

    // Click outside of new post section
    document.addEventListener("click", function (event) {
        if (!newPostForm.contains(event.target)) {
            cancelNewPost();
        }
    });

    // Add a post AJAX for add_post.php
    if (newPostForm) {
        newPostForm.addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);

            fetch(API.addPost, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
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