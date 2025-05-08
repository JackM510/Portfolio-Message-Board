<?php
    session_start();
    require_once('../includes/db_connection.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['post_id']) && isset($_POST['delete_post'])) {
        $post_id = $_POST['post_id'];

        //Retrieve the image path before deleting the post
        $stmt = $pdo->prepare("SELECT post_picture FROM posts WHERE post_id = :post_id");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
        $stmt->execute();
        $post = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($post && !empty($post['post_picture'])) {
            $imagePath = "../" . $post['post_picture']; // Restore full path by adding '../'
    
            // Check if file exists & delete it
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Remove post folder if it's empty
            $postFolder = dirname($imagePath); // Get the folder path
            if (is_dir($postFolder)) {
                $files = array_diff(scandir($postFolder), ['.', '..']); // Get all files inside the folder

                // Ensure folder is empty before deleting
                if (empty($files)) {
                    rmdir($postFolder);
                }
            }
        }

        $stmt = $pdo->prepare("DELETE FROM posts WHERE post_id = :post_id");
        $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error deleting post: " . implode(" ", $stmt->errorInfo());
        }
    }

?>