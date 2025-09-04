<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn() && isset($_POST['post_id']) && isset($_POST['comment_text'])) {
    $user_id = $_SESSION['user_id'];
    $post_id = $_POST['post_id'];
    $comment_text = trim($_POST['comment_text']);

    if (!empty($comment_text)) {
        // Insert comment into the database
        $sql = "INSERT INTO comments (post_id, user_id, comment_text) VALUES (:post_id, :user_id, :comment_text)";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error adding comment.";
        }
    } 
}
?> 