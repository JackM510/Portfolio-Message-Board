<?php
    session_start(); // Start the session
    $isLoggedIn = isset($_SESSION['name']) && isset($_SESSION['email']); // Check login status
?>

<nav class="navbar navbar-expand-lg sticky-top bg-light shadow-lg">
    <div class="container-fluid d-flex m-0">
        <div class="d-flex">
            <ul class="navbar-nav mx-auto">
                <li class="nav-item">
                    <a class="nav-link text-black fw-bold" href="index.php">
                        <img src="icon/forum.png" alt="Home icon" style="width:40px; color: black;" class="rounded-pill">Message Board
                    </a>
                </li>
            </ul>
        </div>

        <!-- Avatar Section -->
        <?php if ($isLoggedIn): ?>
            <!-- Dropdown for logged-in users -->
            <div class="dropdown float-end">
                <a class="navbar-brand m-0 dropdown-toggle" href="#" id="avatarDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <img src="icon/profile.png" alt="Avatar Logo" style="width:40px;" class="rounded-pill">
                </a>
                <ul class="dropdown-menu" aria-labelledby="avatarDropdown">
                    <li><a class="dropdown-item" href="edit-profile.php">Edit Profile</a></li>
                    <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                </ul>
            </div>
        <?php else: ?>
            <!-- Link to login.php for guests -->
            <a class="navbar-brand m-0 float-end" href="login.php">
                <img src="icon/profile.png" alt="Avatar Logo" style="width:40px;" class="rounded-pill">
            </a>
        <?php endif; ?>
        
    </div>
</nav>
