<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
        $full_name = $_POST["full_name"];
        $location = $_POST["location"];
        $bio = $_POST["bio"];
    
        // Update the users first and last name
        // Need to split the first and last names......
        $name_parts = explode(" ", trim($_POST['full_name']), 2);
        $first_name = $name_parts[0]; // First name
        $last_name = isset($name_parts[1]) ? $name_parts[1] : "";

        $stmt = $pdo->prepare("UPDATE users SET first_name = :first, last_name = :last WHERE user_id = :uid");
        $stmt->bindParam(':first', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();

        // Update the users profile location and bio
        $stmt = $pdo->prepare("UPDATE profiles SET location = :location, bio = :bio WHERE user_id = :uid");    
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();

        if (isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["name"])) {
            $user_id = $_SESSION['user_id'];
            $userDir = "uploads/profiles/" . $user_id . "/";
            $profilePicDir = $userDir . "profile_picture/";

            // Ensure user folder exists
            if (!file_exists($userDir)) {
                mkdir($userDir, 0777, true);
            }

            if (!file_exists($profilePicDir)) {
                mkdir($profilePicDir, 0777, true);
            }

            // Retrieve existing profile picture path from db
            $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $existingPicture = $stmt->fetchColumn();

            // Delete the existing profile picture if it exists
            if (!empty($existingPicture) && file_exists($existingPicture)) {
                unlink($existingPicture); // Deletes the previous file
            }

            // Upload the new profile picture
            if (!empty($_FILES["profile_picture"]["name"])) {
                $fileName = basename($_FILES["profile_picture"]["name"]);
                $targetFilePath = $profilePicDir . $fileName; // Store profile pic inside profile folder
            
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                    // Update database with new profile picture URL
                    $stmt = $pdo->prepare("UPDATE profiles SET profile_picture = :profile_picture WHERE user_id = :user_id");
                    $stmt->bindParam(':profile_picture', $targetFilePath, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
        
        if ($stmt->execute()) {
            echo "success";
        } else {
            echo "Error updating profile: " . implode(" ", $stmt->errorInfo());
        }
    }
?>