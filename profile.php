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
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['profile-details'])) {
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
    }

    // ###### Create a new post and insert into mysql
    /*if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["new-post"])) {
        $post_content = $_POST["post_content"];
        $image_url = null; // Default img url if an img is not uploaded

        // Prepare SQL post
        $stmt = $pdo->prepare("INSERT INTO posts (user_id, post_text) VALUES (:uid, :post_text)");
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':post_text', $post_content, PDO::PARAM_STR);
        $stmt->execute();

         // Check if an image was uploaded
        if (!empty($_FILES["image"]["name"])) {

            $user_id = $_SESSION['user_id']; // Get the user's ID
            $userDir = "uploads/profiles/" . $user_id . "/"; // Define user-specific directory

            // Check if the user's directory exists, if not, create it
            if (!file_exists($userDir)) {
                mkdir($userDir, 0777, true); // Create user's main folder
            }

            $post_id = $pdo->lastInsertId(); // Get new post ID
            $postDir = $userDir . "posts/" . $post_id . "/"; // Define post-specific folder
        
            // Ensure the post directory is created **only if an image is being uploaded**
            if (!file_exists($postDir)) {
                mkdir($postDir, 0777, true); // Create post folder
            }
        
            // Move the image into the post folder
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $postDir . $fileName;
        
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                $image_url = $targetFilePath;
        
                // Update the post record with the image URL
                $stmt = $pdo->prepare("UPDATE posts SET post_picture = :post_picture WHERE post_id = :post_id");
                $stmt->bindParam(':post_picture', $image_url, PDO::PARAM_STR);
                $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
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
            <form id="profile-form" class="w-75 mx-auto" action="profile.php" method="POST" enctype="multipart/form-data">
                <div id="profile-details-container" class="d-flex m-auto mt-5">
                    <!-- Profile Picture -->
                    <div>
                        <input id="profile-image-upload" type="file" name="profile_picture" accept="image/*" disabled hidden>
                        <label for="profile-image-upload">
                            <img id="profile-picture" src="<?php echo htmlentities($profile_picture); ?>" alt="Profile Picture">
                        </label>
                    </div>
                    <!-- Profile information -->
                    <div id="profile-details">
                        <!-- Full Name -->
                        <h3><input id="profile-name" class="mb-2" type="text" name="full_name" value="<?php echo !empty($full_name) ? htmlentities($full_name) : 'Enter your name'; ?>" required disabled></h3>
                        <!-- Location -->
                        <h5><input id="profile-location" class="mb-2" type="text" name="location" value="<?php 
                            if ($user_id == $_SESSION['user_id']) {
                                echo !empty($location) ? htmlentities($location) : 'Add a location';
                            } else {
                                echo !empty($location) ? htmlentities($location) : 'Location not set';
                            }
                        ?>" disabled></h5>
                        <!-- Bio -->
                        <textarea id="profile-bio" name="bio" disabled><?php
                            if ($user_id == $_SESSION['user_id']) {
                                echo !empty($bio) ? htmlentities($bio) : "Add a bio";
                            } else {
                                echo !empty($bio) ? htmlentities($bio) : "Bio not set";
                            }
                        ?></textarea>  
                    </div>
                    <!-- Edit icon -->
                    <?php if ($user_id == $_SESSION['user_id']): ?> 
                        <div>
                            <span id="edit-icon">
                                <i class="bi bi-pencil"></i>
                            </span>
                            <span id="cancel-icon">
                                <i class="bi bi-x-lg"></i>
                            </span>
                            <button id="profile-details-submit" type="submit" name="profile-details" style="display: none;"></button>
                        </div>
                    <?php endif; ?>
                </div>
            </form><hr class="mt-5">
        </div>

        <!-- Section posts -->
        <div class="w-50 mx-auto">
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

    <script>
        document.getElementById("profile-image-upload").addEventListener("change", function(event) {
        const file = event.target.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById("profile-picture").src = e.target.result; // Update image preview
            };
            reader.readAsDataURL(file);
        }
    });

</script>
</body>
</html>