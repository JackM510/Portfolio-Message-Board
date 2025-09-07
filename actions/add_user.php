<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);

// Check if specified email exists in the DB
function checkEmailExists(PDO $pdo, string $email): void {
    $stmt = $pdo->prepare("SELECT 1 FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->fetchColumn()) {
        $_SESSION['signup-email-error'] = "Email Address Already Exists";
        exit();
    }
}

// Validate the users age
function validateAge(string $date_of_birth): void {
    $age = date_diff(date_create($date_of_birth), date_create('today'))->y;
    if ($age < 18) {
        $_SESSION['signup-date-error'] = "You must be 18 or older to create an account";
        exit();
    }
}

// Attempt to INSERT new user into DB
function insertUser(PDO $pdo, string $first_name, string $last_name, string $date_of_birth, string $email, string $hashedPassword): bool {
    try {
        $stmt = $pdo->prepare("
            INSERT INTO `users` (`first_name`, `last_name`, `date_of_birth`, `email`, `password`)
            VALUES (:first_name, :last_name, :date_of_birth, :email, :password)
        ");
        $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':date_of_birth', $date_of_birth, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

// Create a new user/profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $_SESSION['display_form'] = "signup"; // stay on 'signup' until submission success
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $date_of_birth = $_POST['date_of_birth'];
    $email = trim(strtolower($_POST['email']));
    $newPW = $_POST['password'];
    $confirmPW = $_POST['confirm_password'];
    $isMatch = ($newPW === $confirmPW); // Check new passwords match
    checkEmailExists($pdo, $email); // Check if email already exists in DB
    validateAge($date_of_birth); // Validate new users age
    
    // Validate the new password
    $error = validatePassword($newPW);
    if ($error !== null) {
        $_SESSION['signup-password-error'] = $error;
        exit();
    }

    // Passwords match
    if ($isMatch) {
        $hashedPassword = password_hash($newPW, PASSWORD_DEFAULT); // Hash the new password
        // Attempt to INSERT new user       
        if (insertUser($pdo, $first_name, $last_name, $date_of_birth, $email, $hashedPassword)) {
            $_SESSION['display_form'] = "profile";
            $_SESSION['id_token'] = $pdo->lastInsertId();
            echo "success";
            exit();
        } 
        // INSERT failed
        else {
            echo "Error adding user.";
            exit();
        }
    } 
    // Password mismatch
    else {
        $_SESSION['signup-password-error'] = "New passwords don't match";
        exit();
    }
}
?>