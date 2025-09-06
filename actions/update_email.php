<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

// Check current email is assigned to the user
function verifyCurrentEmail(PDO $pdo, string $currentEmail, int $user_id): void {
    $stmt = $pdo->prepare("
        SELECT 1 
        FROM users 
        WHERE email = :current_email 
          AND user_id = :uid
    ");
    $stmt->bindParam(':current_email', $currentEmail, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    // Break if current email invalid
    if (!$stmt->fetchColumn()) {
        $_SESSION['invalid-email-error'] = "Invalid current email";
        exit();
    }
}

// Check if the email exists in the DB
function checkEmailExists(PDO $pdo, string $newEmail): void {
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = :new_email");
    $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $_SESSION['new-email-error'] = "New email already in use";
        exit();
    }
}

// Update users email in DB
function updateUserEmail(PDO $pdo, int $userId, string $newEmail, ?string $currentEmail = null): bool {
    if ($currentEmail !== null) {
        $sql = "UPDATE users SET email = :new_email 
                WHERE user_id = :uid AND email = :current_email";
    } else {
        $sql = "UPDATE users SET email = :new_email 
                WHERE user_id = :uid";
    }
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':new_email', $newEmail, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $userId, PDO::PARAM_INT);
    if ($currentEmail !== null) {
        $stmt->bindParam(':current_email', $currentEmail, PDO::PARAM_STR);
    }
    return $stmt->execute();
}

// Form submission from account.php or admin.php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    // Update email from account.php 
    if ($_POST['form_type'] === 'self_update_email' && isLoggedIn()) {
        $user_id = (int)$_SESSION['user_id'];
        $currentEmail = $_POST['current_email'];
        $newEmail = $_POST['new_email'];
        $confirmEmail = $_POST['confirm_email'];
        $isMatch = ($newEmail === $confirmEmail); // Check new emails match

        verifyCurrentEmail($pdo, $currentEmail, $user_id); // Check current email belongs to user
        if ($isMatch) {
            checkEmailExists($pdo, $newEmail); // Check new email exists in DB
            // Update email in mysql
            if (updateUserEmail($pdo, $user_id, $newEmail, $currentEmail)) {
                $_SESSION['email-success'] = "Your email address has been updated";
                exit();
            } else {
                $_SESSION['new-email-error'] = "Error updating email address";
                exit();
            }
        } 
        // Emails don't match
        else {
            $_SESSION['new-email-error'] = "New emails do not match";
            exit();
        }
    } 
    // Update emails as an admin from admin.php 
    else if ($_POST['form_type'] === 'admin_update_email' && isset($_POST['user_id']) && isAdmin()) {
        $_SESSION['display_form'] = "view_profile"; // Stay on 'view profile' after POST
        $user_id = $_POST['user_id'];
        $newEmail = $_POST['new_email'];
        $confirmEmail = $_POST['confirm_email'];
        $isMatch = ($newEmail === $confirmEmail); // Check new emails match

        if ($isMatch) {
            checkEmailExists($pdo, $newEmail); // Check new email exists in DB
            // Update email in mysql
            if (updateUserEmail($pdo, $user_id, $newEmail)) {
                $_SESSION['email-success'] = "Email updated";
                exit();
            } else {
                $_SESSION['new-email-error'] = "Error updating email address";
                exit();
            }  
        } 
        // Emails don't match
        else {
            $_SESSION['new-email-error'] = "New emails do not match";
            exit();
        }
    }
}
// Default else 
else {
    echo "Invalid request.";
    exit();
}
?>