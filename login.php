<?php
    session_start();
    require('db_connection.php');

    // If a user is already logged in
    if (isset($_SESSION['user_id']) && isset($_SESSION['first_name'])) {
        header("Location: index.php");
        exit();
    }

    // Function to validate login credentials
    // Include database connection
    include 'db_connection.php'; // Update with your DB connection file

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
        // Retrieve email and password from form
        $email = $_POST['email'];
        $password = $_POST['password'];

        try {
            // Prepare SQL query to get user by email
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Successful login: Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'];

                    // Redirect to dashboard or another page
                    header("Location: index.php");
                    exit();
                } else {
                    // Password doesn't match
                    echo "Invalid password.";
                }
            } else {
                // Email not found
                echo "No user found with this email address.";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }


    // Function to create a new user/profile
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signup'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password']; // Plaintext password must be hashed prior to INSERT
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT); // Hash the password

        try {
            $stmt = $pdo->prepare("INSERT INTO `users`(`first_name`, `last_name`, `email`, `password`) VALUES (:first_name, :last_name, :email, :password)");
            $stmt->bindParam(':first_name', $first_name, PDO::PARAM_STR);
            $stmt->bindParam(':last_name', $last_name, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':password', $hashedPassword, PDO::PARAM_STR); // Store the hashed password
            $stmt->execute();

            // Need to set SESSION variables to reach profile.php
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['first_name'] = $user['first_name'];

            // Need to set generic profile details - NEW SECTION ADDED IN
            $profile_picture_default = 'uploads/default.png';
            $profile_location_default = 'Add a location';
            $profile_bio_default = 'Add a bio';
            $stmt = $pdo->prepare("INSERT INTO `profiles` (user_id, profile_picture, location, bio) VALUES (:uid, :profile_picture, :profile_location, :profile_bio)");
            $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
            $stmt->bindParam(':profile_picture', $profile_picture_default, PDO::PARAM_STR);
            $stmt->bindParam(':profile_location', $profile_location_default, PDO::PARAM_STR);
            $stmt->bindParam(':profile_bio', $profile_bio_default, PDO::PARAM_STR);
            $stmt->execute();

            header("Location: profile.php");
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <title>Messageboard - Login</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <!-- Login form -->
    <div class="container mt-5" id="login-form">
        <div class="d-flex justify-content-center mt-5 mb-3">
            <h2>Login</h2>
        </div>
        <div class="d-flex justify-content-center">
            <form action="login.php" method="POST" class="w-50">
                <div class="row">
                    <div class="col-12 mb-3">
                        <input class="form-control form-control-lg" type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="col-12 mb-3">
                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <button class="btn btn-lg btn-primary" type="submit" name="login">Login</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="text-center mt-3" onclick="showSignUp()" style="cursor:pointer;">Don't have an account? Sign Up</div>
    </div>
    <!-- Signup form -->
    <div class="container mt-5" id="signup-form" style="display:none;">
        <div class="d-flex justify-content-center mt-3 mb-3">
            <h2>Sign Up</h2>
        </div>
        <div class="d-flex justify-content-center">
            <form action="login.php" method="POST" class="w-50">
                <div class="row">
                    <div class="col-12 mb-3">
                        <input class="form-control form-control-lg" type="text" name="first_name" placeholder="First Name" required>
                    </div>
                    <div class="col-12 mb-3">
                        <input class="form-control form-control-lg" type="text" name="last_name" placeholder="Last Name" required>
                    </div>
                    <div class="col-12 mb-3">
                        <input class="form-control form-control-lg" type="email" name="email" placeholder="Email" required>
                    </div>
                    <div class="col-12 mb-3">
                        <input class="form-control form-control-lg" type="password" name="password" placeholder="Password" required>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <button class="btn btn-lg btn-primary" type="submit" name="signup">Sign Up</button>
                    </div>
                </div>   
            </form>
        </div>
        <div class="text-center mt-3" onclick="showLogin()" style="cursor:pointer;">Already have an account? Login</div>
    </div>  
    <script>
        function showSignUp() {
            document.getElementById("login-form").style.display = "none";
            document.getElementById("signup-form").style.display = "block";
        }

        function showLogin() {
            document.getElementById("login-form").style.display = "block";
            document.getElementById("signup-form").style.display = "none";
        }
    </script>
</body>
</html>