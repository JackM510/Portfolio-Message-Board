<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn() && isset($_POST['comment_id']) && isset($_POST['delete_comment'])) {
    // DELETE the entire comment
    $comment_id = $_POST['comment_id'];
    $stmt = $pdo->prepare("DELETE FROM comments WHERE comment_id = :comment_id");
    $stmt->bindParam(':comment_id', $comment_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error deleting comment: " . implode(" ", $stmt->errorInfo());
    }
}
?>