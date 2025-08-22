<?php
    require_once __DIR__ . '/config.php';
    session_start();
    require_once(DB_INC);
    require_once(UTIL_INC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once (HEAD_INC); ?>
    <!-- Index styles -->
    <link href="<?= CSS_INDEX ?>" rel="stylesheet">
    <script>
        window.API = {
            addPost: "<?= ACTION_ADD_POST ?>",
            addComment: "<?= ACTION_ADD_COMMENT ?>",
            editPost: "<?= ACTION_EDIT_POST ?>",
            editComment: "<?= ACTION_EDIT_COMMENT?>",
            deletePost: "<?= ACTION_DELETE_POST ?>",
            deleteComment: "<?= ACTION_DELETE_COMMENT ?>",
            likePost: "<?= ACTION_LIKE_POST ?>",
            likeComment: "<?= ACTION_LIKE_COMMENT ?>",
        };
    </script>
    <!-- CSS & JS to create posts if user logged in -->
    <link href="<?= CSS_CREATE_POST ?>" rel="stylesheet">
    <script type="module" src="<?= JS_CREATE_POST ?>"></script>
    <!-- CSS & JS to fetch ALL posts from mysql -->
    <link href="<?= CSS_FETCH_POSTS ?>" rel="stylesheet">
    <script type="module" src="<?= JS_FETCH_POSTS?>"></script>
    <title>Message Board</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once (NAV_INC); ?>

    <!-- New post & ALL posts section -->
    <section id="index-container" class="container mx-auto mt-5">
        <div id="index-new-post">
            <?php 
                // Logic for heading above create post
                $heading_text = isset($_SESSION['first_name']) 
                    ? 'Hi ' . htmlspecialchars($_SESSION['first_name']) . ', share on Message Board!'
                    : '<a href="'.LOGIN_URL.'" class="nav-link" style="display: inline-block">Sign-in to post and comment!</a>';
                    
                echo '<div id="welcome-msg" class="text-center">
                        <h1 class="display-5">'. $heading_text .'</h1>
                    </div>';

                // Include the create post section
                if (isset($_SESSION['user_id'])) {
                    include (CREATE_POST_INC);
                }
            ?>    
        </div>
        <div id="index-all-posts">
            <?php
                include (FETCH_POSTS_INC);
                getPosts($pdo); //Fetch ALL posts in mysql
            ?>    
        </div>
    </section>
</body>
</html>