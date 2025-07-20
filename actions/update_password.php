<?php
session_start();
require_once('../includes/db_connection.php');

// Update password function
function updateUserPassword($pdo, $user_id, $newPW) {
    // Validation
    if (strlen($newPW) < 8) {
        $_SESSION['update-password-error'] = "Password must be at least 8 characters";
        return false;
    }
    if (!preg_match('/\d/', $newPW)) {
        $_SESSION['update-password-error'] = "Password must contain at least 1 number";
        return false;
    }
    if (!preg_match('/[A-Z]/', $newPW)) {
        $_SESSION['update-password-error'] = "Password must contain at least 1 capital letter";
        return false;
    }

    // Hash and update
    $hashedPassword = password_hash($newPW, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = :new_pw WHERE user_id = :uid");
    $stmt->bindParam(':new_pw', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    // Reset password from account.php
    if ($_POST['form_type'] === 'self_update_pw' && isset($_SESSION['user_id'])) {
        
        // Get password values
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
        // Check the new passwords are the same
        else if ($newPW !== $confirmPW) {
            $_SESSION['update-password-error'] = "New passwords don't match";
            exit();
        } 

        // Default else to update the users password
        else {
            if (updateUserPassword($pdo, $_SESSION['user_id'], $newPW)) {
                $_SESSION['pw-success'] = "Your password has been updated";
                echo "success";
            } else {
                echo $_SESSION['update-password-error'] ?? "Error updating password.";
            }
        }   
    } 

    // Reset a users password as an admin from admin.php
    else if ($_POST['form_type'] === 'admin_update_pw' && isset($_POST['profile_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        // Get password & profile_id values
        $newPW = $_POST['new_pw'];
        $confirmPW = $_POST['confirm_pw'];
        $profile_id = $_POST['profile_id'];
        
        // Get user_id from profile_id
        $stmt = $pdo->prepare("SELECT user_id FROM profiles WHERE profile_id = ?");
        $stmt->execute([$profile_id]);
        $user_id = $stmt->fetchColumn();

        if (!$user_id) {
            echo "User not found.";
            exit();
        }
        // Check the new passwords are the same
        else if ($newPW !== $confirmPW) {
            $_SESSION['update-password-error'] = "New passwords don't match";
            exit();
        } 

        if (updateUserPassword($pdo, $user_id, $newPW)) {
            $_SESSION['pw-success'] = "User password has been updated";
            echo "success";
        } else {
            echo $_SESSION['update-password-error'] ?? "Error updating password.";
        }
    }
    
    // Default else
    else {
        echo "Invalid request.";
        exit();
    }
}
?>