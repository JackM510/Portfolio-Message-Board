<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

// Attempt to INSERT profile into DB
function insertProfile(PDO $pdo, int $user_id, string $location, string $occupation, string $bio): int {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO profiles (user_id, location, occupation, bio)
            VALUES (:user_id, :location, :occupation, :bio)
        ");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':occupation', $occupation, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);

        if (!$stmt->execute()) {
            echo "Error inserting profile: " . implode(" ", $stmt->errorInfo());
            exit();
        }

        $profile_id = (int) $pdo->lastInsertId();
        if ($profile_id <= 0) {
            echo "Could not retrieve profile_id.";
            exit();
        }

        return $profile_id;

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

// Attempt to INSERT profile picture into DB
function insertProfilePic(PDO $pdo, int $profile_id, array $file): ?string {
    $userDir = DIR_PROFILE_UPLOADS . "/{$profile_id}/profile_picture/"; // Filesystem DIR for profile picture
    if (!is_dir($userDir)) {
        mkdir($userDir, 0777, true);
    }
    // Generate unique filename for profile picture
    $newImageName = uniqid() . "_" . basename($file['name']);
    $newImagePath = $userDir . $newImageName;

    // Move the profile picture to the users DIR
    if (move_uploaded_file($file["tmp_name"], $newImagePath)) {
        $imageForDB = URL_PROFILE_UPLOADS . "/{$profile_id}/profile_picture/{$newImageName}"; // Web URL for DB
        // INSERT profile picture
        $stmt = $pdo->prepare("
            UPDATE profiles
            SET profile_picture = :profile_picture
            WHERE profile_id = :profile_id
        ");
        $stmt->bindParam(':profile_picture', $imageForDB, PDO::PARAM_STR);
        $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
        // Success profile picture added
        if ($stmt->execute()) {
            return $imageForDB;
        } 
        // Error INSERT profile picture
        else {
            return null; 
        }
    } 
    // Failure moving profile picture
    else {
        return null; 
    }
}

// Set $_SESSION data and 
function setupSession(PDO $pdo, int $user_id): void {
    // Pull all required $_SESSION data
    $stmt = $pdo->prepare("
        SELECT users.email, users.first_name, users.role, profiles.profile_id, profiles.profile_picture
        FROM users
        JOIN profiles ON users.user_id = profiles.user_id
        WHERE users.user_id = :uid
    ");
    $stmt->bindParam(':uid', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    // $_SESSION data
    $_SESSION["user_id"] = $_SESSION["id_token"];
    unset($_SESSION["id_token"]);
    $_SESSION["profile_id"] = $data['profile_id'];
    $_SESSION["first_name"] = $data['first_name'];
    $_SESSION["email"] = $data['email'];
    $_SESSION["role"] = $data['role'];
    $_SESSION['avatar'] = $data['profile_picture'];
}

// Add profile POST submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_FILES["profile_picture"]["name"])) {
    $user_id = (int) $_SESSION['id_token']; // Temp ID token
    $location = trim($_POST['location']);
    $occupation = trim($_POST['occupation']);
    $bio = trim($_POST['bio']);

    // If all profile data provided
    if ($location && $occupation && $bio) { 
        $profile_id = insertProfile($pdo, $user_id, $location, $occupation, $bio); // INSERT profile into DB
        $imageForDB = insertProfilePic($pdo, $profile_id, $_FILES['profile_picture']); // INSERT profile picture into DB
        // INSERT success
        if ($profile_id && $imageForDB) {
            setupSession($pdo, $user_id); // Set $_SESSION data and direct to index.php
            echo "success";
        } 
        // INSERT error
        else {
            echo "Error inserting profile data";
            exit();
        }
    } 
    // Missing profile data
    else {
        echo "Missing profile data";
        exit();
    }      
}
?>