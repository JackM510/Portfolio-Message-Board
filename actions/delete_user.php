<?php
session_start();
require_once('../includes/db_connection.php');

// Function to delete entire user DIR recursively
function deleteUserDirectory($profile_id) {
    $userDir = "../uploads/profiles/{$profile_id}/";
    if (!is_dir($userDir)) return;

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($userDir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::CHILD_FIRST
    );

    foreach ($files as $file) {
        $file->isDir() ? rmdir($file) : unlink($file);
    }
    rmdir($userDir);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['form_type'])) {
    
    // Delete your account from account.php
    if ($_POST['form_type'] === 'self_delete_user' && isset($_SESSION['user_id']) && !empty($_POST['delete_checkbox_1']) && !empty($_POST['delete_checkbox_2'])) {
        $user_id = $_SESSION['user_id'];
        $profile_id = $_SESSION['profile_id'];

        // Check that the email doesn't already exist in the DB
        $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
        $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
        $stmt->execute();
    
        deleteUserDirectory($profile_id); // Delete users DIR
        header("Location: ../actions/logout_user.php"); // log the user out
    } 
    
    // Delete another users account as an admin from admin.php
    else if ($_POST['form_type'] === 'admin_delete_user' && isset($_POST['user_id']) && isset($_POST['profile_id']) && isset($_SESSION['role']) && $_SESSION['role'] === 'admin' && !empty($_POST['delete_checkbox_1'])) {
        $profile_id = (int) $_POST['profile_id'];

        // Prevent admin from deleting their own account
        if ($profile_id === (int)$_SESSION['profile_id']) {
            $_SESSION['delete-error'] = "You cannot delete your own admin account";
            echo json_encode([
                "success" => false,
                "message" => "Admins cannot delete their own account"
            ]);
            exit();
        }
        
        // Delete user from DB
        $stmt = $pdo->prepare("
            DELETE FROM users
            WHERE user_id = (
                SELECT user_id FROM profiles WHERE profile_id = ?
            )
        ");
        $stmt->execute([$profile_id]);

        // Delete user's profile directory
        deleteUserDirectory($profile_id);
        // Go back to user_search after user deleted
        $_SESSION['display_form'] = "user_search";
        $_SESSION['delete-success'] ="User deleted";

        echo json_encode(["success" => true, "message" => "User deleted"]);
    } 
    
    else {
        echo "Invalid request.";
        exit();
    }
}

?>