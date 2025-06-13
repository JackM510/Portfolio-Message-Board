<?php
    session_start();
    require_once('includes/db_connection.php');
    require_once('utilities.php');

    // If user not logged in; redirect to login.php to login/signup
    if (isLoggedIn() === false) {
        header("Location: login.php");
    }

    $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $_SESSION['user_id'];

    // Set Profile details - get users full name and profile information
    $sql = "SELECT u.first_name, u.last_name, p.age, p.bio, p.profile_picture, p.location
        FROM users u
        JOIN profiles p ON u.user_id = p.user_id
        WHERE u.user_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handling default values
    $profile_picture = $data['profile_picture'];
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $full_name = $first_name . ' ' . $last_name;
    $age = $data['age'];
    $location = $data['location'];
    $bio = $data['bio'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/profile.css" rel="stylesheet">
    <script src="js/profile.js"></script>
    <!-- CSS & JS to create posts if user logged in -->
    <link href="css/create_post.css" rel="stylesheet">
    <script src="js/create_post.js"></script>
    <!-- CSS & JS to fetch posts from mysql for a specified user -->
    <link href="css/fetch_posts.css" rel="stylesheet">
    <script src="js/fetch_posts.js"></script>
    
    <title>Profile</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <section class="mt-5">
        <!-- Profile details section -->
        <div class="container-fluid">
            <?php
                // Display profile_details.php
                include 'includes/profile_details.php';
            ?>
        </div>
        <!-- Section posts -->
        <div id="profile-posts-container" class="container mx-auto">
            <div id="profile-new-post">
                <?php 
                    // Display create_post.php if user logged in
                    include 'includes/create_post.php';
                ?>    
            </div>   
            <div id="profile-users-posts" class="d-flex flex-column justify-content-center mt-5">
                <?php 
                    // Display all posts from the user
                    include 'includes/fetch_posts.php';
                    getPosts($pdo, $user_id); //Fetch ALL posts in mysql   
                ?>
            </div>
        </div> 
    </section>
</body>
</html>