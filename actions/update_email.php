<?php
session_start();
require_once('../includes/db_connection.php');


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    // Update email from account.php 
    if ($_POST['form_type'] === 'self_update_email' && isset($_SESSION['user_id'])) {
        
        $currentEmail = $_POST['current_email'];
        $newEmail = $_POST['new_email'];
        $confirmEmail = $_POST['confirm_email'];
        
    
        // Check the current email is valid
        $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :current_email AND user_id = :uid");
        $stmt->bindParam(':current_email', $currentEmail, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->execute();
    
        if (!$stmt->fetch()) {
            $_SESSION['invalid-email-error'] = "Invalid current email";
            exit();
        }
    
    
        //Check that the new email addresses match
        $isMatch = ($newEmail === $confirmEmail);
    
        if ($isMatch) {
    
            // Check that the email doesn't already exist in the DB
            $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :new_email");
            $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
            $stmt->execute();
    
            if ($stmt->fetch()) {
                $_SESSION['new-email-error'] = "New email already in use";
                exit();
            }
    
            // Prepare the SQL UPDATE statement
            $stmt = $pdo->prepare("UPDATE users SET email = :new_email WHERE user_id = :uid AND email = :current_email");
            $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
            $stmt->bindParam(':current_email', $currentEmail, PDO::PARAM_STR);
            $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);    
            $stmt->execute();
            
        } else {
            $_SESSION['new-email-error'] = "New emails do not match";
            exit();
        }
    
        if ($stmt->execute()) {
            $_SESSION['email-success'] = "Your email address has been updated";
            echo "success";
        } else {
            echo "error updating email address: " . implode(" ", $stmt->errorInfo());
        }

    } 
    // Update another users email as an admin from admin.php 
    else if ($_POST['form_type'] === 'admin_update_email' && isset($_POST['profile_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        
        $profile_id = $_POST['profile_id'];
        $newEmail = $_POST['new_email'];
        $confirmEmail = $_POST['confirm_email'];
        $isMatch = ($newEmail === $confirmEmail); //Check that the new email addresses match

        if ($isMatch) {
            // Check that the email doesn't already exist in the DB
            $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :new_email");
            $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
            $stmt->execute();
    
            if ($stmt->fetch()) {
                $_SESSION['new-email-error'] = "New email already in use";
                exit();
            }

            $stmt = $pdo->prepare("
                UPDATE users u
                JOIN profiles p ON u.user_id = p.user_id
                SET u.email = :new_email
                WHERE p.profile_id = :profile_id
            ");
            $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
            $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
            $stmt->execute();

        } else {
            $_SESSION['new-email-error'] = "New emails do not match";
            exit();
        }

        if ($stmt->execute()) {
            $_SESSION['email-success'] = "Your email address has been updated";
            echo "success";
        } else {
            echo "error updating email address: " . implode(" ", $stmt->errorInfo());
        }
    }
}
// Default else 
else {
    echo "Invalid request.";
    exit();
}

?>