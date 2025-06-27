<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {

    $currentPW = $_POST['current_pw'];
    $newPW = $_POST['new_pw'];
    $confirmPW = $_POST['confirm_pw'];

    // Need to check the current password matches the password stored in the DB
    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :uid");
    $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $storedHash = $stmt->fetchColumn();

    // Verify the current password
    if (!password_verify($currentPW, $storedHash)) {
        $_SESSION['invalid-password-error'] = "Current password invalid";
        exit();
    }

    // Need to check that the passwords match
    $isMatch = ($newPW === $confirmPW);
    if ($isMatch) {

        // Need to check the password meets certain conditions
        // Flash error message if password is < 8 characters
        if (strlen($newPW) < 8) {
            $_SESSION['update-password-error'] = "Password must be at least 8 characters";
            exit();
        }
        // Flash error message if password contains <1 number
        else if (!preg_match('/\d/', $newPW)) {
            $_SESSION['update-password-error'] = "Password must contain at least 1 number";
            exit();
        }
        // Flash error message if password contains <1 number
        else if (!preg_match('/[A-Z]/', $newPW)) {
            $_SESSION['update-password-error'] = "Password must contain at least 1 capital letter";
            exit();
        } 

        // Need to hash the new password first
        $hashedPassword = password_hash($newPW, PASSWORD_DEFAULT); // Hash the password

        // Prepare the SQL UPDATE statement
        $stmt = $pdo->prepare("UPDATE users SET password = :new_pw WHERE user_id = :uid AND email = :current_email");
        $stmt->bindParam(':new_pw', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);   
        $stmt->bindParam(':current_email', $_SESSION['email'], PDO::PARAM_STR);
        
        $stmt->execute();

    } else {
        $_SESSION['update-password-error'] = "New passwords don't match";
        exit();
    }

    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error updating password: " . implode(" ", $stmt->errorInfo());
    }
}
?>