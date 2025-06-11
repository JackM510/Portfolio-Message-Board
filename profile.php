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
    $sql = "SELECT u.first_name, u.last_name, p.bio, p.profile_picture, p.location
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
    $location = $data['location'];
    $bio = $data['bio'];



    // ######### Update profile data through form
    /*if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profile-details'])) {
        $full_name = $_POST["full_name"];
        $location = $_POST["location"];
        $bio = $_POST["bio"];
    
        // Update the users first and last name
        // Need to split the first and last names......
        $name_parts = explode(" ", trim($_POST['full_name']), 2);
        $first_name = $name_parts[0]; // First name
        $last_name = isset($name_parts[1]) ? $name_parts[1] : "";

        $stmt = $pdo->prepare("UPDATE users SET first_name = :first, last_name = :last WHERE user_id = :uid");
        $stmt->bindParam(':first', $first_name, PDO::PARAM_STR);
        $stmt->bindParam(':last', $last_name, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();

        // Update the users profile location and bio
        $stmt = $pdo->prepare("UPDATE profiles SET location = :location, bio = :bio WHERE user_id = :uid");    
        $stmt->bindParam(':location', $location, PDO::PARAM_STR);
        $stmt->bindParam(':bio', $bio, PDO::PARAM_STR);
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->execute();

        if (isset($_FILES["profile_picture"]) && !empty($_FILES["profile_picture"]["name"])) {
            $user_id = $_SESSION['user_id'];
            $userDir = "uploads/profiles/" . $user_id . "/";
            $profilePicDir = $userDir . "profile_picture/";

            // Ensure user folder exists
            if (!file_exists($userDir)) {
                mkdir($userDir, 0777, true);
            }

            if (!file_exists($profilePicDir)) {
                mkdir($profilePicDir, 0777, true);
            }

            // Retrieve existing profile picture path from db
            $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->execute();
            $existingPicture = $stmt->fetchColumn();

            // Delete the existing profile picture if it exists
            if (!empty($existingPicture) && file_exists($existingPicture)) {
                unlink($existingPicture); // Deletes the previous file
            }

            // Upload the new profile picture
            if (!empty($_FILES["profile_picture"]["name"])) {
                $fileName = basename($_FILES["profile_picture"]["name"]);
                $targetFilePath = $profilePicDir . $fileName; // Store profile pic inside profile folder
            
                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $targetFilePath)) {
                    // Update database with new profile picture URL
                    $stmt = $pdo->prepare("UPDATE profiles SET profile_picture = :profile_picture WHERE user_id = :user_id");
                    $stmt->bindParam(':profile_picture', $targetFilePath, PDO::PARAM_STR);
                    $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                    $stmt->execute();
                }
            }
        }
        header("Location: profile.php"); // temp line to update profile pic
    }*/
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