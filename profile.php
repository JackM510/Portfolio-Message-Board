<?php
    session_start();
    require_once('db_connection.php');
    require_once('utilities.php');

    //If user not logged in; redirect to login.php to login/signup
    if (isLoggedIn() === false) {
        header("Location: login.php");
    }

    // Set Profile details - get users name
    $stmt = $pdo->prepare("SELECT `first_name`, `last_name` FROM `users` WHERE `email` = :email");
    $stmt->bindParam(':email', $_SESSION['email'], PDO::PARAM_STR);
    $stmt->execute();
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $first_name = $user['first_name'] ?? 'Unknown';
    $last_name = $user['last_name'] ?? 'User';
    $full_name = $first_name.' '.$last_name;

    // Set profile details - get profile pic, bio, location etc.
    $stmt = $pdo->prepare("SELECT `bio`, `profile_picture`, `location` FROM `profiles` WHERE `user_id` = :user_id");
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    $profile_picture = $profile['profile_picture'] ?? 'uploads/default.png';
    $bio = $profile['bio'] ?? 'Set a bio';
    $location = $profile['location'] ?? 'Set your location';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <title>Profile</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <section class="container border mt-5">
        <div id="profile-details" class="d-flex justify-content-center mt-5">
            <img id="profile-picture" src="<?php echo htmlentities($profile_picture) ?>" style="width: 100px;"> 
            
            <div>
                <div>   
                    <h1 id="profile-name"><?php echo htmlentities($full_name) ?></h1>
                    <span></span>
                </div>
                <h4 id="profile-location"><?php echo htmlentities($location) ?></h4>
                <p id="profile-bio"><?php echo htmlentities($bio) ?></p>
            </div>
        
        </div>
        <div id="profile-new-post" class="justify-content-center mt-5">
            <!-- Section for a new post -->
             <h2>Create a new post</h2>
             <form action="profile.php" method="POST">
                <textarea id="new-post" required></textarea>
                <button class="btn btn-sm btn-primary" type="submit" name="post">Post</button>
             </form>
             
        </div>
        <div id="profile-posts" class="d-flex justify-content-center mt-5">
            <p>All users posts displayed here</p>
            <!-- List of posts by the user -->

            <!-- Display No posts if none made -->

        </div>
    </section>
</body>
</html>