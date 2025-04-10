<?php 

function isLoggedIn() {
    if (!isset($_SESSION['user_id']) && !isset($_SESSION['email'])) {
        return false;
    } else {
        return true;
    }
}

?>