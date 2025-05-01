<?php
    session_start();
    require_once('../includes/db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['comment_id']) && isset($_POST['delete_comment'])) {
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