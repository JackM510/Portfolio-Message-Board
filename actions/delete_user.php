<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

// Delete user from DB
function deleteUser($pdo, $user_id): void {
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
}

// Delete entire user DIR recursively
function deleteUserDirectory($userID) {
    $userDir = "../uploads/profiles/{$userID}/";
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
    if ($_POST['form_type'] === 'self_delete_user' && isLoggedIn() && !empty($_POST['delete_checkbox_1']) && !empty($_POST['delete_checkbox_2'])) {
        $user_id = (int) $_SESSION['user_id'];
        deleteUser($pdo, $user_id); // Delete user from DB
        deleteUserDirectory($user_id); // Delete users DIR
        header("Location: " . LOGOUT_URL); // log user out
    }  
    // Delete another users account as an admin from admin.php
    else if ($_POST['form_type'] === 'admin_delete_user' && isAdmin() && isset($_POST['user_id']) && !empty($_POST['delete_checkbox_1'])) {
        $user_id = (int) $_POST['user_id'];
        deleteUser($pdo, $user_id); // Delete user from DB
        deleteUserDirectory($user_id); // Delete users DIR

        // Go back to user_search after user deleted
        $_SESSION['display_form'] = "user_search";
        $_SESSION['delete-success'] ="User deleted";
        echo json_encode(["success" => true, "message" => "User deleted"]);
    } 
    // Default if
    else {
        echo "Invalid request.";
        exit();
    }
}

?>