<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];
    $edited_comment = $_POST['edit_comment']; // Retrieve edited text

    $edited_timestamp = date('Y-m-d H:i:s');


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
