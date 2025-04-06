<?php
    session_start();
    require('db_connection.php');

    // If a user is already logged in
    if (isset($_SESSION['name']) && isset($_SESSION['email'])) {
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
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['role'] = $user['role'];

                // Redirect to dashboard or another page
                header("Location: dashboard.php");
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
    if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['password'])) {

        // If signup successful redirect to profile.php
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