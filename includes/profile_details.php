<!-- Profile details container -->
<div id="profile-details" class="w-50 m-auto mt-5">
    <!-- Profile picture -->
    <div id="profile-picture-container">
        <img id="profile-picture"  src="<?php echo htmlentities($profile_picture); ?>" alt="Profile Picture">
    </div>
    <!-- Profile details -->
    <div id="profile-details-container">
        <!-- Full Name -->
        <h1 id="profile-fullname" class="display-5"><?php echo !empty($full_name) ? htmlentities($full_name) : 'Enter your name'; ?></h1>
        <!-- Age -->
        <p id="profile-age" ><strong>Age: </strong><?php echo !empty($age) ? htmlentities($age) : 'Enter your age'; ?></p>
        <!-- Location -->
        <p id="profile-location" ><strong>Location: </strong><?php 
            if ($user_id == $_SESSION['user_id']) {
                echo !empty($location) ? htmlentities($location) : 'Add a location';
            } else {
                echo !empty($location) ? htmlentities($location) : 'Location not set';
            }
        ?></p>
        <!-- Bio -->
        <p id="profile-bio" ><strong>Bio: </strong><?php
            if ($user_id == $_SESSION['user_id']) {
                echo !empty($bio) ? htmlentities($bio) : "Add a bio";
            } else {
                echo !empty($bio) ? htmlentities($bio) : "Bio not set";
            }
        ?></p>  
    </div>

    <!-- Edit icon -->
    <?php if ($user_id == $_SESSION['user_id']): ?> 
        <div>
            <span id="edit-icon">
                <i class="bi bi-pencil" style="color:grey;"></i>
            </span>
        </div>
    <?php endif; ?>
</div>
    
<!-- Update profile container -->
<div id="update-profile" class="container" style="display:none;">
    <h1 class="display-5 text-center mb-4">Update Profile</h1>
    <form id="profile-form" class="w-50 mx-auto" action="profile.php" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Profile Picture -->
                <div class="col-12 mb-3">
                    <input id="profile-picture-input" type="file" name="profile_picture" accept="image/*" disabled hidden>
                    <label id="profile-picture-label" for="profile-image-upload" class="mb-2">
                        <div class="d-flex flex-column justify-content-center">
                            <img id="profile-picture-img" class="mb-2" src="<?php echo htmlentities($profile_picture); ?>" alt="Profile Picture">
                            <button id="profile-picture-btn" type="button" class="btn btn-sm btn-light" title="Upload Profile Picture">
                                <i class="bi bi-card-image" style="font-size: 16px;"></i>
                            </button>
                        </div>
                    </label>
                    
                </div>
                <!-- First Name -->
                <div class="col-12 mb-3">
                    <label class="pb-1" for="first_name"><strong>First Name</strong></label>
                    <input id="first-name-input" class="form-control" type="text" name="first_name" value="<?php echo !empty($first_name) ? htmlentities($first_name) : "First Name Missing"; ?>" disabled required>
                </div>
                <!-- Last Name -->
                <div class="col-12 mb-3">
                    <label class="pb-1" for="last_name"><strong>Last Name</strong></label>
                    <input id="last-name-input" class="form-control" type="text" name="last_name" value="<?php echo !empty($last_name) ? htmlentities($last_name) : "Last Name Missing"; ?>" disabled required>
                </div>
                <!-- Location -->
                <div class="col-12 mb-3">
                    <label class="pb-1" for="location"><strong>Location</strong></label>
                    <input id="location-input" class="form-control" type="location" name="location" value="<?php echo !empty($location) ? htmlentities($location) : "Location Missing"; ?>" disabled required>
                </div>
                <!-- Age -->
                <div class="col-12 mb-3">
                    <label class="pb-1" for="age"><strong>Age</strong></label>
                    <input id="age-input" class="form-control" type="number" name="age" value="<?php echo !empty($age) ? htmlentities($age) : "Age Missing"; ?>" disabled required>
                </div>
                <!-- Bio -->
                <div class="col-12 mb-4">
                    <label class="pb-1" for="bio"><strong>Bio</strong></label>
                    <textarea id="bio-textarea" class="form-control" name="bio" row="2" style="resize:none;" disabled required><?php echo !empty($bio) ? htmlentities($bio) : "Bio Missing"; ?></textarea>
                </div>
                <!-- Form Buttons -->
                <div class="col-12 d-flex justify-content-center">
                    <button id="profile-cancel-btn" class="btn btn-sm btn-secondary mx-1" type="button" name="Cancel">Cancel</button>
                    <button id="profile-update-btn" class="btn btn-sm btn-primary mx-1" type="submit" name="Update">Update</button>
                </div>
            </div>
        </form>
</div>
<hr class="mt-5">