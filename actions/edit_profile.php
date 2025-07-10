<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {

    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];

    $location = $_POST["location"];
    $occupation = $_POST['occupation'];
    $bio = $_POST["bio"];

    // UPDATE 'user' table
    $stmt = $pdo->prepare("UPDATE users SET first_name = :first, last_name = :last WHERE user_id = :uid");
    $stmt->bindParam(':first', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
    $stmt->execute();

    // UPDATE 'profile' table
    $stmt = $pdo->prepare("UPDATE profiles SET location = :location, occupation = :occupation, bio = :bio WHERE user_id = :uid");    
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
    $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
    $stmt->execute();

    // Check is a new image is uploaded
    if (isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["name"])) {

        $user_id = $_SESSION['user_id'];

        // Define the correct directory structure
        $uploadDir = "../uploads/profiles/{$user_id}/profile_picture/";

        // Retrieve existing profile picture path from db
        $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $currentImagePath = $data['profile_picture']; // Store current image path
        
        // Ensure the directory exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Generate unique filename
        $newImageName = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $newImagePath = $uploadDir . $newImageName;

        // Delete previous image if it exists
        if (!empty($currentImagePath) && file_exists("../" . $currentImagePath)) {
            unlink("../" . $currentImagePath);
        }

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $newImagePath)) {
            $imageForDB = str_replace("../", "", $newImagePath); // Store relative path in DB
            // Update database with new profile picture URL
            $stmt = $pdo->prepare("UPDATE profiles SET profile_picture = :profile_picture WHERE user_id = :user_id");
            $stmt->bindParam(':profile_picture', $imageForDB, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();

            // Update the avatar icon on the navbar using $_SESSION
            $_SESSION['avatar'] = $imageForDB; // Need to move to outside if stmt

        } else {
            echo "Error uploading image.";
            exit();
        }       
    }
    
    if ($stmt->execute()) {
        $_SESSION["first_name"] = $first_name; // Sets name for index.php heading
        echo "success";
    } else {
        echo "Error updating profile: " . implode(" ", $stmt->errorInfo());
    }
}
?>