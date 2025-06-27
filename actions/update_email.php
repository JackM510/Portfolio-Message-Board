<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {

    $currentEmail = $_POST['current_email'];
    $newEmail = $_POST['new_email'];
    $confirmEmail = $_POST['confirm_email'];
    
    //Check that the new email addresses match
    $isMatch = ($newEmail === $confirmEmail);

    if ($isMatch) {

        // Check that the email doesn't already exist in the DB
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :new_email");
        $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->fetch()) {
            $_SESSION['new-email-error'] = "Email is already in use";
            exit();
        }

        // Prepare the SQL UPDATE statement
        $stmt = $pdo->prepare("UPDATE users SET email = :new_email WHERE user_id = :uid AND email = :current_email");
        $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
        $stmt->bindParam(':current_email', $currentEmail, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);    
        $stmt->execute();
        
    } else {
        $_SESSION['new-email-error'] = "Emails do not match";
        exit();
    }

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error updating email address: " . implode(" ", $stmt->errorInfo());
    }
}
?>