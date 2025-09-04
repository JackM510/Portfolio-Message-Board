<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn() && isset($_POST['comment_id'])) {
    // Comment data
    $comment_id = $_POST['comment_id'];
    $edited_comment = $_POST['edit_comment']; 
    $edited_timestamp = date('Y-m-d H:i:s');
    // UPDATE comment stmt
    $stmt = $pdo->prepare("UPDATE comments SET comment_text = :comment_text, comment_edited = :comment_edited WHERE comment_id = :comment_id");
    $stmt->bindParam(':comment_text', $edited_comment, PDO::PARAM_STR);
    $stmt->bindParam(':comment_edited', $edited_timestamp, PDO::PARAM_STR);
    $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error editing comment: " . implode(" ", $stmt->errorInfo());
    }
}
?>
