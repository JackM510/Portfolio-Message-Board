<?php
    session_start();
    require_once('includes/utils/utilities.php');
    if (isLoggedIn() === false && !isset($_SESSION['role'])) {
        header("Location: login.php");
    }

    require_once('includes/db_connection.php');
    
    // Retrieve all users from mysql - FOR SEARCH -------TEMPORARY STATEMENT FOR UI DESIGN---------------------
    $sql = "SELECT u.user_id, u.email, u.role, p.profile_id 
    FROM users u
    INNER JOIN profiles p ON u.user_id = p.user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/admin.css" rel="stylesheet">
    <script src="js/admin.js" type="module"></script>
    <title>Admin Panel</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    
    <section class="container d-flex flex-column justify-content-center">
        
        <!-- View a list of all users -->
        <div id="user-search" class="fade-in">
            <div class="mt-5 mb-5 text-center">
                <h1 class="display-5">Admin Control Panel</h1>
            </div>
            <!-- Delete success flash -->
            <?php if (isset($_SESSION['delete-success'])) { echo "<p class='success-flash'>".$_SESSION['delete-success']."</p>"; unset($_SESSION['delete-success']); } ?>
            <div class="scrollbox-wrapper">
                <!-- Search Input -->
                <input type="text" id="user-search-input" class="form-control rounded-0" placeholder="Search users by profile id or email">
                <div class="scrollbox-header d-flex">
                    <div class="header-id">Profile ID</div>
                    <div class="header-email">Email</div>
                    <div class="header-role">Role</div>
                </div>

                <div class="scrollbox-container">
                    <?php foreach ($users as $user): ?>
                    <div class="user-row" data-user-id="<?= $user['profile_id'] ?>">
                        <div class="user-id"><?= htmlspecialchars($user['profile_id']) ?></div>
                        <div class="user-email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="user-role"><?= htmlspecialchars($user['role']) ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- View a users profile -->
        <div id="view-profile" class="fade-in" style="display:none;">
        
    
            <div class="view-profile-wrapper">
                <div class="position-relative d-flex justify-content-center align-items-center text-center mt-5 mb-5">
                    <!-- Return btn -->
                    <span class="position-absolute" style="top: 50%; transform: translateY(-50%); left:0;">
                        <button id="return-btn" class="btn btn-sm btn-secondary" title="Return to user list">
                            <i id="return-btn" class="bi bi-arrow-left"></i>
                        </button>           
                    </span>
                    <div>
                        <h1 class="display-5 mx-auto">Profile View</h1>
                    </div>      
                </div>
            
                <div class="row">
                    <!-- Profile Picture -->
                    <div class="col-12 mb-4">
                        <div class="d-flex flex-column justify-content-center align-items-center w-75 mx-auto">
                            <div class="d-flex flex-column justify-content-center w-25 h-100 mb-4">
                                <img id="profile-picture-img" class="mb-2" src="" alt="Profile Picture">
                            </div> 
                        </div>
                    </div>
                    <!-- First Name -->
                    <div class="col-12 col-lg-6 mb-3">
                        <label class="pb-1" for="last_name"><strong>First Name</strong></label>
                        <input id="first-name-input" class="form-control" type="text" name="" maxlength="20" disabled required>
                    </div>
                    <!-- Last Name -->
                    <div class="col-12 col-lg-6 mb-3">
                        <label class="pb-1" for=""><strong>Last Name</strong></label>
                        <input id="last-name-input" class="form-control" type="text" name="" maxlength="20" disabled required>
                    </div>
                    <!-- Profile ID -->
                    <div class="col-12 col-lg-6 mb-3">
                        <label class="pb-1" for=""><strong>Profile ID</strong></label>
                        <input id="profileid-input" class="form-control" type="text" name="" maxlength="50" disabled required>
                    </div>
                    <!-- Joined Date -->
                    <div class="col-12 col-lg-6 mb-3">
                        <label class="pb-1" for=""><strong>Joined Date:</strong></label>
                        <input id="joined-date-input" class="form-control" type="text" name="" maxlength="50" disabled required>
                    </div>
                    <!-- Email -->
                    <div class="col-12 col-lg-6 mb-3">
                        <label class="pb-1" for=""><strong>Email</strong></label>
                        <input id="email-input" class="form-control" type="text" name="" maxlength="50" disabled required>
                    </div>
                    <!-- Role -->
                    <div class="col-12 col-lg-6 mb-3">
                        <label class="pb-1" for=""><strong>Role</strong></label>
                        <input id="role-input" class="form-control" type="text" name="" maxlength="50" disabled required>
                    </div>
                </div>
        
                   
        
                <!-- BS5 Accordian --> 
                <div id="accordian" class="mt-4 mb-2 accordian">

                    <!-- Update email container-->
                    <div class="card">
                        <div class="card-header text-center">
                            <a class="btn" data-bs-toggle="collapse" href="#collapse-email">
                                Reset email
                            </a>
                        </div>
                        <div id="collapse-email" class="collapse" data-bs-parent="#accordian">
                            <div class="card-body">
                                <div id="update-email-body" class="mt-3 mx-auto">
                                    <h1 class="display-5 text-center mb-4">Update Email Address</h1>
                                    <?php if (isset($_SESSION['email-success'])) { echo "<p class='success-flash'>".$_SESSION['email-success']."</p>"; unset($_SESSION['email-success']); } ?>
                                    <form id="reset-email-form" method="POST" action="actions/update_email.php">
                                        <row>
                                            <!-- Hidden inputs -->
                                            <input type="hidden" name="form_type" value="admin_update_email" hidden>
                                            <input type="hidden" id="hidden-email-input" name="profile_id" value="" hidden>
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
                                Reset password
                            </a>
                        </div>
                        <div id="collapse-pw" class="collapse" data-bs-parent="#accordian">
                            <div class="card-body">
                                
                                <!-- Update password -->
                                <div id="update-pw-body" class="mt-3 mx-auto">
                                    <h1 class="display-5 text-center mb-4">Update Password</h1>
                                    <?php if (isset($_SESSION['pw-success'])) { echo "<p class='success-flash'>".$_SESSION['pw-success']."</p>"; unset($_SESSION['pw-success']); } ?>
                                    <form id="reset-pw-form" method="POST" action="actions/update_password.php">
                                        <div class="row">
                                            <!-- Hidden inputs -->
                                            <input type="hidden" name="form_type" value="admin_update_pw" hidden>
                                            <input type="number" id="hidden-pw-input" name="profile_id" value="" hidden>
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
                                Delete user
                            </a>
                        </div>
                        <div id="collapse-delete-account" class="collapse" data-bs-parent="#accordian">
                            <div class="card-body">
                                
                                <!-- Delete account -->
                                <div id="delete-account-body" class="mt-5 mx-auto d-flex flex-column">
                                    <h1 class="display-5 text-center mb-4">Delete Account</h1>
                                    <form id="delete-user-form" method="POST" action="actions/delete_user.php">
                                        <!-- Hidden inputs -->
                                        <input type="hidden" name="form_type" value="admin_delete_user" hidden>
                                        <input type="number" id="hidden-delete-input" name="profile_id" value="" hidden>
                                        <!-- Checkbox 1-->
                                        <div class="d-flex justify-content-center form-check mb-3">
                                            <input class="form-check-input required-checkbox me-2" type="checkbox" name="delete_checkbox_1">
                                            <label class="form-check-label">Yes - delete the user and all associated data.</label>
                                        </div>
                                        <!-- Submit btn -->
                                        <div class="d-flex mx-auto">
                                            <button id="delete-btn" class="btn btn-sm btn-danger mx-auto disabled" type="submit">Delete Account</button>
                                        </div> 
                                    </form>
                                </div>
                                
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        
    </div>        

          
    </div>
                        
    

    </section></br>

</body>
</html>