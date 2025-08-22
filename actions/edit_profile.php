<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

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

    // If a new image is uploaded
    if (isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["name"])) {

        $user_id = $_SESSION['user_id'];
        $profile_id = $_SESSION['profile_id'];

        // Filesystem directory for profile picture
        $uploadDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/profile_picture/";

        // Ensure directory exists before saving
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Retrieve existing profile picture path from db
        $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentImageURL = $data['profile_picture'];
        
        // Delete old image if it exists and isn't the default
        if (!empty($currentImageURL) && $currentImageURL !== DEFAULT_PROFILE_PIC) {
            // Normalise and map URL to filesystem path
            $oldImagePath = str_replace(
                URL_PROFILE_UPLOADS,
                DIR_PROFILE_UPLOADS,
                '/' . ltrim($currentImageURL, '/')
            );
            if (is_file($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Save new image
        $newImageName = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $newImagePath = $uploadDir . $newImageName;

        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $newImagePath)) {
            $imageForDB = URL_PROFILE_UPLOADS . "/{$profile_id}/profile_picture/{$newImageName}";
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