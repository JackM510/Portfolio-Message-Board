import { predictLines } from "./utils/textarea.js";

document.addEventListener("DOMContentLoaded", function() {

    // TA responsive height testing
    document.querySelectorAll(".responsive-textarea").forEach(textarea => {
        textarea.addEventListener("input", () => {
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);
        });
    });    
  
      
    // Add an event listener to each posts dropdown edit button
    document.querySelectorAll(".edit-post-btn").forEach(button => {
        const postId = button.getAttribute("data-post-id"); // Get post ID from button
        const postForm = document.querySelector(`#edit-post-form-${postId}`);
        const postDropdown = document.querySelector(`#post-dropdown-${postId}`);
        const img = document.querySelector(`#post-picture-${postId}`);
        const imgUploadBtn = document.querySelector(`#post-image-upload-btn-${postId}`);
        const paragraph = document.querySelector(`#post-description-${postId}`);
        const textarea = document.querySelector(`#post-textarea-${postId}`);
        const btnGroup = document.querySelector(`#edit-post-btn-group-${postId}`);
        
        // Show form elements and buttons to update the post
        button.addEventListener("click", function() {
            
            const imgSrc = img.getAttribute("src")?.trim();

            // Only treat it as valid if it's a real image file (not fallback or preview)
            const isValidImage = imgSrc &&
                !imgSrc.includes("index.php") &&
                !imgSrc.includes("profile.php") &&
                !imgSrc.includes("Post Image") &&
                !imgSrc.startsWith("data:") && // prevent preview from being stored
                /\.(jpg|jpeg|png|gif|webp)$/i.test(imgSrc);
            
            img.dataset.hasOriginalImage = isValidImage ? "true" : "false";
            
            if (isValidImage) {
                img.dataset.originalSrc = imgSrc;
                img.style.display = "block";
            } else {
                img.style.display = "none";
            }
            
            textarea.dataset.originalValue = textarea.value;
            
            imgUploadBtn.style.display ="block"; // Show img upload btn
            paragraph.style.display = "none";

            textarea.removeAttribute("hidden"); // Make textarea active
            textarea.removeAttribute("disabled"); // Make textarea active
            textarea.style.display = "block";
            textarea.focus();

            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);
            
            btnGroup.style.display = "block";
            btnGroup.classList.add("d-flex", "float-end");

            // Hide/restore elements if the user clicks outside of the edit post layout
            const outsideClickHandler = function (event) {
                if (!postDropdown.contains(event.target) && !postForm.contains(event.target)) {
                    
                    if (img.dataset.hasOriginalImage === "true") {
                        img.src = img.dataset.originalSrc;
                        img.style.display = "block";
                    } else {
                        img.src = ""; // clear anything accidental
                        img.style.display = "none";
                    }
                    
                    paragraph.style.display = "block";
                    textarea.setAttribute("hidden", "true");
                    textarea.setAttribute("disabled", "true");
                    textarea.value = textarea.dataset.originalValue;
                    
                    // Hide buttons
                    imgUploadBtn.style.display = "none";
                    btnGroup.style.display = "none";
                    btnGroup.classList.remove("d-flex", "float-end");
    
                    // Remove this listener so it doesn't fire again
                    document.removeEventListener("click", outsideClickHandler);
                }
            };
            document.addEventListener("click", outsideClickHandler);
        });
    });

    // Event listener for 'cancel' button when editing a post
    document.querySelectorAll(".edit-post-cancel-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postId = button.getAttribute("data-post-id"); // Get post ID from button
            const img = document.querySelector(`#post-picture-${postId}`);
            const imgUploadBtn = document.querySelector(`#post-image-upload-btn-${postId}`);
            const paragraph = document.querySelector(`#post-description-${postId}`);
            const textarea = document.querySelector(`#post-textarea-${postId}`); // Find correct textarea
            const buttonGroup = document.querySelector(`#edit-post-btn-group-${postId}`);

            if (textarea) {

                if (img.dataset.hasOriginalImage === "true") {
                    img.src = img.dataset.originalSrc;
                    img.style.display = "block";
                } else {
                    img.src = "";
                    img.style.display = "none";
                }
                
                paragraph.style.display = "block";

                textarea.value = textarea.dataset.originalValue; // Restore the original comment text
                textarea.setAttribute("hidden", "true");
                textarea.setAttribute("disabled", "true");
                
                
                imgUploadBtn.style.display ="none";
                buttonGroup.style.display = "none";
                buttonGroup.classList.remove("d-flex", "float-end");
            }
        });
    });

    

    // Event listener if image uploaded when editing a post
    document.querySelectorAll(".post-image-upload").forEach(input => {
        input.addEventListener("change", function(event) {
            const postId = input.id.replace("post-image-upload-", ""); // Extract post ID
            const imgTag = document.querySelector(`#post-picture-${postId}`);

            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
    
                reader.onload = function(e) {
                    imgTag.src = e.target.result; // Update image preview
                    imgTag.style.display = "block"; // Make sure image is visible
                };
    
                reader.readAsDataURL(file);
            }
        });
    });
    
    
    // Add an event listener to each add comment textarea on each post
     document.querySelectorAll(".add-comment-textarea").forEach(textarea => {
        const id = textarea.getAttribute("id").replace("add-comment-textarea-", "");
        const buttonGroup = document.querySelector(`#add-comment-btns-${id}`);

        textarea.addEventListener("click", function() {
            buttonGroup.style.display = "block";
            buttonGroup.classList.add("d-flex", "float-end");
        });

        document.addEventListener("click", function(event) {
            if (!textarea.contains(event.target) && !buttonGroup.contains(event.target)) {
                textarea.value = "";
                buttonGroup.style.display = "none";
                buttonGroup.classList.remove("d-flex", "float-end");
            }
        });
    });

    // Add event listener to each cancel comment btn on each post
    document.querySelectorAll(".cancel-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postId = button.getAttribute("data-post-id"); // Get post ID from button
            const textarea = document.querySelector(`#add-comment-textarea-${postId}`); // Find correct textarea
            const buttonGroup = document.querySelector(`#add-comment-btns-${postId}`);

            if (textarea) {
                textarea.value = "";
                buttonGroup.style.display = "none";
                buttonGroup.classList.remove("d-flex", "float-end");
            }
        });
    });


    // Event listener for edit btn in comment dropdown
    document.querySelectorAll(".edit-btn").forEach(button => {
        const postId = button.getAttribute("data-post-id");
        const paragraph = document.querySelector(`#comment-description-${postId}`);
        const textarea = document.querySelector(`#comment-textarea-${postId}`);
        const commentDropdown = document.querySelector(`#comment-dropdown-${postId}`);
        const buttonGroup = document.querySelector(`#edit-comment-btns-${postId}`);

        // Store the original value before editing - used when edit-cancel-btn selected
        textarea.dataset.originalValue = textarea.value;

        button.addEventListener("click", function() {

            paragraph.style.display = "none";

            // Make textarea active
            textarea.removeAttribute("disabled");
            textarea.removeAttribute("hidden");
            textarea.style.display = "block";
            textarea.focus();

            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);

            // Show the button group
            buttonGroup.style.display = "block";
            buttonGroup.classList.add("d-flex", "float-end");
            
        });

        document.addEventListener("click", function(event) {
            if (!textarea.contains(event.target) && !commentDropdown.contains(event.target) && !buttonGroup.contains(event.target)) {

                paragraph.style.display = "block";

                textarea.setAttribute("hidden", "true");
                textarea.setAttribute("disabled", "true");
                textarea.value = textarea.dataset.originalValue;
                textarea.style.display = "none";

                buttonGroup.style.display = "none";
                buttonGroup.classList.remove("d-flex", "float-end");
            }
        });  
    });

    // Event listener for cancel btn when editing a comment
    document.querySelectorAll(".edit-cancel-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postId = button.getAttribute("data-post-id"); // Get post ID from button
            const paragraph = document.querySelector(`#comment-description-${postId}`);
            const textarea = document.querySelector(`#comment-textarea-${postId}`); // Find correct textarea
            const buttonGroup = document.querySelector(`#edit-comment-btns-${postId}`);

            if (textarea) {
                
                paragraph.style.display = "block";

                textarea.setAttribute("hidden", "true");
                textarea.setAttribute("disabled", "true");
                textarea.value = textarea.dataset.originalValue;
                textarea.style.display = "none";

                buttonGroup.style.display = "none";
                buttonGroup.classList.remove("d-flex", "float-end");
                textarea.setAttribute("disabled", "true");
            }
        });
    });

    // Edit post AJAX for edit_post.php
    document.querySelectorAll("[id^=edit-post-form]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission
    
            const formData = new FormData(this);
    
            fetch("actions/edit_post.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error editing post: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Delete post AJAX for delete_post.php
    document.querySelectorAll("[id^=post-options-form]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission

            const formData = new FormData(this);
    
            // Debugging - Log the values
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }
    
            fetch("actions/delete_post.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error deleting post: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Like post AJAX for like_post.php
    document.querySelectorAll(".post-like-btn").forEach(button => {
        button.addEventListener("click", () => {
          const container = button.closest(".post-like-container");
          const postId = container.dataset.postId;
      
          fetch("actions/like_post.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
              post_id: postId
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              // Update like count
              button.querySelector(".post-like-count").textContent = data.like_count;
      
              // Toggle heart icon
              const icon = button.querySelector("i");
              icon.classList.toggle("bi-heart-fill", data.liked);
              icon.classList.toggle("bi-heart", !data.liked);
            } else if (data.unauthorized) {
                window.location.href = "login.php";
                return;
            } else {
              alert("Something went wrong with liking.");
            }
          });
        });
      });
      
    // Add comment AJAX for add_comment.php
    document.querySelectorAll("[id^=add-comment-form]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission
    
            const formData = new FormData(this);
    
            // Debugging - Log the values
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }
    
            fetch("actions/add_comment.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error adding comment: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Edit comment AJAX for edit_comment.php
    document.querySelectorAll("[id^=edit-comment-form]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission
    
            const formData = new FormData(this);
    
            fetch("actions/edit_comment.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error adding comment: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Delete comment AJAX for delete_comment.php
    document.querySelectorAll("[id^=comment-options-form]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Stop default form submission

            const formData = new FormData(this);
    
            // Debugging - Log the values
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }
    
            fetch("actions/delete_comment.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log("Server Response:", data); // Log server response
                if (data.trim() === "success") {
                    location.reload();
                } else {
                    alert("Error deleting comment: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Like post AJAX for like_post.php
    document.querySelectorAll(".comment-like-btn").forEach(button => {
        button.addEventListener("click", () => {
          const container = button.closest(".comment-like-container");
          const commentId = container.dataset.postId;
      
          fetch("actions/like_comment.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
              comment_id: commentId
            })
          })
          .then(res => res.json())
          .then(data => {
            if (data.success) {
              // Update like count
              button.querySelector(".comment-like-count").textContent = data.like_count;
      
              // Toggle heart icon
              const icon = button.querySelector("i");
              icon.classList.toggle("bi-heart-fill", data.liked);
              icon.classList.toggle("bi-heart", !data.liked);

              alert("comment was liked/unliked.");

            } else if (data.unauthorized) {
                window.location.href = "login.php";
                return;
            } else {
              alert("Something went wrong with liking this comment.");
            }
          });
        });
      });
});