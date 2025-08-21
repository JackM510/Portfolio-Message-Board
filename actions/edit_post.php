<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && isset($_POST['post_id'])) {
    $user_id = $_SESSION['user_id'];
    $profile_id = $_SESSION['profile_id'];
    $post_id = $_POST['post_id'];
    $edited_text = $_POST['post_textarea']; // Retrieve edited text
    $edited_timestamp = date('Y-m-d H:i:s');

    // Define the correct directory structure
    $uploadDir = "../uploads/profiles/{$profile_id}/posts/{$post_id}/";

    // Get current image path before updating (to delete it if needed)
    $stmt = $pdo->prepare("SELECT post_picture FROM posts WHERE post_id = :post_id");
    $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
    $stmt->execute();
    $post = $stmt->fetch(PDO::FETCH_ASSOC);

    $currentImagePath = $post['post_picture']; // Store current image path

    // Check if a new image is uploaded
    if (!empty($_FILES["post-image-upload"]["name"])) {
        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true); // Create directories recursively
        }

        // Generate unique filename
        $newImageName = uniqid() . "_" . basename($_FILES["post-image-upload"]["name"]);
        $newImagePath = $uploadDir . $newImageName;

        // Delete previous image if it exists
        if (!empty($currentImagePath) && file_exists("../" . $currentImagePath)) {
            unlink("../" . $currentImagePath);
        }

        // Move the uploaded file to the correct directory
        if (move_uploaded_file($_FILES["post-image-upload"]["tmp_name"], $newImagePath)) {
            $imageForDB = str_replace("../", "", $newImagePath); // Store relative path in DB
        } else {
            echo "Error uploading image.";
            exit();
        }
    } else {
        // Keep the current image if no new one is uploaded
        $imageForDB = $currentImagePath;
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