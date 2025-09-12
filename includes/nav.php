<?php
    $isLoggedIn = isLoggedIn(); // Check login status
    $isAdmin = isAdmin(); // Check whether user is an admin
    $navIcon = isset($_SESSION['avatar']) ? APP_BASE_PATH . "/" . htmlentities($_SESSION['avatar']) : ICON_PROFILE;
?>

<nav class="navbar navbar-expand-lg sticky-top bg-light shadow-lg">
    <div class="container-fluid d-flex m-0">
        <!-- Messageboard icon & home link -->
        <div class="d-flex">
            <ul class="navbar-nav mx-auto">
                <li id="brand-container" class="nav-item">
                    <a id="brand-link" class="nav-link" href="<?= INDEX_URL; ?>">
                        <img id="brand-icon" src="<?= ICON_HOME; ?>" alt="Brand icon">Messageboard
                    </a>
                </li>
            </ul>
        </div>

        <!-- Avatar dropdown if logged in -->
        <?php if ($isLoggedIn): ?>
            <div class="dropdown float-end">
                <a id="avatarDropdown" class="navbar-brand nav-profile m-0 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img id="profile-icon" src="<?= $navIcon ?>" alt="Profile icon">
                </a>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="avatarDropdown">
                    <li><a class="dropdown-item nav-dropdown-item" href="<?= PROFILE_URL; ?>">View Profile</a></li>
                    <?php if ($isAdmin): ?>
                        <li><a class="dropdown-item nav-dropdown-item" href="<?= ADMIN_URL; ?>">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item nav-dropdown-item" href="<?= ACCOUNT_URL; ?>">Account Settings</a></li>
                    <li><a class="dropdown-item nav-dropdown-item text-danger" href="<?= LOGOUT_URL; ?>">Logout</a></li>
                </ul>
            </div>
            
        <!-- Not logged in - avatar redirect to login.php -->
        <?php else: ?>
            <a class="navbar-brand nav-profile float-end m-0" href="<?= LOGIN_URL; ?>">
                <img id="avatar-icon" src="<?= ICON_PROFILE; ?>" alt="Avatar icon">
            </a>
        <?php endif; ?>
    </div>
</nav>