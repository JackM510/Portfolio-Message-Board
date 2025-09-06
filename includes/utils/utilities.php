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

    // Password validation checks
    function validatePassword(string $password): ?string {
        if (strlen($password) < 8) {
            return "Password must be at least 8 characters";
        }
        if (!preg_match('/\d/', $password)) {
            return "Password must contain at least 1 number";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            return "Password must contain at least 1 capital letter";
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            return "Password must contain at least 1 symbol";
        }
        return null; // valid
    }
?>