<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

// Verify current password 
function verifyUserPassword(PDO $pdo, int $user_id, string $currentPW) {
    $stmt = $pdo->prepare("SELECT password FROM users WHERE user_id = :uid");
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $storedHash = $stmt->fetchColumn();

    // Verify current password with stored hash 
    if (!password_verify($currentPW, $storedHash)) {
        $_SESSION['invalid-password-error'] = "Current password invalid";
        exit();
    } 
}

// Validate & update new password
function updateUserPassword(PDO $pdo, int $user_id, string $newPW) {
    // Validate the new password
    $error = validatePassword($newPW);
    if ($error !== null) {
        $_SESSION['update-password-error'] = $error;
        return false;
    }
    // Validated - hash and update new password
    $hashedPassword = password_hash($newPW, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("UPDATE users SET password = :new_pw WHERE user_id = :uid");
    $stmt->bindParam(':new_pw', $hashedPassword, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    return $stmt->execute();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    // Reset password from account.php
    if ($_POST['form_type'] === 'self_update_pw' && isLoggedIn()) {
        $user_id = $_SESSION['user_id'];
        $currentPW = $_POST['current_pw'];
        $newPW = $_POST['new_pw'];
        $confirmPW = $_POST['confirm_pw'];
        $isMatch = ($newPW === $confirmPW); // Check new passwords match
        verifyUserPassword($pdo, $user_id, $currentPW); // Verify users current password

        if ($isMatch) {
            // Attempt to update password
            if (updateUserPassword($pdo, $_SESSION['user_id'], $newPW)) {
                $_SESSION['pw-success'] = "Password updated";
                echo "success";
                exit();
            } else {
                echo $_SESSION['update-password-error'] ?? "Error updating password.";
                exit();
            }
        }
        // Password mismatch
        else {
            $_SESSION['update-password-error'] = "New passwords don't match";
            exit();
        } 
    } 

    // Reset user password as an admin from admin.php
    else if ($_POST['form_type'] === 'admin_update_pw' && isAdmin() && isset($_POST['user_id'])) {
        $_SESSION['display_form'] = "view_profile"; // Stay on view_profile after POST
        $user_id = $_POST['user_id'];
        $newPW = $_POST['new_pw'];
        $confirmPW = $_POST['confirm_pw'];
        $isMatch = ($newPW === $confirmPW); // Check new passwords match
        
        if ($isMatch) {
            // Attempt to update password
            if (updateUserPassword($pdo, $user_id, $newPW)) {
                $_SESSION['pw-success'] = "Password updated";
                echo "success";
                exit();
            } else {
                echo $_SESSION['update-password-error'] ?? "Error updating password.";
                exit();
            }
        } 
        // Password mismatch
        else {
            $_SESSION['update-password-error'] = "New passwords don't match";
            exit();
        }   
    }
    // Default else
    else {
        echo "Invalid request.";
        exit();
    }
}
?>