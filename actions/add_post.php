<?php

session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["post_content"])) {
    $post_content = $_POST["post_content"];
    $image_url = null; // Default img url if an img is not uploaded
    $success = false; // Track success status of mysql INSERTs

    // Prepare SQL post
    $stmt = $pdo->prepare("INSERT INTO posts (user_id, post_text) VALUES (:uid, :post_text)");
    $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->bindParam(':post_text', $post_content, PDO::PARAM_STR);
    if ($stmt->execute()) {
        $success = true;
    }

     // Check if an image was uploaded
    if (!empty($_FILES["image"]["name"])) {

        $user_id = $_SESSION['user_id']; // Get the user's ID
        $profile_id = $_SESSION['profile_id'];
        
        $userDir = "../uploads/profiles/" . $profile_id . "/"; // Define user-specific directory from project root directory

        // Check if the user's directory exists, if not, create it
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true); // Create user's main folder
        }

        $post_id = $pdo->lastInsertId(); // Get new post ID
        $postDir = $userDir . "posts/" . $post_id . "/"; // Define post-specific folder
    
        // Ensure the post directory is created **only if an image is being uploaded**
        if (!file_exists($postDir)) {
            mkdir($postDir, 0777, true); // Create post folder
        }
    
        // Move the image into the post folder
        $fileName = basename($_FILES["image"]["name"]);
        $targetFilePath = $postDir . $fileName;
    
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
            $stored_filepath = substr($targetFilePath, 3); // Strip the '../' from the file path so the stored path in mysql is accurate
            $image_url = $stored_filepath;
    
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