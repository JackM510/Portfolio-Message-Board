<?php
    session_start();
    require_once('includes/db_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/posts.css" rel="stylesheet">
    <script src="js/posts.js"></script>
    <title>Message Board</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>

    <!-- Post section -->
    <section class="container w-50 mx-auto mt-5">
        <div>
            <?php
                include 'includes/posts.php';
                getPosts($pdo); //Fetch ALL posts in mysql
            ?>    
        </div>
    </section>
</body>
</html>