<?php 
require_once(ACTION_GET_POSTS);
require_once(ACTION_GET_COMMENTS);

// Post section
function renderPost(PDO $pdo, array $post): string {
    // Post data
    $postId = htmlspecialchars($post['post_id']);
    $profileId = htmlspecialchars($post['profile_id']);
    $profilePic = htmlspecialchars($post['profile_picture']);
    $userName = htmlspecialchars($post['first_name'] . ' ' . $post['last_name']);
    $post_picture = htmlspecialchars($post['post_picture']);
    $post_text = htmlspecialchars($post['post_text']);
    $timestamp = date('g:iA j/n/y', strtotime($post['post_created']));
    $postTimestamp = !empty($post['post_edited']) ? "$timestamp (edited)" : $timestamp;
    $likeCount = $post['like_count']; // Post like count
    $commentCount = $post['comment_count']; // Post comment count
    $liked = !empty($post['user_has_liked']); // True if logged in user has liked the post
    $commented = !empty($post['user_has_commented']); // True if logged in user has commented on the post

    ob_start(); ?>
    <!-- Post start -->
    <div class="post-container">
        <!-- Postee data & dropdown -->
        <div class="d-flex align-items-start pt-1">
            <!-- Postee profile pic & full name -->
            <a class="post-profile-link d-flex" href="<?= PROFILE_URL . '?profile_id=' . urlencode($profileId) ?>">
                <img class="post-profile-picture rounded-pill me-2 "
                    src="<?= APP_BASE_PATH . "/" . $profilePic ?>" alt="Post Image">
                <h5 class="break-text mb-0"><?= $userName ?></h5>
            </a>
            <!-- Edit post dropdown -->
            <?php if (isLoggedIn() && $_SESSION['user_id'] == htmlspecialchars($post['user_id'])): ?>
            <div class="post-dropdown-<?= $postId ?> dropdown d-flex align-items-start h-100 ms-auto">
                <span class="post-options-<?= $postId ?>" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-three-dots-vertical"></i>
                </span>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><button class="edit-post-btn dropdown-item edit-post-dropdown-item" type="button" data-post-id="<?= $postId ?>">Edit</button></li>
                    <li>
                        <form class="post-options-form-<?= $postId ?>" method="POST" data-post-id="<?= $postId ?>">
                            <input type="hidden" name="delete_post" value="true">
                            <input type="hidden" name="post_id" value="<?= $postId ?>">
                            <button class="dropdown-item edit-post-dropdown-item text-danger" type="submit">Delete</button>
                        </form>
                    </li>
                </ul>
            </div>
            <?php endif; ?>
        </div>

        <!-- Post data -->
        <form class="edit-post-form-<?= $postId ?>" method="POST">
            <!-- Post img -->
            <div>
                <img class="post-picture-<?= $postId ?> mt-3"
                    src="<?= !empty($post_picture) ? APP_BASE_PATH . "/" . $post_picture : '' ?>"
                    alt="Post Image"
                    style="<?= empty($post_picture) ? 'display:none;' : '' ?>">
            </div>
            <!-- Post img upload btn-->
            <div>
                <input class="post-img-upload-<?= $postId ?>" type="file" name="post-img-upload" data-post-id="<?= $postId ?>" accept="image/*" hidden>
                <button class="post-img-upload-btn-<?= $postId ?> btn btn-sml btn-light mt-3" type="button" data-post-id="<?= $postId ?>">
                    <i class="bi bi-card-image"></i>
                </button>
            </div>
            
            <div class="mt-3">
                <!-- Post text -->      
                <p class="post-description-<?= $postId ?> break-text mb-2"><?= $post_text ?></p>
                <textarea class="post-textarea-<?= $postId ?> form-control rounded mb-1" name="post_textarea" rows="1" maxlength="255" hidden required disabled><?= $post_text ?></textarea>
                <!-- Post edit btns -->
                <div class="d-flex">
                    <p class="post-timestamp mb-1"><?= $postTimestamp ?></p>
                    <div class="edit-post-btn-group-<?= $postId ?> mt-1 ms-auto">
                        <input type="hidden" name="post_id" value="<?= $postId ?>">
                        <button class="edit-post-cancel-btn btn btn-sm btn-secondary ms-1" type="button" name="edit-cancel-post" data-post-id="<?= $postId ?>">Cancel</button>
                        <button class="btn btn-sm btn-primary ms-1" type="submit">Post</button>
                    </div>
                </div>

                <!-- Post like & comment btns -->
                <div class="post-like-container-<?= $postId ?> d-flex">
                    <button class="post-like-btn-<?= $postId ?> btn btn-sm btn-outline-primary" type="button" data-post-id="<?= $postId ?>">
                        <i class="bi bi-hand-thumbs-up<?= $liked ? '-fill' : '' ?>"></i>
                        <span class="post-like-count"><?= $likeCount ?></span>
                    </button>
                    <button class="post-comment-btn-<?= $postId ?> btn btn-sm btn-outline-primary" type="button" data-post-id="<?= $postId ?>">
                        <i class="bi bi-chat<?= $commented ? '-fill' : '' ?>"></i>
                        <span class="post-comment-count"><?= $commentCount ?></span>
                    </button>
                </div>
            </div>
        </form>
        
        <!-- Comment section -->
        <div class="comment-section-<?= $postId ?>">
            <?php
                // Render 'add comment' section
                if (isLoggedIn()) {
                    echo renderAddComment($pdo, $postId);
                }

                // Fetch all comments on the post
                global $get_post_comments;
                $stmt = $pdo->prepare($get_post_comments);
                $stmt->bindValue(':pid', $postId, PDO::PARAM_INT);
                if (isLoggedIn()) {
                    $stmt->bindValue(':current_user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                }
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
            <div class="view-more-comments-wrapper-<?= $postId ?> justify-content-center mt-3">
                <button class="view-more-comments-btn btn btn-sm btn-secondary" data-post-id="<?= $postId ?>">View more comments</button>
            </div>
        </div>
        <!-- Comment end -->
    </div><br>
    <!-- Post end -->
    <?php
    return ob_get_clean();
}

// Add comment section
function renderAddComment(PDO $pdo, int $postId): string {
    $postId = htmlspecialchars($postId);
    ob_start();
    ?>

    <!-- Add comment start -->
    <hr>
    <div class="add-comment d-flex pt-3">
        <!-- Users profile pic -->
        <div>
            <img class="comment-profile-picture rounded-pill me-3" src="<?= APP_BASE_PATH . "/" . $_SESSION['avatar'] ?>" alt="Post Image">
        </div>
        <!-- TA and cancel/comment btns -->
        <form class="add-comment-form-<?= $postId ?> w-100" method="POST" data-post-id="<?= $postId ?>">
            <textarea class="add-comment-textarea-<?= $postId ?> form-control w-100 rounded mb-2" name="comment_text" placeholder="Add a comment..." data-post-id="<?= $postId ?>" rows="1" maxlength="255"></textarea>
            <input type="hidden" name="post_id" value="<?= $postId ?>">
            <div class="add-comment-btns-<?= $postId ?>">
                <button class="add-comment-cancel-btn btn btn-sm btn-secondary" type="button" data-post-id="<?= $postId ?>">Cancel</button>
                <button class="btn btn-sm btn-primary ms-1">Comment</button>
            </div>
        </form>
    </div>
    <!-- Add comment end -->
    <?php
    return ob_get_clean();
}

// Comments section
function renderComment(PDO $pdo, array $comment, int $postId): string {
    // Comment data
    $profileId = htmlspecialchars($comment['profile_id']);
    $commentorName = htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']);
    $commentorPic = htmlspecialchars($comment['profile_picture']);
    $commentId = htmlspecialchars($comment['comment_id']);
    $commentText = htmlspecialchars($comment['comment_text']);
    $postIdEscaped = htmlspecialchars($postId);
    $timestamp = date('g:iA j/n/y', strtotime($comment['comment_created']));
    $commentTimestamp = !empty($comment['comment_edited']) ? "$timestamp (edited)" : $timestamp;
    $likeCount = $comment['like_count'];  // Comment like count
    $liked = !empty($comment['user_has_liked']); // True if logged in user has liked the comment

    ob_start(); ?>
    <!-- Comment start -->
    <div class="comment-<?= $postId ?>">
        <hr>
        <div class="d-flex align-items-start">
            <!-- Commentor profile pic & full name -->
            <a class="post-profile-link d-flex" href="<?= PROFILE_URL . '?profile_id=' . urlencode($profileId) ?>">
                <img class="comment-profile-picture rounded-pill me-2" 
                    src="<?= APP_BASE_PATH . "/" . $commentorPic ?>" alt="Profile Picture">
                <p class="break-text mb-2"><strong><?= $commentorName ?></strong></p>
            </a>
            
            <!-- Comment dropdown options -->
            <?php if (isLoggedIn() && $_SESSION['user_id'] == htmlspecialchars($comment['user_id'])): ?>
                <div class="comment-dropdown-<?= $commentId ?> dropdown ms-auto">
                    <!-- Comment three-dot -->
                    <span class="comment-options-<?= $commentId ?>" data-bs-toggle="dropdown" aria-expanded="false" role="button">
                        <i class="bi bi-three-dots-vertical"></i>
                    </span>
                    <!-- Dropdown options -->
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><button class="edit-comment-btn dropdown-item edit-comment-dropdown-item" type="button" data-post-id="<?= $commentId ?>">Edit</button></li>
                        <li>
                            <form class="comment-options-form-<?= $commentId ?>" method="POST" data-post-id="<?= $postIdEscaped ?>">
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
            <form class="edit-comment-form-<?= $commentId ?> w-100" data-post-id="<?= $postIdEscaped ?>" method="POST" action="<?= ACTION_EDIT_COMMENT ?>">
                <!-- Comment text -->
                <p class="comment-description-<?= $commentId ?> break-text mb-2"><?= $commentText ?></p>
                <textarea class="comment-textarea-<?= $commentId ?> form-control rounded mb-1" name="edit_comment" data-post-id="<?= $commentId ?>" rows="1"  maxlength="255" hidden required disabled><?= $commentText ?></textarea>
                <input type="hidden" name="comment_id" value="<?= $commentId ?>">
                <!-- Comment timestamp & cancel/submit btns -->
                <div class="d-flex">
                    <p class="comment-timestamp mb-0"><?= $commentTimestamp ?></p>
                    <div class="edit-comment-btns-<?= $commentId ?> ms-auto mt-1">
                        <button class="edit-comment-cancel-btn btn btn-sm btn-secondary" type="button" data-post-id="<?= $commentId ?>">Cancel</button>
                        <button class="btn btn-sm btn-primary ms-1" type="submit">Comment</button>
                    </div>
                </div>
                <!-- Comment like btn -->
                <div class="comment-like-container mb-2">
                    <button class="comment-like-btn-<?= $commentId ?> btn btn-sm btn-outline-primary" type="button" data-post-id="<?= $commentId ?>">
                        <i class="bi bi-hand-thumbs-up<?= $liked ? '-fill' : '' ?>"></i>
                        <span class="comment-like-count"><?= htmlspecialchars($likeCount) ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Comment end --> 
    <?php
    return ob_get_clean();
}

// Get posts call
function getPosts($pdo, $profile_id = null) {
    // Get users posts
    if ($profile_id) {
        global $get_posts_by_profile;
        $stmt = $pdo->prepare($get_posts_by_profile);
        if (isLoggedIn()) {
            $stmt->bindValue(':current_user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        }
        $stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
    } 
    // Get all posts
    else {
        global $get_all_posts;
        $stmt = $pdo->prepare($get_all_posts);
        if (isLoggedIn()) {
            $stmt->bindValue(':current_user_id', $_SESSION['user_id'], PDO::PARAM_INT);
        }
    }
    // Execute SQL SELECT
    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Render posts
    if ($posts) {
        foreach ($posts as $post) {
            echo renderPost($pdo, $post);
        }
    } else {
        echo('<p class="text-center">No Posts Available</p>'); // No posts in DB
    }
}
?>