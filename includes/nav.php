<?php
    // Temp til all adjustments made
    require_once __DIR__ . '/../config.php';

    $isLoggedIn = isset($_SESSION['user_id']) && isset($_SESSION['email']); // Check login status
    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    $navIcon = isset($_SESSION['avatar']) ? APP_BASE_PATH . "/" . $_SESSION['avatar'] : ICON_PROFILE;
?>

<nav class="navbar navbar-expand-lg sticky-top bg-light shadow-lg">
    <div class="container-fluid d-flex m-0">
        <div class="d-flex">
            <ul class="navbar-nav mx-auto">
                <li id="home-container" class="nav-item">
                    <a id="home-link" class="nav-link" href="<?= INDEX_URL; ?>">
                        <img id="home-icon" src="<?= ICON_HOME; ?>" alt="Home icon">Message Board
                    </a>
                </li>
            </ul>
        </div>

        <!-- Profile Section -->
        <?php if ($isLoggedIn): ?>
            <!-- Dropdown for logged-in users -->
            <div class="dropdown float-end">
                <a id="avatarDropdown" class="navbar-brand nav-profile m-0 dropdown-toggle" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <img id="avatar-icon" src="<?php echo htmlentities($navIcon) ?>" alt="Profile icon">
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
        <?php else: ?>
            <!-- Link to login.php for guests -->
            <a class="navbar-brand nav-profile m-0 float-end" href="<?= LOGIN_URL; ?>">
                <img src="<?= ICON_PROFILE; ?>" alt="Profile icon" style="width:40px;">
            </a>
        <?php endif; ?>
    </div>
</nav>
