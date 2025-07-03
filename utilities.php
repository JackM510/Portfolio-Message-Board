<?php 

    function isLoggedIn() {
        if (!isset($_SESSION['user_id']) && !isset($_SESSION['email']) && !isset($_SESSION['first_name']) && !isset($_SESSION['role'])) {
            return false;
        } else {
            return true;
        }
    }

?>