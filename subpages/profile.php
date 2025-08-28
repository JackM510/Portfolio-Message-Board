<?php
    require_once __DIR__ . '/../config.php';
    session_start();
    require_once(DB_INC);
    require_once(UTIL_INC);

    // If user not logged in; redirect to login.php to login/signup
    if (isLoggedIn() === false) {
        header("Location: " . LOGIN_URL);
    }

    $profile_id = isset($_GET['profile_id'])
        ? (int)$_GET['profile_id']
        : (int)$_SESSION['profile_id'];

    // Set Profile details - get users full name and profile information
    $sql = "SELECT 
            u.first_name,
            u.last_name,
            u.created_at,
            u.date_of_birth,
            p.location,
            p.occupation,
            p.bio,
            p.profile_picture
        FROM users AS u
        JOIN profiles AS p 
            ON u.user_id = p.user_id
        WHERE p.profile_id = :profile_id
        ";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Full name
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $full_name = $first_name . ' ' . $last_name;
    // Joined Date
    $joinedDAT = $data['created_at'];
    $joined = date('F j, Y', strtotime($joinedDAT));
    // Age
    $dob = $data['date_of_birth'];
    $age = date_diff(date_create($dob), date_create('today'))->y;

    // Handling profile values
    $profile_picture = $data['profile_picture'];
    $location = $data['location'];
    $occupation = $data['occupation'];
    $bio = $data['bio'];

    $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    // Admin specific data - get if signed in:
    if ($isAdmin) {
        $sql = "SELECT u.email, p.profile_id
            FROM users AS u
            JOIN profiles AS p 
                ON u.user_id = p.user_id
            WHERE p.profile_id = :profile_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
            $stmt->execute();
            $adminData = $stmt->fetch(PDO::FETCH_ASSOC);

        $email = $adminData['email'];
        $profileId = $adminData['profile_id'];
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once (HEAD_INC); ?>
    <link href="<?= CSS_PROFILE ?>" rel="stylesheet">
    <script>
        window.API = {
            editProfile: "<?= ACTION_EDIT_PROFILE ?>",
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
    <script type="module" src="<?= JS_PROFILE ?>"></script>
    <!-- CSS & JS to create posts if user logged in -->
    <link href="<?= CSS_CREATE_POST ?>" rel="stylesheet">
    <script type="module" src="<?= JS_CREATE_POST ?>"></script>
    <!-- CSS & JS to fetch posts from mysql for a specified user -->
    <link href="<?= CSS_FETCH_POSTS ?>" rel="stylesheet">
    <script type="module" src="<?= JS_FETCH_POSTS ?>"></script>
   
    <title>Profile</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once (NAV_INC); ?>
    <section>
        <!-- Profile details section -->
        <div id="profile-container" class="container-fluid pt-5" style="background-color: #FAFAFA;">
            <?php
                // Display profile_details.php
                include (PROFILE_DETAILS_INC);
            ?>
        </div>
        <!-- Section posts -->
        <div id="profile-posts-container" class="container mx-auto">
            <div id="profile-new-post">
                <?php 
                    // Display create_post.php if user logged in
                    include (CREATE_POST_INC);
                ?>    
            </div>   
            <div id="profile-users-posts" class="d-flex flex-column justify-content-center">
                <?php 
                    // Display all posts from the user
                    include (FETCH_POSTS_INC);
                    getPosts($pdo, $profile_id); //Fetch ALL posts in mysql   
                ?>
            </div>
        </div> 
    </section>
</body>
</html>