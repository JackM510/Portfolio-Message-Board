<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isLoggedIn()) {
    // Profile data
    $user_id = $_SESSION['user_id'];
    $profile_id = $_SESSION['profile_id'];
    $first_name = $_POST["first_name"];
    $last_name = $_POST["last_name"];
    $location = $_POST["location"];
    $occupation = $_POST['occupation'];
    $bio = $_POST["bio"];
    $uploadDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/profile_picture/"; // Filesystem DIR for profile picture

    // UPDATE users & profiles tables
    $sql = "
        UPDATE users AS u
        JOIN profiles AS p ON u.user_id = p.user_id
        SET 
            u.first_name = :first,
            u.last_name  = :last,
            p.location   = :location,
            p.occupation = :occupation,
            p.bio        = :bio
        WHERE u.user_id = :uid
        ";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':first', $first_name, PDO::PARAM_STR);
    $stmt->bindParam(':last', $last_name, PDO::PARAM_STR);
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
    $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_STR);
    $stmt->execute();

    // If new img is uploaded
    if (isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["name"])) {
        // Check profile picture DIR exists
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Retrieve profile picture from db (if exists)
        $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :user_id");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->execute();
        $data = $stmt->fetch(PDO::FETCH_ASSOC);
        $currentImageURL = $data['profile_picture'];

        // Delete current profile picture
        if (!empty($currentImageURL) && $currentImageURL !== DEFAULT_PROFILE_PIC) {
            $oldImagePath = str_replace(URL_PROFILE_UPLOADS, DIR_PROFILE_UPLOADS, '/' . ltrim($currentImageURL, '/'));
            if (is_file($oldImagePath)) {
                unlink($oldImagePath);
            }
        }

        // Generate unique filename for new img
        $newImageName = uniqid() . "_" . basename($_FILES["profile_picture"]["name"]);
        $newImagePath = $uploadDir . $newImageName;

        // Add the new img to profile picture DIR
        if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $newImagePath)) {
            $imageForDB = URL_PROFILE_UPLOADS . "/{$profile_id}/profile_picture/{$newImageName}"; // Web URL for DB
            // UPDATE profile picture
            $stmt = $pdo->prepare("UPDATE profiles SET profile_picture = :profile_picture WHERE user_id = :user_id");
            $stmt->bindParam(':profile_picture', $imageForDB, PDO::PARAM_STR);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            // Update $_SESSION avatar icon on the navbar
            $_SESSION['avatar'] = $imageForDB;
        } else {
            echo "Error uploading image.";
            exit();
        }       
    }

    if ($stmt->execute()) {
        $_SESSION["first_name"] = $first_name; // Update $_SESSION['first_name']
        echo "success";
    } else {
        echo "Error updating profile: " . implode(" ", $stmt->errorInfo());
    }
}
?>