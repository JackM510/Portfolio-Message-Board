document.addEventListener("DOMContentLoaded", function() {
    // Declare module variables
    let predictLines;
    // Load JS modules dynamically
    import(window.API.jsPredictLines)
        .then(mod => predictLines = mod.predictLines)
        .catch(err => console.error("Predict lines module failed to load:", err));

    // Edit post 'cancel' btn & outside click handler
    function cancelEditPost(postId) {
        const img = document.querySelector(`.post-picture-${postId}`);
        const imgUploadBtn = document.querySelector(`.post-img-upload-btn-${postId}`);
        const likeBtn = document.querySelector(`.post-like-btn-${postId}`);
        const commentsBtn = document.querySelector(`.post-comment-btn-${postId}`);
        const paragraph = document.querySelector(`.post-description-${postId}`);
        const textarea = document.querySelector(`.post-textarea-${postId}`);
        const buttonGroup = document.querySelector(`.edit-post-btn-group-${postId}`);

        // Restore original img
        if (img && img.dataset.hasOriginalImage === "true") {
                    img.src = img.dataset.originalSrc;
                    img.style.display = "block";
        } else if (img) {
            img.src = "";
            img.style.display = "none";
        }

        // Display
        likeBtn.style.display = "block";
        commentsBtn.style.display = "block";
        paragraph.style.display = "block";
        // Hide
        textarea.value = textarea.dataset.originalValue;
        textarea.setAttribute("hidden", "true");
        textarea.setAttribute("disabled", "true");
        imgUploadBtn.style.display ="none";
        buttonGroup.style.display = "none";
        buttonGroup.classList.remove("d-flex", "float-end");
    }

    // Add comment 'cancel' btn & outside click handler
    function cancelAddComment(postId) {
        const textarea = document.querySelector(`.add-comment-textarea-${postId}`);
        const buttonGroup = document.querySelector(`.add-comment-btns-${postId}`);
        if (textarea) {
            textarea.value = "";
            buttonGroup.style.display = "none";
            buttonGroup.classList.remove("d-flex", "float-end");
        }
    }

    // Edit comment 'cancel' btn & outside click handler
    function cancelEditComment(postId) {
        const paragraph = document.querySelector(`.comment-description-${postId}`);
        const textarea = document.querySelector(`.comment-textarea-${postId}`);
        const buttonGroup = document.querySelector(`.edit-comment-btns-${postId}`);
        const commentLikeBtn = document.querySelector(`.comment-like-btn-${postId}`);

        if (textarea) {
            // Display
            commentLikeBtn.style.display = "block";
            paragraph.style.display = "block";
            // Hide
            textarea.setAttribute("hidden", "true");
            textarea.setAttribute("disabled", "true");
            textarea.value = textarea.dataset.originalValue;
            textarea.style.display = "none";
            buttonGroup.style.display = "none";
            buttonGroup.classList.remove("d-flex", "float-end");
        }
    }

    // Textarea predict lines
    document.querySelectorAll("textarea").forEach(textarea => {
        textarea.addEventListener("input", () => {
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);
        });
    });
    
    // Remember post 'comment section' should be visible
    window.addEventListener("DOMContentLoaded", () => {
        const postIdToShow = sessionStorage.getItem("showCommentsFor"); // Get postId of post
        if (postIdToShow) {
            // Get the post 'comment' section elements
            const section = document.querySelector(`.comment-section-${postIdToShow}`);
            const comments = section?.querySelectorAll(`.comment-${postIdToShow}`);
            const viewMoreWrapper = document.querySelector(`.view-more-comments-wrapper-${postIdToShow}`);
            // Scroll to comment
            if (section) {
                section.style.display = "block";
                if (sessionStorage.getItem("scrollToNewComment") === "true") {
                    comments.forEach(comment => comment.style.display = "block"); // Show all comments
                    if (viewMoreWrapper) viewMoreWrapper.style.display = "none"; // Hide 'View more comments' btn
                } else {
                    comments.forEach(comment => comment.style.display = "none"); // Initially hide all  comments
                    // Display only the first 5 comments
                    for (let i = 0; i < Math.min(5, comments.length); i++) { 
                        comments[i].style.display = "block"; 
                    }
                    // Display the 'View more comments' btn
                    if (viewMoreWrapper) { 
                        viewMoreWrapper.setAttribute("data-visible-count", "5");
                        viewMoreWrapper.style.display = comments.length > 5 ? "flex" : "none";
                    }
                }
            }
            // Clear session keys
            sessionStorage.removeItem("showCommentsFor");
            sessionStorage.removeItem("scrollToNewComment");
        }
    });

    // Edit post btn
    document.querySelectorAll(".edit-post-btn").forEach(button => {
        const postId = button.getAttribute("data-post-id"); // Get post ID
        const postForm = document.querySelector(`.edit-post-form-${postId}`);
        const postDropdown = document.querySelector(`.post-dropdown-${postId}`);
        const img = document.querySelector(`.post-picture-${postId}`);
        const imgUploadBtn = document.querySelector(`.post-img-upload-btn-${postId}`);
        const paragraph = document.querySelector(`.post-description-${postId}`);
        const textarea = document.querySelector(`.post-textarea-${postId}`);
        const btnGroup = document.querySelector(`.edit-post-btn-group-${postId}`);
        const postLikeBtn = document.querySelector(`.post-like-btn-${postId}`);
        const viewCommentsBtn = document.querySelector(`.post-comment-btn-${postId}`);
        
        // Show form elements to update the post
        button.addEventListener("click", function() {
            if (img) {
                const imgSrc = img.getAttribute("src")?.trim(); // Get img src
                const isValidImage = imgSrc &&
                    !imgSrc.includes("index.php") &&
                    !imgSrc.includes("profile.php") &&
                    !imgSrc.includes("Post Image") &&
                    !imgSrc.startsWith("data:") &&
                    /\.(jpg|jpeg|png|gif|webp)$/i.test(imgSrc);
                img.dataset.hasOriginalImage = isValidImage ? "true" : "false";
                // Display img if valid
                if (isValidImage) {
                    img.dataset.originalSrc = imgSrc;
                    img.style.display = "block";
                } else {
                    img.style.display = "none";
                }
            }    
            // Hide
            paragraph.style.display = "none";
            postLikeBtn.style.display = "none";
            viewCommentsBtn.style.display = "none";
            // Display  
            textarea.dataset.originalValue = textarea.value;
            imgUploadBtn.style.display ="block";
            btnGroup.style.display = "block";
            btnGroup.classList.add("d-flex", "float-end");
            textarea.removeAttribute("hidden"); 
            textarea.removeAttribute("disabled"); 
            textarea.style.display = "block";
            textarea.focus();
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);

            // Outside click handler
            const outsideClickHandler = function (event) {
                if (!postDropdown.contains(event.target) && !postForm.contains(event.target)) {
                    cancelEditPost(postId);
                }
            };
            document.addEventListener("click", outsideClickHandler);
        });
    });

    // Edit post 'cancel' btn
    document.querySelectorAll(".edit-post-cancel-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postId = button.getAttribute("data-post-id");
            cancelEditPost(postId);                
        });
    });

    // Edit post 'img upload' btn
    document.querySelectorAll('[class^="post-img-upload-btn-"]').forEach(btn => {
        btn.addEventListener('click', function () {
            const postId = this.dataset.postId;
            const fileInput = document.querySelector(`.post-img-upload-${postId}`);
            // Trigger the img input
            if (fileInput) {
                fileInput.click(); 
            }
        });
    });

    // Edit post 'img upload' input listener
    document.querySelectorAll('[class^="post-img-upload-"]').forEach(input => {
        input.addEventListener("change", function(event) {
            const postId = input.dataset.postId; // Extract post ID
            const imgTag = document.querySelector(`.post-picture-${postId}`);
            const file = event.target.files[0]; // Get the uploaded img
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imgTag.src = e.target.result; // Update img preview
                    imgTag.style.display = "block"; // Make sure img is visible
                };
                reader.readAsDataURL(file);
            }
        });
    });
    
    // Post 'comment icon' btn (expand comment section on a post)
    document.querySelectorAll('[class^="post-comment-btn-"]').forEach(btn => {
        btn.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const commentSection = document.querySelector(`.comment-section-${postId}`);
            const comments = commentSection.querySelectorAll(`.comment-${postId}`);
            const viewMoreWrapper = document.querySelector(`.view-more-comments-wrapper-${postId}`);
    
            // Show/Hide comment section on post
            const isVisible = commentSection.style.display === "block";
            commentSection.style.display = isVisible ? "none" : "block";
            if (!isVisible) {
                comments.forEach(comment => comment.style.display = "none");
                // Show first 5 comments when comment button clicked
                for (let i = 0; i < Math.min(5, comments.length); i++) {
                    comments[i].style.display = "block";
                }
                viewMoreWrapper.setAttribute("data-visible-count", "5");
                viewMoreWrapper.style.display = comments.length > 5 ? "flex" : "none";
            }
        });
    }); 
    
    // Post 'view more comments' btn
    document.querySelectorAll(".view-more-comments-btn").forEach(btn => {
        btn.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const commentSection = document.querySelector(`.comment-section-${postId}`);
            const comments = commentSection.querySelectorAll(`.comment-${postId}`);
            const viewMoreWrapper = document.querySelector(`.view-more-comments-wrapper-${postId}`);
            let visibleCount = parseInt(viewMoreWrapper.getAttribute("data-visible-count"));
            
            // Get comment count and display another 5 comments
            for (let i = visibleCount; i < Math.min(visibleCount + 5, comments.length); i++) {
                comments[i].style.display = "block";
            }
            visibleCount += 5;
            viewMoreWrapper.setAttribute("data-visible-count", visibleCount); // Keep track of comments visible
            if (visibleCount >= comments.length) {
                viewMoreWrapper.style.display = "none";
            }
        });
    });
    
    // Add a comment textarea
     document.querySelectorAll('[class^="add-comment-textarea-"]').forEach(textarea => {
        const postId = textarea.dataset.postId;
        const buttonGroup = document.querySelector(`.add-comment-btns-${postId}`);

        textarea.addEventListener("click", function() {
            buttonGroup.style.display = "block";
            buttonGroup.classList.add("d-flex", "float-end");
        });

        // Outside click handler
        const outsideClickHandler = function (event) {
             if (!textarea.contains(event.target) && !buttonGroup.contains(event.target)) {
                cancelAddComment();
            }
        };
        document.addEventListener("click", outsideClickHandler);
    });

    // Add a comment 'cancel' btn
    document.querySelectorAll(".add-comment-cancel-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postId = button.getAttribute("data-post-id");
            cancelAddComment(postId);
        });
    });

    // Comment 'edit' btn
    document.querySelectorAll(".edit-comment-btn").forEach(button => {
        const postId = button.getAttribute("data-post-id");
        const paragraph = document.querySelector(`.comment-description-${postId}`);
        const textarea = document.querySelector(`.comment-textarea-${postId}`);
        const commentDropdown = document.querySelector(`.comment-dropdown-${postId}`);
        const buttonGroup = document.querySelector(`.edit-comment-btns-${postId}`);
        const commentLikeBtn = document.querySelector(`.comment-like-btn-${postId}`);

        textarea.dataset.originalValue = textarea.value; // Store original value
        button.addEventListener("click", function() {
            // Hide
            commentLikeBtn.style.display = "none";
            paragraph.style.display = "none";
            // Display
            textarea.removeAttribute("disabled");
            textarea.removeAttribute("hidden");
            textarea.style.display = "block";
            textarea.focus();
            const lines = predictLines(textarea);
            textarea.setAttribute("rows", lines);
            buttonGroup.style.display = "block";
            buttonGroup.classList.add("d-flex", "float-end");
        });

        // Outside click handler
        const outsideClickHandler = function (event) {
            if (!textarea.contains(event.target) && !commentDropdown.contains(event.target) && !buttonGroup.contains(event.target)) {
                cancelEditComment(postId);
            }
        };
        document.addEventListener("click", outsideClickHandler);
    });

    // Comment 'cancel' btn
    document.querySelectorAll(".edit-comment-cancel-btn").forEach(button => {
        button.addEventListener("click", function() {
            const postId = button.getAttribute("data-post-id"); // Get post ID from button
            cancelEditComment(postId);
        });
    });

    // Edit post AJAX for edit_post.php
    document.querySelectorAll("[class^=edit-post-form-]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            
            fetch(API.editPost, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
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
    document.querySelectorAll("[class^=post-options-form-]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const formData = new FormData(this);
            
            fetch(API.deletePost, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
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
    document.querySelectorAll("[class^=post-like-btn-]").forEach(button => {
        button.addEventListener("click", () => {
            const postId = button.getAttribute("data-post-id"); // Get post ID
           
            fetch(API.likePost, {
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
                // Toggle heart icon
                if (data.success) {
                    button.querySelector(".post-like-count").textContent = data.like_count; // Update like count
                    const icon = button.querySelector(`.post-like-btn-${postId} i`);
                    icon.classList.toggle("bi-hand-thumbs-up-fill", data.liked);
                    icon.classList.toggle("bi-hand-thumbs-up", !data.liked);
                } else if (data.unauthorized) {
                    window.location.href = API.loggedOutLike; // Redirect to login.php
                    return;
                } else {
                alert("Something went wrong with liking.");
                }
            });
        });
      });

    // Add comment AJAX for add_comment.php
    document.querySelectorAll("[class^=add-comment-form-]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const postId = this.getAttribute("data-post-id");
            const formData = new FormData(this);
  
            fetch(API.addComment, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    sessionStorage.setItem("showCommentsFor", postId); // Display post comment section
                    sessionStorage.setItem("scrollToNewComment", "true"); // Scroll to the new comment
                    location.reload();
                } else {
                    alert("Error adding comment: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Edit comment AJAX for edit_comment.php
    document.querySelectorAll("[class^=edit-comment-form-]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const postId = this.getAttribute("data-post-id");
            const formData = new FormData(this);
    
            fetch(API.editComment, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    sessionStorage.setItem("showCommentsFor", postId); // Display post comment section
                    location.reload();
                } else {
                    alert("Error adding comment: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Delete comment AJAX for delete_comment.php
    document.querySelectorAll("[class^=comment-options-form-]").forEach(form => {
        form.addEventListener("submit", function(event) {
            event.preventDefault();
            const postId = this.getAttribute("data-post-id");
            const formData = new FormData(this);
    
            fetch(API.deleteComment, {
                method: "POST",
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                if (data.trim() === "success") {
                    sessionStorage.setItem("showCommentsFor", postId); // Display post comment section
                    location.reload();
                } else {
                    alert("Error deleting comment: " + data);
                }
            })
            .catch(error => console.error("Fetch Error:", error));
        });
    });

    // Like post AJAX for like_comment.php
    document.querySelectorAll('[class^="comment-like-btn-"]').forEach(button => {
        button.addEventListener("click", () => {
           const commentId = button.getAttribute("data-post-id"); // Get post ID
      
          fetch(API.likeComment, {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded"
            },
            body: new URLSearchParams({
              comment_id: commentId
            })
          })
          .then(res => res.json())
          // Toggle heart icon
          .then(data => {
            if (data.success) {
              button.querySelector(".comment-like-count").textContent = data.like_count; // Update like count
              const icon = button.querySelector(`.comment-like-btn-${commentId} i`);
              icon.classList.toggle("bi-hand-thumbs-up-fill", data.liked);
              icon.classList.toggle("bi-hand-thumbs-up", !data.liked);
            } else if (data.unauthorized) {
                window.location.href = API.loggedOutLike; // Redirect to login.php
                return;
            } else {
              alert("Something went wrong with liking this comment.");
            }
          });
        });
    });
});