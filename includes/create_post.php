<?php 
if ($user_id == $_SESSION['user_id']): ?>
    <div id="profile-new-post" class="mt-5">
        <form id="new-post-form" class="d-flex flex-column justify-content-center w-75 m-auto" method="POST" enctype="multipart/form-data">
            <img id="new-post-img" class="mb-2" src="">
            <div class="d-flex flex-column">
                <div class="mb-2">
                    <input type="file" name="image" id="image-upload" accept="image/*" hidden>
                    <button type="button" onclick="document.getElementById('image-upload').click()" style="border:none;">
                    <i class="bi bi-card-image"></i>
                    </button>
                </div>
                
                <textarea id="new-post-textarea" class="mb-2 new-post-textarea" name="post_content" placeholder="Create a new post" rows="3" required></textarea>
                <div id="new-post-btn-group" class="ms-auto">
                    <button id="cancel-post-btn" class="btn btn-sm btn-secondary ms-1" type="button" name="cancel-post">Cancel</button>
                    <button id="new-post-btn" class="btn btn-sm btn-primary ms-1" type="submit" name="new-post">Post</button>
                </div>
            </div>
        </form>
        <hr class="mt-5">
    </div>
<?php endif; ?>  