<?php
    session_start();
    require_once('includes/db_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
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


    <section class="container w-50 mx-auto mt-5">
        <div>
            <?php 
                if (isset($_SESSION['user_id'])) {
                    $user_id = $_SESSION['user_id'];
                    include 'includes/create_post.php';
                }
            ?>    
        </div>
        <div>
            <?php
                include 'includes/fetch_posts.php';
                getPosts($pdo); //Fetch ALL posts in mysql
            ?>    
        </div>
    </section>
</body>
</html>