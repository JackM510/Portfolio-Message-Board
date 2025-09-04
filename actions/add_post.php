<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn() && isset($_POST["post_content"])) {
    $post_content = $_POST["post_content"];
    $user_id = $_SESSION['user_id'];
    $profile_id = $_SESSION['profile_id'];
    $image_url = null; // Default img url
    $success = false; // Track success of mysql INSERT

    // INSERT post text
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, post_text) VALUES (:uid, :post_text)");
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->bindParam(':post_text', $post_content, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $success = true;
        $post_id = $pdo->lastInsertId();
    }

    // Check if img uploaded to post
    if (!empty($_FILES["image"]["name"])) {
        // Define users profile dir
        $userDir = DIR_PROFILE_UPLOADS . "/" . $profile_id . "/";
        // Check the profile dir exists
        $userDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/";
        if (!is_dir($userDir)) {
            mkdir($userDir, 0777, true);
        }
        // Check post dir exists within profile dir
        $postDir = $userDir . "posts/{$post_id}/";
        if (!is_dir($postDir)) {
            mkdir($postDir, 0777, true);
        }
        // Move the img into the post folder
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $postDir . $fileName;
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            // Store the img URL for sql UPDATE
            $image_url = URL_PROFILE_UPLOADS . "/{$profile_id}/posts/{$post_id}/{$fileName}";
            // Update the post with the img URL
            $stmt = $pdo->prepare("UPDATE posts SET post_picture = :post_picture WHERE post_id = :post_id");
            $stmt->bindParam(':post_picture', $image_url, PDO::PARAM_STR);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $success = true;
            } 
        }
    }
    
    // Check if success adding new post data
    if ($success) {
        echo "success";
    } else {
        echo "error adding post.";
    }
}
?>