<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['post_id']) && isset($_POST['delete_post'])) {
    $post_id = $_POST['post_id'];

    //Retrieve the image path before deleting the post
    $stmt = $pdo->prepare("SELECT post_picture FROM posts WHERE post_id = :post_id");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($post && !empty($post['post_picture'])) {
         $currentImageURL = $post['post_picture'];

        // Convert web URL to filesystem path
        $imagePath = str_replace(URL_PROFILE_UPLOADS, DIR_PROFILE_UPLOADS, $currentImageURL);

        // Delete the image file if it exists
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Remove post folder if empty
        $postFolder = dirname($imagePath);
        if (is_dir($postFolder)) {
            $files = array_diff(scandir($postFolder), ['.', '..']);
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