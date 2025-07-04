<?php 
if ($user_id == $_SESSION['user_id']): ?>
    <div id="new-post-container" class="mt-5">
        <form id="new-post-form" class="d-flex flex-column justify-content-center m-auto" method="POST" enctype="multipart/form-data">
            <div class="d-flex flex-column">
                <img id="new-post-img" class="mb-2" src="">
                <div class="mb-2">
                    <input type="file" name="image" id="image-upload" accept="image/*" hidden>
                    <button id="image-upload-btn" type="button" class="btn btn-sm btn-light" onclick="document.getElementById('image-upload').click()" style="border:none;">
                    <i class="bi bi-card-image" style="font-size: 18px;"></i>
                    </button>
                </div>
                
                <textarea id="new-post-textarea" class="form-control mb-2 rounded new-post-textarea" name="post_content" placeholder="Create a new post" rows="2" maxlength="150"required></textarea>
                <div id="new-post-btn-group" class="ms-auto">
                    <button id="cancel-post-btn" class="btn btn-sm btn-secondary ms-1" type="button" name="cancel-post">Cancel</button>
                    <button id="new-post-btn" class="btn btn-sm btn-primary ms-1" type="submit" name="new-post">Post</button>
                </div>
            </div>
        </form>
        <hr class="mt-5">
    </div>
<?php endif; ?>  