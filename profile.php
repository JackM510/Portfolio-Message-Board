<?php
    session_start();
    require_once('db_connection.php');
    require_once('utilities.php');

    // If user not logged in; redirect to login.php to login/signup
    if (isLoggedIn() === false) {
        header("Location: login.php");
    }

    // Set Profile details - get users full name and profile information
    $sql = "SELECT u.first_name, u.last_name, p.bio, p.profile_picture, p.location
        FROM users u
        JOIN profiles p ON u.user_id = p.user_id
        WHERE u.user_id = :user_id";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handling default values
    $first_name = $data['first_name'] ?? 'Unknown';
    $last_name = $data['last_name'] ?? 'User';
    $full_name = $first_name . ' ' . $last_name;
    $bio = $data['bio'] ?? 'Set a bio';
    $profile_picture = $data['profile_picture'] ?? 'uploads/default.png';
    $location = $data['location'] ?? 'Set your location';



    // ######### Update profile data through form
    if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['new-post'])) {
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
    }

    // ###### Create a new post and insert into mysql
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new-post"])) {
        $post_content = $_POST["post_content"];
    
        // Insert post into database
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, post_text) VALUES (:uid, :post_text)");
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
        $stmt->bindParam(':post_text', $post_content, PDO::PARAM_STR);
        $stmt->execute();
    
        echo "Post created successfully!";
    }


    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/profile.css" rel="stylesheet">
    <script src="js/profile.js"></script>
    <title>Profile</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <section class="container w-50 mt-5">
        <form id="profile-form" action="profile.php" method="POST">
            <div id="profile-details-container" class="d-flex m-auto border mt-5">
                <!-- Profile Picture -->
                <div>
                    <img id="profile-picture" src="<?php echo htmlentities($profile_picture) ?>"> 
                </div>
                <!-- Profile Details -->
                <div class="d-flex flex-column justify-content-center">
                    <!-- Full Name -->
                    <div class="mb-2">   
                        <h1><input id="profile-name" type="text" name="full_name" value="<?php echo htmlentities($full_name); ?>" disabled></h1>
                    </div>
                    <!-- Location -->
                    <div class="mb-2">
                        <h4><input id="profile-location" type="text" name="location" value="<?php echo htmlentities($location); ?>" disabled></h4>
                    </div>
                    <!-- Bio -->
                    <div>
                        <p><textarea id="profile-bio" name="bio" disabled><?php echo htmlentities($bio); ?></textarea></p>
                    </div>   
                </div>
                <!-- Edit icon -->
                <div>
                    <span id="edit-icon">
                        <i class="bi bi-pencil"></i>
                    </span>
                </div>
            </div>
        </form>
        <div id="profile-new-post" class="mt-5">
            <!-- Section for a new post -->
             <form action="profile.php" method="POST" class="d-flex flex-column justify-content-center w-75 m-auto">
                <img id="new-post-img" class="mb-2" src="">
                <textarea id="new-post-textarea" class="mb-2" name="post_content" placeholder="Create a new post" rows="3" required></textarea>
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button"><i class="bi bi-card-image"></i></button>
                        <div id="new-post-btn-group">
                            <button id="cancel-post-btn" class="btn btn-sm btn-secondary ms-1" type="button" name="new-post">Cancel</button>
                            <button id="new-post-btn" class="btn btn-sm btn-primary ms-1" type="submit" name="new-post">Post</button>
                        </div>
                    </div>
             </form>
            <hr>
        </div>
        <div id="profile-posts" class="d-flex flex-column justify-content-center border mt-5">
            <?php 
                // ##### Display all posts from the user
                $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = :uid ORDER BY post_created DESC");
                $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_STR);
                $stmt->execute();

                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($posts) {
                    foreach ($posts as $post) {
                        echo('<div><p>'.htmlentities($post['post_text']).'</p>');
                        echo('<p>'.htmlentities($post['post_created']).'</p></div>');

                        if (!empty($post['post_picture'])) {
                            echo "<img src='" . htmlspecialchars($post['post_picture']) . "' alt='Post Image' style='max-width:100px;'>";
                        }

                        

                    }
                } else {
                    echo('<p>No Posts Available</p>');
                }
                        
            
            
            
            ?>
            <!-- List of posts by the user -->

            <!-- Display No posts if none made -->

        </div>
    </section>
</body>
</html>