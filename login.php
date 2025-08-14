<?php
    session_start();
    require_once('includes/db_connection.php');

    // If a user is already logged in
    if (isset($_SESSION['user_id']) && isset($_SESSION['first_name'])) {
        header("Location: index.php");
        exit();
    }

    $email = isset($_COOKIE['user_login']) ? $_COOKIE["user_login"] : ""; // Cookie for email address

    // Logic to keep the current form active after selecting a submit button
    $displayForm = isset($_SESSION['display_form']) ? $_SESSION['display_form'] : "login";
    unset($_SESSION['display_form']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/login.css" rel="stylesheet">
    <script type="module" src="js/login.js"></script>
    <title>Messageboard - Login</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <!-- Login form -->
    <div class="container mt-5" id="login-form" style="<?= $displayForm === 'login' ? 'display:block;' : 'display:none;' ?>">
        <div class="d-flex justify-content-center mt-5 mb-3">
            <h1 class="display-5">Login</h1>
        </div>
        <div class="d-flex justify-content-center">
            <form id="login-form" action="actions/login_user.php" method="POST">
                <div class="row">
                    <div class="col-12 mb-3">
                    <?php if (isset($_SESSION['login-email-error'])) { echo "<p class='error-flash'>".$_SESSION['login-email-error']."</p>"; unset($_SESSION['login-email-error']); } ?>
                        <label class="pb-1" for="email">Email</label>
                        <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="col-12 mb-3">
                    <?php if (isset($_SESSION['login-password-error'])) { echo "<p class='error-flash'>".$_SESSION['login-password-error']."</p>"; unset($_SESSION['login-password-error']); } ?>
                        <label class="pb-1" for="password">Password</label>
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <div>
                    <label>
                        <input type="checkbox" name="remember_me" <?= !empty($email) ? "checked" : "" ?>> Remember Me
                    </label>
                    </div>
                    <div class="col-12 d-flex justify-content-center mt-2">
                        <button class="btn btn btn-primary" type="submit" name="login">Login</button>
                    </div>
                </div>   
            </form>
        </div>
        <div id="signup-tab" class="text-center mt-3" style="cursor:pointer;">Don't have an account? Sign Up</div>
    </div>

    <!-- Signup form -->
    <div class="container mt-5" id="signup-container" style="<?= $displayForm === 'signup' ? 'display:block;' : 'display:none;' ?>">
        <div class="d-flex justify-content-center mt-3 mb-3">
            <h1 class="display-5">Sign Up</h1>
        </div>
        <div class="d-flex justify-content-center">
            <form id="signup-form" action="actions/add_user.php" method="POST">
                <div id="signup-form-container" class="row">
                    <div class="col-12 mb-3">
                        <label class="pb-1" for="first_name">First Name</label>
                        <input class="form-control" type="text" maxlength="20" name="first_name" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="pb-1" for="last_name">Last Name</label>
                        <input class="form-control" type="text" maxlength="20" name="last_name" required>
                    </div>
                    <div class="col-12 mb-3">
                    <?php if (isset($_SESSION['signup-email-error'])) { echo "<p class='error-flash'>".$_SESSION['signup-email-error']."</p>"; unset($_SESSION['signup-email-error']); } ?>
                        <label class="pb-1" for="email">Email</label>
                        <input class="form-control" type="email" name="email" required>
                    </div>
                    <div class="col-12 mb-3">
                    <?php if (isset($_SESSION['signup-age-error'])) { echo "<p class='error-flash'>".$_SESSION['signup-age-error']."</p>"; unset($_SESSION['signup-age-error']); } ?>
                        <label class="pb-1" for="date_of_birth">Date of Birth</label>
                        <input class="form-control" type="date" name="date_of_birth" required>
                    </div>
                    <div class="col-12 mb-3">
                    <?php if (isset($_SESSION['signup-password-error'])) { echo "<p class='error-flash'>".$_SESSION['signup-password-error']."</p>"; unset($_SESSION['signup-password-error']); } ?>
                        <label class="pb-1" for="password">New Password</label>    
                        <input class="form-control" type="password" name="password" required>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="pb-1" for="confirm_password">Confirm Password</label>    
                        <input class="form-control" type="password" name="confirm_password" required>
                    </div>
                    <div class="col-12 d-flex justify-content-center">
                        <button id="signup-btn" class="btn btn-primary" type="submit" name="signup">Sign Up</button>
                    </div>
                </div> 
            </form>
        </div>
        <div class="login-tab text-center mt-3" style="cursor:pointer;">Already have an account? Login</div>
    </div>

    <!-- Complete Profile form -->
    <div class="container mt-5" id="profile-container" style="<?= $displayForm === 'profile' ? 'display:block;' : 'display:none;' ?>">
        <div class="d-flex justify-content-center mt-3 mb-3">
            <h1 class="display-5">Complete Profile</h1>
        </div>
        <div class="d-flex justify-content-center">
            <form id="profile-form" method="POST" action="actions/add_profile.php">
                <div class="row">
                    <!-- Profile Picture -->
                    <div class="col-12 mb-4">
                        <input id="profile-picture-input" type="file" name="profile_picture" accept="image/*" disabled hidden>
                        <label id="profile-picture-label" for="profile-image-upload" class="d-flex flex-column justify-content-center align-items-center mb-2">
                            <div class="d-flex flex-column justify-content-center w-25 h-100 mb-2">
                                <img id="profile-picture-img" class="mb-2 rounded-pill" src="<?php echo !empty($profile_picture) ? htmlentities($profile_picture) : "uploads/default/profile_picture.png"; ?>" alt="Profile Picture">
                                <button id="profile-picture-btn" type="button" class="btn btn-sm btn-light mx-auto" title="Upload Profile Picture">
                                    <i class="bi bi-card-image" style="font-size: 18px;"></i>
                                </button>
                            </div>
                        </label>
                    </div>

                    <!-- Location -->
                    <div class="col-12 mb-3">
                        <label class="pb-1" for="location">Location</label>
                        <input id="location-input" class="form-control" type="location" name="location" maxlength="50" required>
                    </div>
                    <!-- Occupation -->
                    <div class="col-12 mb-3">
                        <label class="pb-1" for="occupation">Occupation</label>
                        <input id="occupation-input" class="form-control" type="text" name="occupation" maxlength="50" required>
                    </div>
                    <!-- Bio -->
                    <div class="col-12 mb-4">
                        <label class="pb-1" for="bio">Bio</label>
                        <textarea id="bio-textarea" class="form-control responsive-textarea" name="bio" maxlength="250" rows="1" required></textarea>
                    </div>
                    <!-- Complete profile btn -->
                    <div class="col-12 d-flex justify-content-center">
                        <button id="create-profile-btn" class="btn btn-primary mx-1" type="submit" name="create_profile">Create Profile</button>
                    </div>
                </div>  
            </form>
        </div>
        <div class="login-tab text-center mt-3" style="cursor:pointer;">Already have an account? Login</div>
    </div>
</body>
</html>