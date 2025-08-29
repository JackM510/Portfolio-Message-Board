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
    <div>
        <!-- Profile section -->
        <div id="profile-container" class="container-fluid pt-5">
            <!-- Profile details -->
            <div id="profile-details" class="m-auto fade-in">
                <!-- Profile picture -->
                <div id="profile-picture-container">
                    <img id="profile-picture" class="rounded-pill" src="<?= APP_BASE_PATH . "/" . htmlentities($profile_picture); ?>" alt="Profile Picture">
                </div>
    
                <div id="profile-details-container">
                    <!-- Full Name -->
                    <div class="d-flex align-items-center justify-content-between mt-3">
                        <h1 id="profile-fullname" class="display-5 mb-0"><?php echo !empty($full_name) ? htmlentities($full_name) : 'Enter your name'; ?></h1>
                        <!-- Edit icon -->
                        <?php if ($profile_id == $_SESSION['profile_id']): ?> 
                            <span id="edit-icon" class="align-self-start">
                                <i class="bi bi-pencil" style="color:grey;"></i>
                            </span>
                        <?php endif; ?>   
                    </div><hr>

                    <div class="row">
                        <?php if ($isAdmin): ?>
                            <!-- Email -->
                            <div class="col-12 order-0 col-lg-6 order-lg-0"> 
                                <p id="profile-email" ><strong>Email: </strong><?php echo !empty($email) ? htmlentities($email) : 'N/A'; ?></p>
                            </div> 
                            <!-- Profile -->
                            <div class="col-12 order-1 offset-lg-1 col-lg-5 order-lg-1">  
                                <p id="profile-id"><strong>Profile ID: </strong><?php echo !empty($profile_id) ? htmlentities($profileId) : 'N/A'; ?></p>
                            </div>
                        <?php endif; ?>

                        <!-- Location -->
                        <div class="col-12 order-4 col-lg-6 order-lg-2"> 
                            <p id="profile-location" ><strong>Location: </strong><?php echo !empty($location) ? htmlentities($location) : 'N/A'; ?></p>
                        </div>            
                        <!-- Joined Date -->
                        <div class="col-12 order-2 offset-lg-1 col-lg-5 order-lg-3">  
                            <p id="profile-joined-date"><strong>Joined On: </strong><?php echo !empty($joined) ? htmlentities($joined) : 'N/A'; ?></p>
                        </div>
                        <!-- Occupation -->
                        <div class="col-12 order-5 col-lg-6 order-lg-4">
                            <p id="profile-occupation"><strong>Occupation: </strong><?php echo !empty($occupation) ? htmlentities($occupation) : 'N/A'; ?></p>
                        </div>            
                        <!-- Age -->
                        <div class="col-12 order-3 offset-lg-1 col-lg-5 order-lg-5">
                            <p id="profile-age"><strong>Age: </strong><?php echo !empty($age) ? htmlentities($age) : 'N/A'; ?></p>
                        </div>
                        <!-- Bio -->
                        <div class="col-12 order-last col-lg-9 order-lg-last">
                            <p id="profile-bio" ><strong>Bio: </strong><?php echo !empty($bio) ? htmlentities($bio) : "N/A"; ?></p>
                        </div>
                    </div>
                </div>   
            </div>
                
            <!-- Update profile container -->
            <div id="update-profile" class="container" style="display:none; ">
                <h1 class="display-5 text-center mb-5">Update Profile</h1>
                <form id="profile-form" class="mx-auto" action="<?= ACTION_EDIT_PROFILE ?>" method="POST" enctype="multipart/form-data" style="background-color: #FAFAFA;">
                    <div class="row">
                        <!-- Profile Picture -->
                        <div class="col-12 mb-4">
                            <input id="profile-picture-input" type="file" name="profile_picture" accept="image/*" disabled hidden>
                            <label id="profile-picture-label" for="profile-image-upload" class="mb-2">
                                <div class="d-flex flex-column justify-content-center w-25 h-100 mb-2">
                                    <img id="profile-picture-img" class="mb-2" src="<?= APP_BASE_PATH . "/" . htmlentities($profile_picture); ?>" alt="Profile Picture">
                                    <button id="profile-picture-btn" type="button" class="btn btn-sm btn-light mx-auto" title="Upload Profile Picture">
                                        <i class="bi bi-card-image" style="font-size: 18px;"></i>
                                    </button>
                                </div>
                            </label>
                            
                        </div>
                        <!-- First Name -->
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="pb-1" for="first_name"><strong>First Name</strong></label>
                            <input id="first-name-input" class="form-control" type="text" name="first_name" maxlength="20" value="<?php echo !empty($first_name) ? htmlentities($first_name) : "First Name Missing"; ?>" disabled required>
                        </div>
                        <!-- Last Name -->
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="pb-1" for="last_name"><strong>Last Name</strong></label>
                            <input id="last-name-input" class="form-control" type="text" name="last_name" maxlength="20" value="<?php echo !empty($last_name) ? htmlentities($last_name) : "Last Name Missing"; ?>" disabled required>
                        </div>
                        <!-- Location -->
                        <div class="col-12 mb-3">
                            <label class="pb-1" for="location"><strong>Location</strong></label>
                            <input id="location-input" class="form-control" type="location" name="location" maxlength="50" value="<?php echo !empty($location) ? htmlentities($location) : "Location Missing"; ?>" disabled required>
                        </div>
                        <!-- Occupation -->
                        <div class="col-12 mb-3">
                            <label class="pb-1" for="occupation"><strong>Occupation</strong></label>
                            <input id="occupation-input" class="form-control" type="text" name="occupation" maxlength="50" value="<?php echo !empty($occupation) ? htmlentities($occupation) : "Occupation Missing"; ?>" disabled required>
                        </div>
                        <!-- Bio -->
                        <div class="col-12 mb-4">
                            <label class="pb-1" for="bio"><strong>Bio</strong></label>
                            <textarea id="bio-textarea" class="form-control responsive-textarea" name="bio" maxlength="250" rows="1" disabled required><?php echo !empty($bio) ? htmlentities($bio) : "Bio Missing"; ?></textarea>
                        </div>
                        <!-- Form Buttons -->
                        <div class="col-12 d-flex justify-content-center">
                            <button id="profile-cancel-btn" class="btn btn-sm btn-secondary mx-1" type="button" name="Cancel">Cancel</button>
                            <button id="profile-update-btn" class="btn btn-sm btn-primary mx-1" type="submit" name="Update">Update</button>
                        </div>
                    </div>
                </form>
            </div>
            <hr class="mt-3">
        </div>
        
        <!-- Posts section -->
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
    </div>
</body>
</html>