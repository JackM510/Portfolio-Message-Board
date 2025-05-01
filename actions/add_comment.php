<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['comment_text'])) {
    $post_id = $_POST['post_id'];
    $user_id = $_SESSION['user_id'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        // Insert comment into the database
        $sql = "INSERT INTO comments (post_id, user_id, comment_text) VALUES (:post_id, :user_id, :comment_text)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "success"; // Send success response
        } else {
            echo "Error adding comment.";
        }
    } 
}
?> 