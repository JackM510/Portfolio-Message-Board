<?php
    session_start();
    // Logic to determine whether a user is logged in
        // If logged in; can post and comment.
        // Else can't comment or post


    // Display all posts sorted from newest to oldest - may need to limit posts to e.g., 20 at a time unless users keeps scrolling

    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <title>Message Board</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <section class="container text-center">
        <h1>Message Board</h1>
    </section>

    <!-- Example Post structure -->
    <section>
        <div>
            <div id="content">
                
            </div>
            <div id="comments">

            </div>
            <div id="add_comments">

            </div>
        </div>
    </section>


</body>
</html>