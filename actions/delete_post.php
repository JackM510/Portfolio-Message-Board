<?php
    session_start();
    require_once('../includes/db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['post_id']) && isset($_POST['delete_post'])) {
        $post_id = $_POST['post_id'];

        $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = :post_id");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error deleting post: " . implode(" ", $stmt->errorInfo());
        }
    }

?>