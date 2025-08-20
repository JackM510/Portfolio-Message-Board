<?php
    session_start();
    require_once('includes/db_connection.php');
    require_once('includes/utils/utilities.php');

    // If user not logged in; redirect to login.php to login/signup
    if (isLoggedIn() === false) {
        header("Location: login.php");
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
    <?php require_once "head.php"; ?>
    <link href="css/profile.css" rel="stylesheet">
    <script type="module"src="js/profile.js"></script>
    <!-- CSS & JS to create posts if user logged in -->
    <link href="css/create_post.css" rel="stylesheet">
    <script type="module" src="js/create_post.js"></script>
    <!-- CSS & JS to fetch posts from mysql for a specified user -->
    <link href="css/fetch_posts.css" rel="stylesheet">
    <script type="module" src="js/fetch_posts.js"></script>
   
    <title>Profile</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <section>
        <!-- Profile details section -->
        <div id="profile-container" class="container-fluid pt-5" style="background-color: #FAFAFA;">
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
            <div id="profile-users-posts" class="d-flex flex-column justify-content-center">
                <?php 
                    // Display all posts from the user
                    include 'includes/fetch_posts.php';
                    getPosts($pdo, $profile_id); //Fetch ALL posts in mysql   
                ?>
            </div>
        </div> 
    </section>
</body>
</html>