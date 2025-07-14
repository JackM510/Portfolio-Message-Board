<?php
    session_start();
    require_once('utilities.php');
    if (isLoggedIn() === false) {
        header("Location: login.php");
    }

    require_once('includes/db_connection.php');

    

    // Delete a user
    if (isset($_POST['delete_user_id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $_POST['delete_user_id'], PDO::PARAM_INT);
            $stmt->execute();
    
            echo "User and associated data deleted successfully.";
        } catch (PDOException $e) {
            echo "Error deleting user: " . $e->getMessage();
        }
    }
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/account.css" rel="stylesheet">
    <script src="js/account.js"></script>
    <title>Account Management</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <div class="mt-5 text-center mb-5">
        <h1 class="display-5">Account Management</h1>
    </div>


    <!-- BS5 Accordian --> 
    <div id="accordian" class="accordian container">
        <!-- Update email container-->
        <div class="card">
            <div class="card-header text-center">
                <a class="btn" data-bs-toggle="collapse" href="#collapse-email">
                    Change your email address
                </a>
            </div>
            <div id="collapse-email" class="collapse" data-bs-parent="#accordian">
                <div class="card-body">
                    <div id="update-email-body" class="mt-3 mx-auto">
                        <h1 class="display-5 text-center mb-4">Update Email Address</h1>
                        <?php if (isset($_SESSION['email-success'])) { echo "<p class='success-flash'>".$_SESSION['email-success']."</p>"; unset($_SESSION['email-success']); } ?>
                        <form id="update-email-form" method="POST" action="actions/update_email.php">
                            <row>
                                <!-- Current email -->
                                <div class="col-12 mb-3">
                                    <?php if (isset($_SESSION['invalid-email-error'])) { echo "<p class='error-flash'>".$_SESSION['invalid-email-error']."</p>"; unset($_SESSION['invalid-email-error']); } ?>
                                    <label class="pb-1" for="current_email">Current Email</label>
                                    <input class="form-control" type="email" name="current_email" required>
                                </div>
                                <!-- New email -->
                                <div class="col-12 mb-3">
                                    <?php if (isset($_SESSION['new-email-error'])) { echo "<p class='error-flash'>".$_SESSION['new-email-error']."</p>"; unset($_SESSION['new-email-error']); } ?>

                                    <label class="pb-1" for="new_email">New Email</label>
                                    <input class="form-control" type="email" name="new_email" required>
                                </div>
                                <!-- Confirm email -->
                                <div class="col-12 mb-4">
                                    <label class="pb-1" for="confirm_email">Confirm Email</label>
                                    <input class="form-control" type="email" name="confirm_email" required>
                                </div>
                                <!-- Submit btn -->
                                <div class="col-12 d-flex justify-content-center mb-2">
                                    <button class="btn btn-sm btn-primary" type="submit">Submit</button>
                                </div>
                            </row>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Update password container-->
        <div class="card">
            <div class="card-header text-center">
                <a class="btn" data-bs-toggle="collapse" href="#collapse-pw">
                    Change your password
                </a>
            </div>
            <div id="collapse-pw" class="collapse" data-bs-parent="#accordian">
                <div class="card-body">
                    
                    <!-- Update password -->
                    <div id="update-pw-body" class="mt-3 mx-auto">
                        <h1 class="display-5 text-center mb-4">Update Password</h1>
                        <?php if (isset($_SESSION['pw-success'])) { echo "<p class='success-flash'>".$_SESSION['pw-success']."</p>"; unset($_SESSION['pw-success']); } ?>
                        <form id="update-pw-form" method="POST" action="actions/update_password.php">
                            <div class="row">
                                <!-- Current password -->
                                <?php if (isset($_SESSION['invalid-password-error'])) { echo "<p class='error-flash'>".$_SESSION['invalid-password-error']."</p>"; unset($_SESSION['invalid-password-error']); } ?>
                                <div class="col-12 mb-3">
                                    <label class="pb-1" for="current_pw">Current Password</label>
                                    <input class="form-control" type="password" name="current_pw" required>
                                </div>
                                <!-- New password -->
                                <div class="col-12 mb-3">
                                <?php if (isset($_SESSION['update-password-error'])) { echo "<p class='error-flash'>".$_SESSION['update-password-error']."</p>"; unset($_SESSION['update-password-error']); } ?>
                                    <label class="pb-1" for="new_pw">New Password</label> 
                                    <input class="form-control" type="password" name="new_pw" required>
                                </div>
                                <!-- Confirm password -->
                                <div class="col-12 mb-4">
                                    <label class="pb-1" for="confirm_pw">Confirm Password</label>
                                    <input class="form-control" type="password" name="confirm_pw" required>
                                </div>
                                <!-- Submit btn -->
                                <div class="col-12 d-flex justify-content-center">
                                    <button class="btn btn-sm btn-primary" type="submit">Submit</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete account container-->
        <div class="card">
            <div class="card-header text-center">
                <a class="btn" data-bs-toggle="collapse" href="#collapse-delete-account">
                    Close your account
                </a>
            </div>
            <div id="collapse-delete-account" class="collapse" data-bs-parent="#accordian">
                <div class="card-body">
                    
                    <!-- Delete account -->
                    <div id="delete-account-body" class="mt-5 mx-auto d-flex flex-column">
                        <h1 class="display-5 text-center mb-4">Delete Account</h1>
                        <form method="POST" action="actions/delete_user.php">
                            <div class="form-check mb-3">
                                <input class="form-check-input required-checkbox" type="checkbox" name="delete_checkbox_1">
                                <label class="form-check-label">Yes - I really want to delete the account</label>
                            </div>
                            <div class="form-check mb-4">
                                <input class="form-check-input required-checkbox" type="checkbox" name="delete_checkbox_2">
                                <label class="form-check-label">I acknowledge once deleted the account cannot be restored and all data associated with the account will be removed.</label>
                            </div>
                            <div class="d-flex mx-auto">
                                <button id="delete-btn" class="btn btn-sm btn-danger mx-auto disabled" type="submit">Delete Account</button>
                            </div> 
                        </form>
                    </div>
                    
                </div>
            </div>
        </div>

    </div>
</body>
</html>