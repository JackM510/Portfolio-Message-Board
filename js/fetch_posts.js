document.addEventListener("DOMContentLoaded", function() {

    // TA responsive height testing
    document.querySelectorAll("textarea").forEach(textarea => {
        textarea.style.height = "auto"; // Reset for fresh calculation
        textarea.style.height = textarea.scrollHeight + "px"; // Adjust to content
      });


      document.addEventListener("input", function (event) {
        if (event.target.tagName.toLowerCase() === "textarea") {
          event.target.style.height = "auto"; 
          event.target.style.height = event.target.scrollHeight + "px"; 
        }
      });

      /*function resizeToExactLines(textarea) {
        textarea.style.height = "auto";
        const lines = textarea.value.split("\n").length || 1;
        const lineHeight = parseFloat(getComputedStyle(textarea).lineHeight);
        textarea.style.height = (lines * lineHeight) + "px";
      }
      
      document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll("textarea.auto-resize").forEach(textarea => {
          resizeToExactLines(textarea); // Initial sizing
      
          textarea.addEventListener("input", () => {
            resizeToExactLines(textarea);
          });
        });
      });*/
      


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
        
        // Store the original value before editing - used when edit-cancel-btn selected
        img.dataset.originalSrc = img.src;
        textarea.dataset.originalValue = textarea.value;


        button.addEventListener("click", function() {
            imgUploadBtn.style.display ="block"; // Show img upload btn
            paragraph.style.display = "none";


            textarea.removeAttribute("hidden"); // Make textarea active
            textarea.removeAttribute("disabled"); // Make textarea active
            textarea.style.display = "block";
            
            textarea.focus();
            btnGroup.style.display = "block";
            btnGroup.classList.add("d-flex", "float-end");
        });

        // Hide new post components when clicked outside of new post layout
        document.addEventListener("click", function (event) {
            if (!postDropdown.contains(event.target) && !postForm.contains(event.target)) {
                
                img.src = img.dataset.originalSrc;
                img.style.display = "none";

                paragraph.style.display = "block";

                textarea.setAttribute("hidden", "true");
                textarea.setAttribute("disabled", "true");
                textarea.value = textarea.dataset.originalValue;

                imgUploadBtn.style.display = "none";
                btnGroup.style.display = "none";
                btnGroup.classList.remove("d-flex", "float-end");
            }
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

                img.src = img.dataset.originalSrc;  // Restore img original src (if any)
                img.style.display = "none";
                
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

            // Store the original image src before changing it
            if (!imgTag.dataset.originalSrc) {
                imgTag.dataset.originalSrc = imgTag.src;
            }

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
                textarea.value = ""; // Clear the textarea without refreshing
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
                textarea.value = ""; // Clear the textarea without refreshing
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
            textarea.removeAttribute("hidden");
            textarea.removeAttribute("disabled");
            textarea.style.display = "block";

            // Show the button group
            buttonGroup.style.display = "block";
            buttonGroup.classList.add("d-flex", "float-end");
            textarea.focus();
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
});