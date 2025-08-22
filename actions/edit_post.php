<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['post_id'])) {
    $user_id = $_SESSION['user_id'];
    $profile_id = $_SESSION['profile_id'];
    $post_id = $_POST['post_id'];
    $edited_text = $_POST['post_textarea'];
    $edited_timestamp = date('Y-m-d H:i:s');

    // Filesystem directory for this post
    $uploadDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/posts/{$post_id}/";

    // Get current image URL from DB
    $stmt = $pdo->prepare("SELECT post_picture FROM posts WHERE post_id = :post_id");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    $currentImageURL = $post['post_picture'];

    // If a new image is uploaded
    if (!empty($_FILES["post-image-upload"]["name"])) {
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $newImageName = uniqid() . "_" . basename($_FILES["post-image-upload"]["name"]);
        $newImagePath = $uploadDir . $newImageName;

        if (!empty($currentImageURL)) {
            // Convert web URL to filesystem path
            $oldImagePath = str_replace(URL_PROFILE_UPLOADS, DIR_PROFILE_UPLOADS, $currentImageURL);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath);
            }
        }


        if (move_uploaded_file($_FILES["post-image-upload"]["tmp_name"], $newImagePath)) {
            $imageForDB = URL_PROFILE_UPLOADS . "/{$profile_id}/posts/{$post_id}/{$newImageName}";
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        // Keep the current image if no new one is uploaded
        $imageForDB = $currentImageURL;
    }

    // Update post details in the database
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