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

        /*if (!empty($_FILES["image"]["name"])) {
            $targetDir = "uploads/posts/";
            $fileName = basename($_FILES["image"]["name"]);
            $targetFilePath = $targetDir . $fileName;

            if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetFilePath)) {
                echo "File uploaded to: " . $targetFilePath; // Debugging message
                $image_url = $targetFilePath; // Store the image path
            }
        }*/

        // Prepare SQL query with optional image URL
        /*$stmt = $pdo->prepare("INSERT INTO posts (user_id, post_text, post_picture) VALUES (:uid, :post_text, :post_picture)");
        $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
        $stmt->bindParam(':post_text', $post_content, PDO::PARAM_STR);
        $stmt->bindParam(':post_picture', $image_url, PDO::PARAM_STR | PDO::PARAM_NULL); // Can be NULL
        $stmt->execute();*/

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
             <form class="d-flex flex-column justify-content-center w-75 m-auto" action="profile.php" method="POST" enctype="multipart/form-data">
                <img id="new-post-img" class="mb-2" src="">
                <textarea id="new-post-textarea" class="mb-2" name="post_content" placeholder="Create a new post" rows="3" required></textarea>
                    <div class="d-flex justify-content-between align-items-center">
                    <input type="file" name="image" id="image-upload" accept="image/*" hidden>
                    <button type="button" onclick="document.getElementById('image-upload').click()">
                    <i class="bi bi-card-image"></i>
                    </button>

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

    <script>
        document.getElementById("image-upload").addEventListener("change", function(event) {
            const file = event.target.files[0]; // Get the selected file

            if (file) {
                const reader = new FileReader(); // Create a FileReader object
                reader.onload = function(e) {
                    document.getElementById("new-post-img").src = e.target.result; // Set the image source to the selected file
                };
                reader.readAsDataURL(file); // Convert the file into a Data URL
            }
        });
</script>
</body>
</html>