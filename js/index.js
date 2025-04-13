document.addEventListener("DOMContentLoaded", function() {
    const textareas = document.querySelectorAll(".add-comment-textarea");

    textareas.forEach(textarea => {
        const id = textarea.getAttribute("id");
        const buttonGroup = document.querySelector(`#add-comment-btns-${id}`);

        // Show buttons when the textarea is focused
        textarea.addEventListener("focus", function() {
            buttonGroup.style.display = "block"; // Show buttons
        });

        // Hide buttons when focus is lost (clicked outside)
        textarea.addEventListener("blur", function(event) {
            setTimeout(() => {
                buttonGroup.style.display = "none"; // Hide buttons after blur
            }, 100); // Delay needed to allow clicks inside button group
        });
    });
});
