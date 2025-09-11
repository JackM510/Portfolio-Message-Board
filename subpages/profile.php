<?php
    require_once __DIR__ . '/../config.php';
    session_start();
    require_once(DB_INC);
    require_once(UTIL_INC);
    // Must be logged in to view a profile
    if (isLoggedIn() === false) {
        header("Location: " . LOGIN_URL);
    }
    require(ACTION_GET_PROFILE);
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
            jsFadeEl: "<?= JS_PAGE_TRANSITIONS ?>",
            jsPredictLines: "<?= JS_TEXTAREA ?>",
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

                <!-- Name + edit icon -->
                <div id="profile-details-container">
                    <div class="d-flex align-items-center justify-content-between mt-3">
                         <!-- Full Name -->
                        <h1 id="profile-fullname" class="display-5 mb-0"><?php echo !empty($full_name) ? htmlentities($full_name) : 'N/A'; ?></h1>
                        <!-- Edit icon -->
                        <?php if ($profile_id == $_SESSION['profile_id']): ?> 
                            <span id="edit-icon" class="align-self-start">
                                <i class="bi bi-pencil"></i>
                            </span>
                        <?php endif; ?>   
                    </div><hr>

                    <div class="row">
                        <!-- Admin specifc details -->
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
                            <p id="profile-bio"><strong>Bio: </strong><?php echo !empty($bio) ? htmlentities($bio) : "N/A"; ?></p>
                        </div>
                    </div>
                </div>   
            </div>
                
            <!-- Update profile container -->
            <div id="update-profile" class="container">
                <h1 class="display-5 text-center mb-5">Update Profile</h1>
                <form id="profile-form" class="mx-auto" method="POST" action="<?= ACTION_EDIT_PROFILE ?>" enctype="multipart/form-data">
                    <div class="row">
                        <!-- Profile Picture -->
                        <div class="col-12 mb-4">
                            <input id="profile-picture-input" type="file" name="profile_picture" accept="image/*" disabled hidden>
                            <label id="profile-picture-label" class="mb-2" for="profile-image-upload">
                                <div class="d-flex flex-column justify-content-center w-25 h-100 mb-2">
                                    <img id="profile-picture-img" class="mb-2" src="<?= APP_BASE_PATH . "/" . htmlentities($profile_picture); ?>" alt="Profile Picture">
                                    <button id="profile-picture-btn" class="btn btn-sm btn-light mx-auto" type="button" title="Upload Profile Picture">
                                        <i class="bi bi-card-image"></i>
                                    </button>
                                </div>
                            </label>
                        </div>
                        <!-- First Name -->
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="pb-1" for="first_name"><strong>First Name</strong></label>
                            <input id="first-name-input" class="form-control" type="text" name="first_name" value="<?php echo !empty($first_name) ? htmlentities($first_name) : "N/A"; ?>" maxlength="25" disabled required>
                        </div>
                        <!-- Last Name -->
                        <div class="col-12 col-lg-6 mb-3">
                            <label class="pb-1" for="last_name"><strong>Last Name</strong></label>
                            <input id="last-name-input" class="form-control" type="text" name="last_name" value="<?php echo !empty($last_name) ? htmlentities($last_name) : "N/A"; ?>" maxlength="25" disabled required>
                        </div>
                        <!-- Location -->
                        <div class="col-12 mb-3">
                            <label class="pb-1" for="location"><strong>Location</strong></label>
                            <input id="location-input" class="form-control" type="location" name="location" value="<?php echo !empty($location) ? htmlentities($location) : "N/A"; ?>" maxlength="50" disabled required>
                        </div>
                        <!-- Occupation -->
                        <div class="col-12 mb-3">
                            <label class="pb-1" for="occupation"><strong>Occupation</strong></label>
                            <input id="occupation-input" class="form-control" type="text" name="occupation" value="<?php echo !empty($occupation) ? htmlentities($occupation) : "N/A"; ?>" maxlength="50" disabled required>
                        </div>
                        <!-- Bio -->
                        <div class="col-12 mb-4">
                            <label class="pb-1" for="bio"><strong>Bio</strong></label>
                            <textarea id="bio-textarea" class="form-control" name="bio" rows="1" maxlength="255" disabled required><?php echo !empty($bio) ? htmlentities($bio) : "N/A"; ?></textarea>
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
        
        <!-- Posts -->
        <div id="profile-posts-container" class="container mx-auto">
            <!-- Create post -->
            <?php if (isLoggedIn()): ?>
                <div id="profile-new-post">
                    <?php 
                        include (CREATE_POST_INC);
                    ?>    
                </div>
            <?php endif; ?>
            <!-- Fetch all posts from the user -->
            <div id="profile-users-posts" class="d-flex flex-column justify-content-center">
                <?php 
                    include (FETCH_POSTS_INC);
                    getPosts($pdo, $profile_id); //Fetch ALL posts 
                ?>
            </div>
        </div> 
    </div>
</body>
</html>