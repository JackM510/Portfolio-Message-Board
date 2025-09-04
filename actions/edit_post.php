<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn() && isset($_POST['post_id'])) {
    // Post data
    $user_id = (int) $_SESSION['user_id'];
    $profile_id = (int) $_SESSION['profile_id'];
    $post_id = (int) $_POST['post_id'];
    $edited_text = $_POST['post_textarea'];
    $edited_timestamp = date('Y-m-d H:i:s');
    $uploadDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/posts/{$post_id}/"; // Declare filesystem DIR for this post

    // Get current image URL from DB (if exists)
    $stmt = $pdo->prepare("SELECT post_picture FROM posts WHERE post_id = :post_id");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);
    $currentImageURL = $post['post_picture'];

    // If new img uploaded
    if (!empty($_FILES["post-image-upload"]["name"])) {
        // Check post upload DIR exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        // Generate unique filename for img
        $newImageName = uniqid() . "_" . basename($_FILES["post-image-upload"]["name"]);
        $newImagePath = $uploadDir . $newImageName;

        // Post img already exists
        if (!empty($currentImageURL)) {
            $oldImagePath = str_replace(URL_PROFILE_UPLOADS, DIR_PROFILE_UPLOADS, $currentImageURL); // Convert web URL to filesystem path
            // DELETE current post img 
            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }
        // Add the new img to post DIR
        if (move_uploaded_file($_FILES["post-image-upload"]["tmp_name"], $newImagePath)) {
            $imageForDB = URL_PROFILE_UPLOADS . "/{$profile_id}/posts/{$post_id}/{$newImageName}"; // Web URL for DB
        } else {
            echo "Error uploading image.";
            exit();
        }
    } 
    // Keep current img if a new one isn't uploaded
    else {
        $imageForDB = $currentImageURL;
    }

    // UPDATE post data
    $stmt = $pdo->prepare("UPDATE posts SET post_text = :post_text, post_picture = :post_picture, post_edited = :post_edited WHERE post_id = :post_id");
    $stmt->bindParam(':post_text', $edited_text, PDO::PARAM_STR);
    $stmt->bindParam(':post_picture', $imageForDB, PDO::PARAM_STR);
    $stmt->bindParam(':post_edited', $edited_timestamp, PDO::PARAM_STR);
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "Error updating post: " . implode(" ", $stmt->errorInfo());
    }
}
?>