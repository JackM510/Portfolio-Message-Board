<?php
    session_start(); // Start the session
    $isLoggedIn = isset($_SESSION['name']) && isset($_SESSION['email']); // Check login status
?>

<nav class="navbar navbar-expand-lg bg-light navbar-light shadow-lg sticky-top">
    <div class="container d-flex">
        <div class="d-flex justify-content-center w-100">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-black" href="index.php">
                        <img src="icon/home-fill.png" alt="Home icon" style="width:40px;" class="rounded-pill">
                    </a>
                </li>
            </ul>
        </div>

        <!-- Avatar Section -->
        <?php if ($isLoggedIn): ?>
            <!-- Dropdown for logged-in users -->
            <div class="dropdown">
                <a class="navbar-brand m-0 dropdown-toggle" href="#" id="avatarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="img/nav/avatar.png" alt="Avatar Logo" style="width:40px;" class="rounded-pill">
                </a>
                <ul class="dropdown-menu" aria-labelledby="avatarDropdown">
                    <li><a class="dropdown-item" href="edit-profile.php">Edit Profile</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <!-- Link to login.php for guests -->
            <a class="navbar-brand m-0" href="login.php">
                <img src="img/nav/avatar.png" alt="Avatar Logo" style="width:40px;" class="rounded-pill">
            </a>
        <?php endif; ?>
        
    </div>
</nav>
