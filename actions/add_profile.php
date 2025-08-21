<?php

session_start();
require_once('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES["profile_picture"]["name"])) {
    $user_id = $_SESSION['id_token']; // Store temp user token
    // Profile data
    $location = $_POST['location'];
    $occupation = $_POST['occupation'];
    $bio = $_POST['bio'];

    // Insert all profile data except profile picture to generate a profile_id 
    if ($location && $occupation && $bio) { 
        $stmt = $pdo->prepare("INSERT INTO profiles (user_id, location, occupation, bio) VALUES (:user_id, :location, :occupation, :bio)");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
        
        // Check the stmt has executed correctly
        if (!$stmt->execute()) {
            echo "Error inserting profile: " . implode(" ", $stmt->errorInfo());
            exit();
        }

        // Get the newly generated profile_id
        $profile_id = (int)$pdo->lastInsertId();
        if ($profile_id <= 0 || !$profile_id) {
            echo "Could not retrieve profile_id.";
            exit();
        }

        // Define user-specific directory
        $userDir = "../uploads/profiles/{$profile_id}/profile_picture/";
        // Check if the user's profile_picture directory exists, if not, create it
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }

        // Generate a unique filename for the profile_picture
        $newImageName = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $newImagePath = $userDir . $newImageName;

        // Move the profile_picture to the newly created profile directory
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $newImagePath)) {
            $imageForDB = str_replace("../", "", $newImagePath); // Store a relative pathway for the profile_picture in mysql

            // Insert the profile_picture into the users profile
            $stmt = $pdo->prepare("
                UPDATE profiles
                SET profile_picture = :profile_picture
                WHERE profile_id = :profile_id
            ");

            $stmt->bindParam(':profile_picture', $imageForDB, PDO::PARAM_STR);
            $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        
            if ($stmt->execute()) {
        
                $stmt = $pdo->prepare(" SELECT users.email, users.first_name, users.role, profiles.profile_id, profiles.profile_picture
                    FROM users
                    JOIN profiles ON users.user_id = profiles.user_id
                    WHERE users.user_id = :uid");
                $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);

                $_SESSION["user_id"] = $_SESSION["id_token"];
                unset($_SESSION["id_token"]);
                $_SESSION["profile_id"] = $data['profile_id'];
                $_SESSION["first_name"] = $data['first_name'];
                $_SESSION["email"] = $data['email'];
                $_SESSION["role"] = $data['role'];
                $_SESSION['avatar'] = $data['profile_picture']; // Navbar avatar
        
                echo "success";
            } else {
                echo "Error updating profile: " . implode(" ", $stmt->errorInfo());
                exit();
            }
        } else {
            echo "Error updating profile picture: " . implode(" ", $stmt->errorInfo());
            exit();
        }   
    } else {
        echo "Missing profile data";
        exit();
    }      
}
?>