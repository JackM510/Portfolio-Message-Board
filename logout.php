<?php
    unset($_SESSION['name']);
    unset($_SESSION['email']);
    header("Location: index.php");
    exit();
?>