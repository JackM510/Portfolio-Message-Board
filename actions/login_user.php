<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

// Validate user login credentials
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $_SESSION['display_form'] = "login"; // Display 'login' view
    
    // Atempt login with specified email
    try {
        $stmt = $pdo->prepare("SELECT user_id, first_name, email, password, role FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Email exists
        if ($user) {
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Check user has setup their profile
                $stmt = $pdo->prepare("SELECT profile_id, profile_picture FROM profiles WHERE user_id = :uid");
                $stmt->bindParam(':uid', $user['user_id'], PDO::PARAM_STR);
                $stmt->execute();
                $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                // Profile not setup
                if (!$profile) {
                    $_SESSION['display_form'] = "profile"; // Display 'profile' view
                    $_SESSION['id_token'] = $user['user_id']; // Store as token for future INSERT
                    header("Location: " . LOGIN_URL);
                    exit();
                } 
                // Profile complete -> successful login
                else {
                    // Set $_SESSION variables
                    unset($_SESSION['display_form']);
                    $_SESSION['avatar'] = $profile['profile_picture'];
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['profile_id'] = $profile['profile_id'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['first_name'] = $user['first_name'];
                    $_SESSION['role'] = $user['role'];
                    // If 'Remember Me' checkbox selected - set cookie
                    if (!empty($_POST['remember_me'])) {
                        setcookie("user_login", $email, time() + (30 * 24 * 60 * 60), "/"); // Expires in 30 days
                    } 
                    // Clear cookie if unchecked
                    else {
                        setcookie("user_login", "", time() - 3600, "/"); 
                    }
                    // Redirect to dashboard
                    header("Location: " . LOGIN_URL);
                    exit();
                }
            } 
            // Password mismatch
            else {
                $_SESSION['login-password-error'] = "Invalid Password"; 
                header("Location: " . LOGIN_URL);
                exit();
            }
        } 
        // Email doesn't exist in DB
        else {
            $_SESSION['login-email-error'] = "Invalid Email Address";
            header("Location: " . LOGIN_URL);
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>