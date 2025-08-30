// Ensure all checkboxes are marked when deleting a user
export function validateCheckboxes(deleteBtn, checkboxes) {
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    if (allChecked) {
        deleteBtn.classList.remove("disabled");
    } else {
        deleteBtn.classList.add("disabled");
    }
}