<!-- Profile details container -->
<div id="profile-details" class="w-75 m-auto">
    <!-- Profile picture -->
    <div id="profile-picture-container">
        <img id="profile-picture" class="rounded-pill" src="<?php echo htmlentities($profile_picture); ?>" alt="Profile Picture">
    </div>
    <!-- Profile details -->
    <div id="profile-details-container">
        <!-- Full Name -->
        <div class="d-flex align-items-center justify-content-between">
            <h1 id="profile-fullname" class="display-5 mt-3 mb-0"><?php echo !empty($full_name) ? htmlentities($full_name) : 'Enter your name'; ?></h1>
            <!-- Edit icon -->
            <?php if ($user_id == $_SESSION['user_id']): ?> 
                <span id="edit-icon">
                    <i class="bi bi-pencil" style="color:grey;"></i>
                </span>
            <?php endif; ?>   
        </div><hr>

        <div class="row">
            <!-- Location -->
            <div class="col-12 col-lg-7"> 
                <p id="profile-location" ><strong>Location: </strong><?php echo !empty($location) ? htmlentities($location) : 'N/A'; ?></p>
            </div>
            
            <!-- Joined Date -->
            <div class="col-12 col-lg-5">  
                <p id="profile-joined-date"><strong>Joined On: </strong><?php echo !empty($joined) ? htmlentities($joined) : 'N/A'; ?></p>
            </div>

            <!-- Occupation -->
            <div class="col-12 col-lg-7">
                <p id="profile-occupation"><strong>Occupation: </strong><?php echo !empty($occupation) ? htmlentities($occupation) : 'N/A'; ?></p>
            </div>
            
            <!-- Age -->
            <div class="col-12 col-lg-5">
                <p id="profile-age"><strong>Age: </strong><?php echo !empty($age) ? htmlentities($age) : 'N/A'; ?></p>
            </div>

            <!-- Bio -->
            <div class="col-12">
                <p id="profile-bio" ><strong>Bio: </strong><?php echo !empty($bio) ? htmlentities($bio) : "N/A"; ?></p>
            </div>

        </div>
    </div>   
</div>
    
<!-- Update profile container -->
<div id="update-profile" class="container" style="display:none;">
    <h1 class="display-5 text-center mb-5">Update Profile</h1>
    <form id="profile-form" class="w-50 mx-auto" action="profile.php" method="POST" enctype="multipart/form-data" style="background-color: #F8F9FA;">
            <div class="row">
                <!-- Profile Picture -->
                <div class="col-12 mb-4">
                    <input id="profile-picture-input" type="file" name="profile_picture" accept="image/*" disabled hidden>
                    <label id="profile-picture-label" for="profile-image-upload" class="mb-2">
                        <div class="d-flex flex-column justify-content-center w-50 h-100 mb-2">
                            <img id="profile-picture-img" class="mb-2 rounded-pill" src="<?php echo htmlentities($profile_picture); ?>" alt="Profile Picture">
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
                    <textarea id="bio-textarea" class="form-control" name="bio" maxlength="250" rows="2" style="resize:none;" disabled required><?php echo !empty($bio) ? htmlentities($bio) : "Bio Missing"; ?></textarea>
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