<?php 

// Render comments on posts
function renderComment(PDO $pdo, array $comment, int $postId): string {
    $commentorName = htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']);
    $commentorPic = htmlspecialchars($comment['profile_picture']);
    $commentId = htmlspecialchars($comment['comment_id']);
    $commentText = htmlspecialchars($comment['comment_text']);
    $postIdEscaped = htmlspecialchars($postId);
    $userId = $comment['user_id'];

    // Like count
    $likeStmt = $pdo->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?");
    $likeStmt->execute([$commentId]);
    $likeCount = $likeStmt->fetchColumn();

    // Like status
    $liked = false;
    if (isset($_SESSION['user_id'])) {
        $likedStmt = $pdo->prepare("SELECT 1 FROM comment_likes WHERE comment_id = ? AND user_id = ?");
        $likedStmt->execute([$commentId, $_SESSION['user_id']]);
        $liked = $likedStmt->fetchColumn() ? true : false;
    }

    // Timestamp
    $timestamp = date('g:iA j/n/y', strtotime($comment['comment_created']));
    $commentTimestamp = !empty($comment['comment_edited']) ? "$timestamp (edited)" : $timestamp;

    ob_start(); ?>
    
    <div class="comment comment-<?= $postId ?>">
    <hr>
        <div class="d-flex align-items-start">
            <div>
                <a class="post-profile-link" href="profile.php?user_id=<?= $userId ?>">
                    <img class="me-3 rounded-pill comment-profile-picture" src="<?= $commentorPic ?>" alt="Profile Picture">
                </a>
            </div>
            <div class="w-auto">
                <p class="break-text mb-2"><strong><?= $commentorName ?></strong></p>
            </div>
            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
                <div id="comment-dropdown-<?= $commentId ?>" class="dropdown ms-auto" style="height:20px;">
                    <span id="comment-options-<?= $commentId ?>" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
                        <i class="bi bi-three-dots-vertical" style="color:grey; font-size:20px;"></i>
                    </span>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="post-options-<?= $commentId ?>">
                        <li><button type="button" class="dropdown-item edit-btn edit-comment-dropdown-item" data-post-id="<?= $commentId ?>">Edit</button></li>
                        <li>
                            <form id="comment-options-form-<?= $commentId ?>" data-post-id="<?= $postIdEscaped ?>" method="POST">
                                <input type="hidden" name="delete_comment" value="true">
                                <input type="hidden" name="comment_id" value="<?= $commentId ?>">
                                <button type="submit" class="dropdown-item edit-comment-dropdown-item text-danger">Delete</button>
                            </form>
                        </li>
                    </ul>
                </div>
            <?php endif; ?>
        </div>

        <div class="mt-2">
            <form id="edit-comment-form-<?= $commentId ?>" data-post-id="<?= $postIdEscaped ?>" class="w-100" method="POST">
                
                <p id="comment-description-<?= $commentId ?>" class="break-text mb-2"><?= $commentText ?></p>
                <textarea id="comment-textarea-<?= $commentId ?>" class="form-control comment-textarea rounded mb-1 responsive-textarea" name="edit_comment" data-post-id="<?= $commentId ?>" maxlength="250" rows="1" hidden required disabled><?= $commentText ?></textarea>
                <input type="hidden" name="comment_id" value="<?= $commentId ?>">

                <div class="d-flex">
                    <p class="mb-0" style="color:grey;"><?= $commentTimestamp ?></p>
                    <div id="edit-comment-btns-<?= $commentId ?>" class="edit-comment-btns ms-auto mt-1">
                        <button class="btn btn-sm btn-secondary edit-cancel-btn" type="button" data-post-id="<?= $commentId ?>">Cancel</button>
                        <button class="btn btn-sm btn-primary ms-1" type="submit">Comment</button>
                    </div>
                </div>

                <div class="comment-like-container mb-2" data-post-id="<?= $commentId ?>">
                    <button id="comment-like-btn-<?= $commentId ?>" type="button" class="comment-like-btn btn btn-outline-primary btn-sm">
                        <i class="bi bi-hand-thumbs-up<?= $liked ? '-fill' : '' ?>"></i>
                        <span class="comment-like-count"><?= htmlspecialchars($likeCount) ?></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

// Render 'add comment' section
function renderAddComment(PDO $pdo, int $postId): string {
    if (!isset($_SESSION['user_id'])) return '';

    $sql = "SELECT profile_picture FROM profiles WHERE user_id = :user_id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) return ''; // No profile pic found

    $profilePic = htmlspecialchars($data['profile_picture']);
    $postIdEscaped = htmlspecialchars($postId);

    ob_start(); // Start output buffering
    ?>
    <hr>
    <div class="add-comment d-flex pt-3">
        <div>
            <img class="me-3 rounded-pill comment-profile-picture" src="<?= $profilePic ?>" alt="Post Image">
        </div>
        <form id="add-comment-form-<?= $postIdEscaped ?>" data-post-id="<?= $postIdEscaped ?>" method="POST" class="w-100">
            <textarea id="add-comment-textarea-<?= $postIdEscaped ?>" class="form-control w-100 add-comment-textarea rounded responsive-textarea mb-2" name="comment_text" placeholder="Add a comment..." rows="1" maxlength="250"></textarea>
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
        SELECT u.first_name, u.last_name, p.profile_picture 
        FROM users u 
        JOIN profiles p ON u.user_id = p.user_id 
        WHERE u.user_id = :user_id
    ");
    $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    $userName = htmlspecialchars($userData['first_name'] . ' ' . $userData['last_name']);
    $profilePic = htmlspecialchars($userData['profile_picture']);

    // Like and comment counts
    $postLikes = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ?");
    $postLikes->execute([$postId]);
    $likeCount = $postLikes->fetchColumn();

    $commentCount = $pdo->prepare("SELECT COUNT(*) FROM comments WHERE post_id = ?");
    $commentCount->execute([$postId]);
    $commentCount = $commentCount->fetchColumn();

    // Has user liked/commented
    $liked = false;
    $commented = false;
    if (isset($_SESSION['user_id'])) {
        $checkLike = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?");
        $checkLike->execute([$postId, $_SESSION['user_id']]);
        $liked = $checkLike->fetchColumn() ? true : false;

        $checkComment = $pdo->prepare("SELECT 1 FROM comments WHERE post_id = ? AND user_id = ?");
        $checkComment->execute([$postId, $_SESSION['user_id']]);
        $commented = $checkComment->fetchColumn() ? true : false;
    }

    // Timestamp
    $timestamp = date('g:iA j/n/y', strtotime($post['post_created']));
    $postTimestamp = !empty($post['post_edited']) ? "$timestamp (edited)" : $timestamp;

    ob_start(); ?>
    <div class="post-container">
        <div class="d-flex align-items-start pt-1">
            <div>
                <a class="post-profile-link" href="profile.php?user_id=<?= htmlspecialchars($userId) ?>">
                    <img class="me-3 rounded-pill post-profile-picture" src="<?= $profilePic ?>" alt="Post Image">
                </a>
            </div>
            <div class="w-auto">
                <h5 class="break-text"><?= $userName ?></h5>
            </div>

            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $userId): ?>
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

        <form id="edit-post-form-<?= $postId ?>" method="POST">
        <div>
            <img id="post-picture-<?= $postId ?>"
                class="post-picture mt-3"
                src="<?= !empty($post['post_picture']) ? htmlspecialchars($post['post_picture']) : '' ?>"
                alt="Post Image"
                style="<?= empty($post['post_picture']) ? 'display:none;' : '' ?>">
        </div>


            <div>
                <input type="file" name="post-image-upload" id="post-image-upload-<?= $postId ?>" class="post-image-upload" accept="image/*" hidden>
                <button type="button" class="btn btn-sml btn-light mt-3" id="post-image-upload-btn-<?= $postId ?>" onclick="document.getElementById('post-image-upload-<?= $postId ?>').click()" style="border:none; display:none;">
                    <i class="bi bi-card-image" style="font-size: 16px !important;"></i>
                </button>
            </div>

            <div class="mt-3">           
                <p id="post-description-<?= $postId ?>" class="break-text mb-2"><?= htmlspecialchars($post['post_text']) ?></p>
                <textarea id="post-textarea-<?= $postId ?>" class="form-control post-textarea rounded mb-1 responsive-textarea" name="post_textarea" rows="1" maxlength="250" hidden required disabled><?= htmlspecialchars($post['post_text']) ?></textarea>

                <div class="d-flex">
                    <p class="mb-1" style="color:grey;"><?= $postTimestamp ?></p>
                    <div id="edit-post-btn-group-<?= $postId ?>" class="ms-auto edit-post-btn-group mt-1">
                        <input type="hidden" name="post_id" value="<?= $postId ?>">
                        <button class="btn btn-sm btn-secondary ms-1 edit-post-cancel-btn" type="button" data-post-id="<?= $postId ?>" name="edit-cancel-post">Cancel</button>
                        <button id="edit-post-submit-btn" class="btn btn-sm btn-primary ms-1" type="submit">Post</button>
                    </div>
                </div>

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
            <?= renderAddComment($pdo, $postId) ?>

            <?php
            // Fetch comments for this post
            $stmt = $pdo->prepare("
                SELECT c.comment_id, c.comment_text, c.comment_created, comment_edited, u.user_id, u.first_name, u.last_name, p.profile_picture 
                FROM comments c 
                INNER JOIN users u ON c.user_id = u.user_id 
                INNER JOIN profiles p ON c.user_id = p.user_id 
                WHERE c.post_id = :pid
            ");
            $stmt->bindParam(':pid', $postId, PDO::PARAM_INT);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($comments) {
                foreach ($comments as $comment) {
                    echo renderComment($pdo, $comment, $postId);
                }
            }
            ?>
            <div id="view-more-comments-wrapper-<?= $postId ?>" class="justify-content-center view-more-comments-wrapper mt-3" style="display: none;">
                <button id="view-more-comments-btn-<?= $postId ?>" class="btn btn-sm btn-secondary view-more-comments-btn" data-post-id="<?= $postId ?>">View more comments</button>
            </div>
        </div>
    </div><br>
    <?php
    return ob_get_clean();
}


// If user_id is not provided fetch ALL posts, else fetch only posts made by the specified user
function getPosts($pdo, $user_id = null) {
    if ($user_id) {
        $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = :user_id ORDER BY post_created DESC");
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY post_created DESC");
    }

    $stmt->execute();
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($posts) {
        foreach ($posts as $post) {
            echo renderPost($pdo, $post);
        }
    } else {
        echo('<p class="text-center">No Posts Available</p>');
    }
}
?>