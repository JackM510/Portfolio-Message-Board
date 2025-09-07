<?php 
// Render comments section
function renderComment(PDO $pdo, array $comment, int $postId): string {
    // Comment data
    $commentorName = htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']);
    $commentorPic = htmlspecialchars($comment['profile_picture']);
    $commentId = htmlspecialchars($comment['comment_id']);
    $commentText = htmlspecialchars($comment['comment_text']);
    $postIdEscaped = htmlspecialchars($postId);
    $userId = $comment['user_id'];
    $profileId = $comment['profile_id'];
    $timestamp = date('g:iA j/n/y', strtotime($comment['comment_created'])); // Timestamp
    $commentTimestamp = !empty($comment['comment_edited']) ? "$timestamp (edited)" : $timestamp; // Check if comment edited
    
    // Get comment like count
    $likeStmt = $pdo->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?");
    $likeStmt->execute([$commentId]);
    $likeCount = $likeStmt->fetchColumn();
    // Check if user liked the comment
    $liked = false;
    if (isLoggedIn()) {
        $likedStmt = $pdo->prepare("SELECT 1 FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $likedStmt->execute([$commentId, $_SESSION['user_id']]);
        $liked = $likedStmt->fetchColumn() ? true : false;
    }

    ob_start(); ?>
    <!-- Comment start -->
    <div class="comment comment-<?= $postId ?>">
        <hr>
        <div class="d-flex align-items-start">
            <!-- Commentor profile pic & full name -->
            <a class="post-profile-link d-flex" href="<?= PROFILE_URL . '?profile_id=' . urlencode($profileId) ?>">
                <img class="me-2 rounded-pill comment-profile-picture" 
                    src="<?= APP_BASE_PATH . "/" . $commentorPic ?>" alt="Profile Picture">
                <p class="break-text mb-2"><strong><?= $commentorName ?></strong></p>
            </a>
            
            <!-- Comment dropdown options -->
            <?php if (isLoggedIn() && $_SESSION['user_id'] == $userId): ?>
                <div id="comment-dropdown-<?= $commentId ?>" class="dropdown comment-dropdown ms-auto">
                    <!-- Comment three-dot -->
                    <span id="comment-options-<?= $commentId ?>" class="comment-options" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        <i class="bi bi-three-dots-vertical"></i>
                    </span>
                    <!-- Dropdown options -->
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="post-options-<?= $commentId ?>">
                        <li><button class="dropdown-item edit-btn edit-comment-dropdown-item" type="button" data-post-id="<?= $commentId ?>">Edit</button></li>
                        <li>
                            <form id="comment-options-form-<?= $commentId ?>" data-post-id="<?= $postIdEscaped ?>" method="POST">
                                <input type="hidden" name="delete_comment" value="true">
                                <input type="hidden" name="comment_id" value="<?= $commentId ?>">
                                <button class="dropdown-item edit-comment-dropdown-item text-danger" type="submit">Delete</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <!-- Comment data -->
        <div class="mt-2">
            <form id="edit-comment-form-<?= $commentId ?>" class="w-100" data-post-id="<?= $postIdEscaped ?>" method="POST" action="<?= ACTION_EDIT_COMMENT ?>">
                <!-- Comment text -->
                <p id="comment-description-<?= $commentId ?>" class="break-text mb-2"><?= $commentText ?></p>
                <textarea id="comment-textarea-<?= $commentId ?>" class="form-control comment-textarea rounded mb-1 responsive-textarea" name="edit_comment" data-post-id="<?= $commentId ?>" rows="1"  maxlength="255" hidden required disabled><?= $commentText ?></textarea>
                <input type="hidden" name="comment_id" value="<?= $commentId ?>">
                <!-- Comment timestamp & cancel/submit btns -->
                <div class="d-flex">
                    <p class="comment-timestamp mb-0"><?= $commentTimestamp ?></p>
                    <div id="edit-comment-btns-<?= $commentId ?>" class="edit-comment-btns ms-auto mt-1">
                        <button class="btn btn-sm btn-secondary edit-cancel-btn" type="button" data-post-id="<?= $commentId ?>">Cancel</button>
                        <button class="btn btn-sm btn-primary ms-1" type="submit">Comment</button>
                    </div>
                </div>
                <!-- Comment like btn -->
                <div class="comment-like-container mb-2" data-post-id="<?= $commentId ?>">
                    <button id="comment-like-btn-<?= $commentId ?>" class="comment-like-btn btn btn-outline-primary btn-sm" type="button">
                        <i class="bi bi-hand-thumbs-up<?= $liked ? '-fill' : '' ?>"></i>
                        <span class="comment-like-count"><?= htmlspecialchars($likeCount) ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div> <!-- Comment end -->
    <?php
    return ob_get_clean();
}

// Render add comment section
function renderAddComment(PDO $pdo, int $postId): string {
    $profilePic = $_SESSION['avatar']; 
    $postIdEscaped = htmlspecialchars($postId);
    ob_start(); // Start output buffering
    ?>

    <!-- Add comment section -->
    <hr>
    <div class="add-comment d-flex pt-3">
        <div>
            <img class="me-3 rounded-pill comment-profile-picture" src="<?= APP_BASE_PATH . "/" . $profilePic ?>" alt="Post Image">
        </div>
        <form id="add-comment-form-<?= $postIdEscaped ?>" data-post-id="<?= $postIdEscaped ?>" method="POST" class="w-100">
            <textarea id="add-comment-textarea-<?= $postIdEscaped ?>" class="form-control w-100 add-comment-textarea rounded responsive-textarea mb-2" name="comment_text" placeholder="Add a comment..." rows="1" maxlength="255"></textarea>
            <input type="hidden" name="post_id" value="<?= $postIdEscaped ?>">
            <div id="add-comment-btns-<?= $postIdEscaped ?>" class="add-comment-btns">
                <button class="btn btn-sm btn-secondary cancel-btn" type="button" data-post-id="<?= $postIdEscaped ?>">Cancel</button>
                <button class="btn btn-sm btn-primary ms-1">Comment</button>
            </div>
        </form>
    </div>
    <?php
    return ob_get_clean(); // Return rendered HTML
}

// Render each post
function renderPost(PDO $pdo, array $post): string {
    $postId = $post['post_id'];
    $userId = $post['user_id'];

    // Get post author info
    $stmt = $pdo->prepare("
        SELECT u.first_name, u.last_name, p.profile_id, p.profile_picture 
        FROM users u 
        JOIN profiles p ON u.user_id = p.user_id 
        WHERE u.user_id = :user_id
    ");

    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    $profileId = $userData['profile_id'];

    $userName = htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']);
    $profilePic = htmlspecialchars($userData['profile_picture']);

    // Timestamp
    $timestamp = date('g:iA j/n/y', strtotime($post['post_created']));
    $postTimestamp = !empty($post['post_edited']) ? "$timestamp (edited)" : $timestamp;
    // Get post like count
    $postLikes = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ?");
    $postLikes->execute([$postId]);
    $likeCount = $postLikes->fetchColumn();
    // Get post comment count
    $commentCount = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $commentCount->execute([$postId]);
    $commentCount = $commentCount->fetchColumn();

    // Check if user liked or commented on the post
    $liked = false;
    $commented = false;
    if (isLoggedIn()) {

        $checkLike = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?");
        $checkLike->execute([$postId, $_SESSION['user_id']]);
        $liked = $checkLike->fetchColumn() ? true : false;

        $checkComment = $pdo->prepare("SELECT 1 FROM comments WHERE post_id = ? AND user_id = ?");
        $checkComment->execute([$postId, $_SESSION['user_id']]);
        $commented = $checkComment->fetchColumn() ? true : false;
    }

    ob_start(); ?>
    <!-- Post start -->
    <div class="post-container">
        <div class="d-flex align-items-start pt-1">
            <!-- Postee profile pic & full name -->
            <a class="post-profile-link d-flex" href="<?= PROFILE_URL . '?profile_id=' . urlencode($profileId) ?>">
                <img class="me-2 rounded-pill post-profile-picture"
                    src="<?= APP_BASE_PATH . "/" . $profilePic ?>" alt="Post Image">
                <h5 class="break-text mb-0"><?= $userName ?></h5>
            </a>
            
            <!-- Edit post dropdown -->
            <?php if (isLoggedIn() && $_SESSION['user_id'] == $userId): ?>
            <div id="post-dropdown-<?= $postId ?>" class="dropdown ms-auto h-100 d-flex align-items-start">
                <span id="post-options-<?= $postId ?>" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
                    <i class="bi bi-three-dots-vertical" style="color:grey; font-size:20px;"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="post-options-<?= $postId ?>">
                    <li><button class="dropdown-item edit-post-btn edit-post-dropdown-item" type="button" data-post-id="<?= $postId ?>">Edit</button></li>
                    <li>
                        <form id="post-options-form-<?= $postId ?>" data-post-id="<?= $postId ?>" method="POST">
                            <input type="hidden" name="delete_post" value="true">
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                            <button type="submit" class="dropdown-item edit-post-dropdown-item text-danger">Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <!-- Post data -->
        <form id="edit-post-form-<?= $postId ?>" method="POST">
            <!-- Post img -->
            <div>
                <img id="post-picture-<?= $postId ?>"
                    class="post-picture mt-3"
                    src="<?= !empty($post['post_picture']) ? APP_BASE_PATH . "/" . htmlspecialchars($post['post_picture']) : '' ?>"
                    alt="Post Image"
                    style="<?= empty($post['post_picture']) ? 'display:none;' : '' ?>">
            </div>
            
            <!-- Post img upload -->
            <div>
                <input type="file" name="post-image-upload" id="post-image-upload-<?= $postId ?>" class="post-image-upload" accept="image/*" hidden>
                <button type="button" class="btn btn-sml btn-light mt-3" id="post-image-upload-btn-<?= $postId ?>" onclick="document.getElementById('post-image-upload-<?= $postId ?>').click()" style="border:none; display:none;">
                    <i class="bi bi-card-image" style="font-size: 16px !important;"></i>
                </button>
            </div>

            <div class="mt-3">
                <!-- Post text -->      
                <p id="post-description-<?= $postId ?>" class="break-text mb-2"><?= htmlspecialchars($post['post_text']) ?></p>
                <textarea id="post-textarea-<?= $postId ?>" class="form-control post-textarea rounded mb-1 responsive-textarea" name="post_textarea" rows="1" maxlength="255" hidden required disabled><?= htmlspecialchars($post['post_text']) ?></textarea>

                <!-- Post edit btns -->
                <div class="d-flex">
                    <p class="mb-1" style="color:grey;"><?= $postTimestamp ?></p>
                    <div id="edit-post-btn-group-<?= $postId ?>" class="ms-auto edit-post-btn-group mt-1">
                        <input type="hidden" name="post_id" value="<?= $postId ?>">
                        <button class="btn btn-sm btn-secondary ms-1 edit-post-cancel-btn" type="button" data-post-id="<?= $postId ?>" name="edit-cancel-post">Cancel</button>
                        <button id="edit-post-submit-btn" class="btn btn-sm btn-primary ms-1" type="submit">Post</button>
                    </div>
                </div>

                <!-- Post like & comment btns -->
                <div class="post-like-container d-flex" data-post-id="<?= $postId ?>">
                    <button id="post-like-btn-<?= $postId ?>" type="button" class="post-like-btn btn btn-outline-primary btn-sm">
                        <i class="bi bi-hand-thumbs-up<?= $liked ? '-fill' : '' ?>"></i>
                        <span class="post-like-count"><?= $likeCount ?></span>
                    </button>
                    <button id="post-comment-btn-<?= $postId ?>" type="button" class="view-comments-btn btn btn-outline-primary btn-sm" data-post-id="<?= $postId ?>">
                        <i class="bi bi-chat<?= $commented ? '-fill' : '' ?>"></i>
                        <span class="post-comment-count"><?= $commentCount ?></span>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Comment section -->
        <div class="comment-section-<?= $postId ?>" style="display:none">
            <?php
                if (isLoggedIn()) {
                    echo renderAddComment($pdo, $postId); // Render 'add comment' section
                }
                // Fetch all post comment
                $stmt = $pdo->prepare("
                    SELECT c.comment_id, c.comment_text, c.comment_created, comment_edited, u.user_id, u.first_name, u.last_name, p.profile_id, p.profile_picture 
                    FROM comments c 
                    INNER JOIN users u ON c.user_id = u.user_id 
                    INNER JOIN profiles p ON c.user_id = p.user_id 
                    WHERE c.post_id = :pid
                ");
                $stmt->bindParam(':pid', $postId, PDO::PARAM_INT);
                $stmt->execute();
                $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
                // If the post has comments
                if ($comments) {
                    foreach ($comments as $comment) {
                        echo renderComment($pdo, $comment, $postId); // Render each comment
                    }
                }
            ?>
            <!-- View more comments btn -->
            <div id="view-more-comments-wrapper-<?= $postId ?>" class="justify-content-center view-more-comments-wrapper mt-3" style="display: none;">
                <button id="view-more-comments-btn-<?= $postId ?>" class="btn btn-sm btn-secondary view-more-comments-btn" data-post-id="<?= $postId ?>">View more comments</button>
            </div>
        </div>
    </div><br> <!-- Post end -->
    <?php
    return ob_get_clean();
}

// Get posts
function getPosts($pdo, $profile_id = null) {
    // Get specific users posts
    if ($profile_id) {
        $stmt = $pdo->prepare("
            SELECT p.*, pr.profile_id
            FROM posts AS p
            INNER JOIN profiles AS pr ON pr.user_id = p.user_id
            WHERE pr.profile_id = :profile_id
            ORDER BY p.post_created DESC
        ");
        $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    } 
    // Get all posts on message board
    else {
        $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY post_created DESC");
    }

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Render posts (if any in DB)
    if ($posts) {
        foreach ($posts as $post) {
            echo renderPost($pdo, $post);
        }
    } 
    // No posts in DB
    else {
        echo('<p class="text-center">No Posts Available</p>');
    }
}
?>