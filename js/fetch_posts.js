document.addEventListener("DOMContentLoaded", function() {
    
    // Add an event listener to each textarea on each post
     document.querySelectorAll(".add-comment-textarea").forEach(textarea => {
        const id = textarea.getAttribute("id").replace("add-comment-textarea-", "");
        const buttonGroup = document.querySelector(`#add-comment-btns-${id}`);

        textarea.addEventListener("click", function() {
            buttonGroup.style.display = "block";
            buttonGroup.classList.add("d-flex", "float-end");
        });

        document.addEventListener("click", function(event) {
            if (!textarea.contains(event.target) && !buttonGroup.contains(event.target)) {
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