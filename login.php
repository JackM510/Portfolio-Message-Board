<?php

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <title>Messageboard - Login</title>
</head>
<body>
    
    <?php require_once "nav.php"; ?>

    <div class="container" id="login-form">
        <div class="d-flex justify-content-center mt-3 mb-3">
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
    
    <div class="container" id="signup-form" style="display:none;">
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