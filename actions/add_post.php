<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_content"])) {
    $post_content = $_POST["post_content"];
    $image_url = null; // Default img url if an img is not uploaded
    $success = false; // Track success status of mysql INSERTs

    // INSERT Post text
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, post_text) VALUES (:uid, :post_text)");
    $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':post_text', $post_content, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $success = true;
        $post_id = $pdo->lastInsertId(); // Get new post ID
    }

     // Check if an image was uploaded
    if (!empty($_FILES["image"]["name"])) {
        $profile_id = $_SESSION['profile_id'];
        $user_id = $_SESSION['user_id']; // Get the user's ID

        $userDir = DIR_PROFILE_UPLOADS . "/" . $profile_id . "/";

         // Check profile dir exists
        $userDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/";
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }
        // Check post dir exists
        $postDir = $userDir . "posts/{$post_id}/";
        if (!is_dir($postDir)) {
            mkdir($postDir, 0777, true);
        }
    
        // Move the image into the post folder
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $postDir . $fileName;
    
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // After move_uploaded_file succeeds:
             $image_url = URL_PROFILE_UPLOADS . "/{$profile_id}/posts/{$post_id}/{$fileName}";

            // Update the post record with the image URL
            $stmt = $pdo->prepare("UPDATE posts SET post_picture = :post_picture WHERE post_id = :post_id");
            $stmt->bindParam(':post_picture', $image_url, PDO::PARAM_STR);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                $success = true;
            } 
        }
    }
    
    if ($success) {
        echo "success";
    } else {
        echo "error adding post.";
    }
}
?>