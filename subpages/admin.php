<?php
    require_once __DIR__ . '/../config.php';
    session_start();
    require_once(UTIL_INC);
    if (isLoggedIn() === false && !isset($_SESSION['role'])) {
        header("Location: ".LOGIN_URL);
    }
    require_once(DB_INC); 
    require(ACTION_GET_ALL_USERS);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once (HEAD_INC); ?>
    <link href="<?= CSS_ADMIN ?>" rel="stylesheet">
    <script>
        window.API = {
            getUser: "<?= ACTION_GET_USER ?>",
            updateEmail: "<?= ACTION_UPDATE_EMAIL ?>",
            updatePassword: "<?= ACTION_UPDATE_PASSWORD ?>",
            deleteUser: "<?= ACTION_DELETE_USER ?>",
            jsFadeEl: "<?= JS_PAGE_TRANSITIONS ?>",
            jsCheckboxes: "<?= JS_VALIDATE_CHECKBOXES ?>",
        };
    </script>
    <script src="<?= JS_ACCORDIAN ?>" defer></script>
    <script src="<?= JS_ADMIN ?>" type="module"></script>
    <title>Admin Panel</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once (NAV_INC); ?>
    <div class="container d-flex flex-column justify-content-center">
        <!-- View a list of all users -->
        <div id="user-search" class="fade-in">
            <div class="mt-5 mb-5 text-center">
                <h1 class="display-5">Admin Control Panel</h1>
            </div>
            <!-- Delete success flash -->
            <?php if (isset($_SESSION['delete-success'])) { echo "<p class='success-flash'>".$_SESSION['delete-success']."</p>"; unset($_SESSION['delete-success']); } ?>
            <div id="scrollbox-wrapper">
                <!-- Search Input -->
                <input id="user-search-input" class="form-control rounded-0" type="text" placeholder="Search users by profile id or email">
                <div id="scrollbox-header" class="d-flex">
                    <div class="header-uid">User ID</div>
                    <div class="header-pid">Profile ID</div>
                    <div class="header-email">Email</div>
                    <div class="header-role">Role</div>
                </div>
                <!-- User Scrollbox -->
                <div id="scrollbox-container">
                    <?php foreach ($users as $user): ?>
                    <div class="user-row" data-user-id="<?= $user['user_id'] ?>">
                        <div class="user-id"><?= htmlspecialchars($user['user_id']) ?></div>
                        <div class="profile-id"><?= htmlspecialchars($user['profile_id']) ?></div>
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
                <!-- Heading & Return Btn -->
                <div class="position-relative d-flex justify-content-center align-items-center text-center mt-5 mb-5">
                    <span id="return-span">
                        <button id="return-btn" class="btn btn-sm btn-secondary" title="Return to user list">
                            <i class="bi bi-arrow-left"></i>
                        </button>           
                    </span>
                    <div>
                        <h1 class="display-5 mx-auto">Profile View</h1>
                    </div>      
                </div>
            
                <div class="row">
                    <!-- First Name -->
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label class="pb-1"><strong>First Name</strong></label>
                        <input id="first-name-input" class="form-control" type="text" disabled>
                    </div>
                    <!-- Last Name -->
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label class="pb-1"><strong>Last Name</strong></label>
                        <input id="last-name-input" class="form-control" type="text" disabled>
                    </div>
                    <!-- Email -->
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label class="pb-1"><strong>Email</strong></label>
                        <input id="email-input" class="form-control" type="text" disabled>
                    </div>
                    <!-- User ID -->
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label class="pb-1"><strong>User ID</strong></label>
                        <input id="userid-input" class="form-control" type="text" disabled>
                    </div>
                    <!-- Profile ID -->
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label class="pb-1"><strong>Profile ID</strong></label>
                        <input id="profileid-input" class="form-control" type="text" disabled>
                    </div>
                    <!-- Joined Date -->
                    <div class="col-12 col-md-6 col-lg-4 mb-3">
                        <label class="pb-1"><strong>Joined Date:</strong></label>
                        <input id="joined-date-input" class="form-control" type="text" disabled>
                    </div>
                </div>
        
                <!-- BS5 Accordian --> 
                <div id="accordian" class="accordian mt-4 mb-2">
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
                                    <form id="reset-email-form" method="POST" action="<?= ACTION_UPDATE_EMAIL ?>">
                                        <row>
                                            <!-- Hidden inputs -->
                                            <input type="hidden" name="form_type" value="admin_update_email" hidden>
                                            <input id="hidden-email-input" type="hidden" name="user_id" value="" hidden>
                                            <!-- New email -->
                                            <div class="col-12 mb-3">
                                                <?php if (isset($_SESSION['new-email-error'])) { echo "<p class='error-flash'>".$_SESSION['new-email-error']."</p>"; unset($_SESSION['new-email-error']); } ?>
                                                <label class="pb-1" for="new_email">New Email</label>
                                                <input class="form-control" type="email" name="new_email" maxlength="50" required>
                                            </div>
                                            <!-- Confirm email -->
                                            <div class="col-12 mb-4">
                                                <label class="pb-1" for="confirm_email">Confirm Email</label>
                                                <input class="form-control" type="email" name="confirm_email" maxlength="50" required>
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
                                    <form id="reset-pw-form" method="POST" action="<?= ACTION_UPDATE_PASSWORD ?>">
                                        <div class="row">
                                            <!-- Hidden inputs -->
                                            <input type="hidden" name="form_type" value="admin_update_pw" hidden>
                                            <input id="hidden-pw-input" type="number" name="user_id" value="" hidden>
                                            <!-- New password -->
                                            <div class="col-12 mb-3">
                                            <?php if (isset($_SESSION['update-password-error'])) { echo "<p class='error-flash'>".$_SESSION['update-password-error']."</p>"; unset($_SESSION['update-password-error']); } ?>
                                                <label class="pb-1" for="new_pw">New Password</label> 
                                                <input class="form-control" type="password" name="new_pw" maxlength="25" required>
                                            </div>
                                            <!-- Confirm password -->
                                            <div class="col-12 mb-4">
                                                <label class="pb-1" for="confirm_pw">Confirm Password</label>
                                                <input class="form-control" type="password" name="confirm_pw" maxlength="25" required>
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
                                    <?php if (isset($_SESSION['delete-error'])) { echo "<p class='text-center error-flash'>".$_SESSION['delete-error']."</p>"; unset($_SESSION['delete-error']); } ?>
                                    <form id="delete-user-form" method="POST" action="<?= ACTION_DELETE_USER ?>">
                                        <!-- Hidden inputs -->
                                        <input type="hidden" name="form_type" value="admin_delete_user" hidden>
                                        <input id="hidden-delete-input" type="number" name="user_id" value="" hidden>
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
    </div></br>        
</body>
</html>