<?php 
    // Check if a user is logged in
    function isLoggedIn() {
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['profile_id']) && !isset($_SESSION['email']) && !isset($_SESSION['first_name']) && !isset($_SESSION['role'])) {
            return false;
        } else {
            return true;
        }
    }

    // Check if a logged in user has a role of 'admin'
    function isAdmin() {
        return isLoggedIn() && $_SESSION['role'] === 'admin';
    }
?>