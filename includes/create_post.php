<?php if (isLoggedIn()): ?>
    <div id="new-post-container">
        <form id="new-post-form" class="d-flex flex-column justify-content-center m-auto p-4" method="POST" action="<?= ACTION_ADD_POST ?>" enctype="multipart/form-data">
            <div class="d-flex flex-column">
                <!-- Img & Img btn -->
                <img id="new-post-img" class="mb-2" src="">
                <div class="mb-2">
                    <input id="image-upload" type="file" name="image" accept="image/*" hidden>
                    <button id="image-upload-btn" class="btn btn-sm btn-light" type="button">
                        <i class="bi bi-card-image"></i>
                    </button>
                </div>
                <!-- TA and btns -->
                <textarea id="new-post-textarea" class="form-control new-post-textarea responsive-textarea rounded mb-2" name="post_content" placeholder="Create a new post..." rows="1" maxlength="255" required></textarea>
                <div id="new-post-btn-group" class="ms-auto">
                    <button id="cancel-post-btn" class="btn btn-sm btn-secondary ms-1" type="button" name="cancel-post">Cancel</button>
                    <button id="new-post-btn" class="btn btn-sm btn-primary ms-1" type="submit" name="new-post">Post</button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>  