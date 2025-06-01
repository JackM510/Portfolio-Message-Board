<form id="profile-form" class="mx-auto" action="profile.php" method="POST" enctype="multipart/form-data">
    <div id="profile-container" class="d-flex m-auto mt-5">
        <!-- Profile Picture -->
        <div id="profile-picture-container">
            <input id="profile-image-upload" type="file" name="profile_picture" accept="image/*" disabled hidden>
            <label id="profile-picture-label" for="profile-image-upload">
                <img id="profile-picture"  src="<?php echo htmlentities($profile_picture); ?>" alt="Profile Picture">
            </label>
        </div>
        <!-- Profile information -->
        <div id="profile-details-container">
            <!-- Full Name -->
            <h3><input id="profile-name" class="mb-2 dynamic-input" type="text" name="full_name" value="<?php echo !empty($full_name) ? htmlentities($full_name) : 'Enter your name'; ?>" required disabled></h3>
            <!-- Location -->
            <h5><input id="profile-location" class="mb-2 dynamic-input" type="text" name="location" value="<?php 
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
                <button id="profile-details-submit" type="submit" name="update_profile" style="display: none;"></button>
            </div>
        <?php endif; ?>
    </div>
</form><hr class="mt-5">