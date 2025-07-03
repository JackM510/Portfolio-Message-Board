<?php

session_start();
require_once('../includes/db_connection.php');

// Function to create a new user/profile
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['display_form'] = "signup"; // show the sign up form and stay on the layout while the user is making changes

    $newPW = $_POST['password'];
    $confirmPW = $_POST['confirm_password'];
    $isMatch = ($newPW === $confirmPW); // Need to check that the passwords match

    // Need to validate the email before an insert statement
    $email = $_POST['email'];
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    // Flash an error message if users age is not >=18
    $age = date_diff(date_create($_POST['date_of_birth']), date_create('today'))->y;
    if ($age < 18) {
        $_SESSION['signup-age-error'] = "You must be 18 or older to create an account";
        header("Location: ../login.php");
        exit();
    }

    // Flash an error message if the email already exists
    if ($stmt->rowCount() > 0) {
        $_SESSION['signup-email-error'] = "Email Address Already Exists";
        header("Location: ../login.php");
        exit();
    } 
    // Flash error message if password is < 8 characters
    else if (strlen($_POST['password']) < 8) {
        $_SESSION['signup-password-error'] = "Password must be at least 8 characters";
        header("Location: ../login.php");
        exit();
    }
    // Flash error message if password contains <1 number
    else if (!preg_match('/\d/', $_POST['password'])) {
        $_SESSION['signup-password-error'] = "Password must contain at least 1 number";
        header("Location: ../login.php");
        exit();
    }
    // Flash error message if password contains <1 number
    else if (!preg_match('/[A-Z]/', $_POST['password'])) {
        $_SESSION['signup-password-error'] = "Password must contain at least 1 capital letter";
        header("Location: ../login.php");
        exit();
    }  

    // INSERT the new user into mysql
    if ($isMatch) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $date_of_birth = $_POST['date_of_birth'];
        $password = $_POST['password']; // Plaintext password must be hashed prior to INSERT
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        try {
            $stmt = $pdo->prepare("INSERT INTO `users`(`first_name`, `last_name`, `date_of_birth`, `email` , `password`) VALUES (:first_name, :last_name, :date_of_birth, :email, :password)");
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':date_of_birth', $date_of_birth, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); // Store the hashed password

            if ($stmt->execute()) {
                $_SESSION['display_form'] = "profile"; // Display the profile form to add profile details
                $user_id = $pdo->lastInsertId();
                $_SESSION['user_id'] = $user_id; // Store the user_id for future mysql INSERTS 
                echo "success";
            } else {
                echo "Error adding user: " . implode(" ", $stmt->errorInfo());
            } 

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
        
    } else {
        $_SESSION['signup-password-error'] = "New passwords don't match";
        //header("Location: ../login.php");
        exit();
    }
}

?>