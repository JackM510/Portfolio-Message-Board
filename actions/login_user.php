<?php

session_start();
require_once('../includes/db_connection.php');

// Validate user login credentials
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {

        $_SESSION['display_form'] = "login";

        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // A user exists with the specified email
            if ($user) {
                // Verify the users password
                if (password_verify($password, $user['password'])) {
                    
                    // Check the user has finalised their profile before allowing them to use the application
                    $stmt = $pdo->prepare("SELECT profile_id, profile_picture FROM profiles WHERE user_id = :uid");
                    $stmt->bindParam(':uid', $user['user_id'], PDO::PARAM_STR);
                    $stmt->execute();
                    $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if (!$profile) {
                        // Redirect to dashboard after successful login
                        $_SESSION['display_form'] = "profile";
                        $_SESSION['user_id'] = $user['user_id'];
                        header("Location: ../login.php");
                        exit();
                    } else {
                        // get profile picture
                        $_SESSION['avatar'] = $profile['profile_picture'];
                        // Successful login - set $_SESSION variables
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['role'] = $user['role'];
                        
                        // If 'Remember Me' checkbox is selected
                        if (!empty($_POST['remember_me'])) {
                            setcookie("user_login", $email, time() + (30 * 24 * 60 * 60), "/"); // Expires in 30 days
                        } else {
                            setcookie("user_login", "", time() - 3600, "/"); // Clear cookie if unchecked
                        }

                        // Redirect to dashboard after successful login
                        header("Location: ../index.php");
                        exit();
                    }
                } else {
                    $_SESSION['login-password-error'] = "Invalid Password"; // Password doesn't match
                    header("Location: ../login.php");
                    exit();
                }
            } else {
                $_SESSION['login-email-error'] = "Invalid Email Address"; // A user doesn't exist with the specifed email
                header("Location: ../login.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>