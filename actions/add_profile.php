<?php

session_start();
require_once('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $user_id = $_SESSION['id_token'];
    $location = $_POST['location'];
    $occupation = $_POST['occupation'];
    $bio = $_POST['bio'];

    // Check if the users profile picture DIR exists within the uploads/profiles/
    if (!empty($_FILES["profile_picture"]["name"])) {

        $userDir = "../uploads/profiles/{$user_id}/profile_picture/"; // Define user-specific directory from project root directory
        
        // Check if the user's profile_picture directory exists, if not, create it
        if (!file_exists($userDir)) {
            mkdir($userDir, 0777, true);
        }

        // Generate unique filename
        $newImageName = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $newImagePath = $userDir . $newImageName;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $newImagePath)) {
            
            $imageForDB = str_replace("../", "", $newImagePath); // Store relative path in DB
            // Update database with new profile picture URL
            $stmt = $pdo->prepare("INSERT INTO profiles (user_id, profile_picture, location, occupation, bio) VALUES (:user_id, :profile_picture, :location, :occupation, :bio)");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':profile_picture', $imageForDB, PDO::PARAM_STR);
            $stmt->bindParam(':location', $location, PDO::PARAM_STR);
            $stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
            $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
           
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
                // Navbar avatar
                $_SESSION['avatar'] = $data['profile_picture'];
        
                echo "success";
            } else {
                echo "Error updating profile: " . implode(" ", $stmt->errorInfo());
            }
        } else {
            echo "Error uploading image.";
            exit();
        }       
    } else {
        echo "No image uploaded.";
        exit();
    } 
}

?>