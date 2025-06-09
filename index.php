<?php
    session_start();
    require_once('includes/db_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <!-- Index styles -->
    <link href="css/index.css" rel="stylesheet">
    <!-- CSS & JS to create posts if user logged in -->
    <link href="css/create_post.css" rel="stylesheet">
    <script src="js/create_post.js"></script>
    <!-- CSS & JS to fetch ALL posts from mysql -->
    <link href="css/fetch_posts.css" rel="stylesheet">
    <script src="js/fetch_posts.js"></script>
    <title>Message Board</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>

    <!-- New post & ALL posts section -->
    <section id="index-container" class="container mx-auto mt-5">
        <div id="index-new-post">
            <?php 
                // Logic for heading above create post
                $heading_text = isset($_SESSION['first_name']) 
                    ? 'Hi ' . htmlspecialchars($_SESSION['first_name']) . ', share on Message Board!'
                    : '<a href="login.php">Sign-in</a> or <a href="login.php">Sign-up</a> to post and comment!';
                    
                echo '<div class="text-center">
                        <h1 class="display-5">'. $heading_text .'</h1>
                    </div>';

                // Include the create post section
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    include 'includes/create_post.php';
                }
            ?>    
        </div>
        <div id="index-all-posts" class="mt-5">
            <?php
                include 'includes/fetch_posts.php';
                getPosts($pdo); //Fetch ALL posts in mysql
            ?>    
        </div>
    </section>
</body>
</html>