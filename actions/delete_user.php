<?php
session_start();
require_once('../includes/db_connection.php');

// Function to delete entire user DIR recursively
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


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && !empty($_POST['delete_checkbox_1']) && !empty($_POST['delete_checkbox_2'])) {

    $user_id = $_SESSION['user_id'];

    // Check that the email doesn't already exist in the DB
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();

    deleteUserDirectory($user_id); // Delete users DIR
    header("Location: ../actions/logout_user.php"); // log the user out
}

?>