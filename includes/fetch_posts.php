<?php 

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

            // Get the profile picture and name of the user that made a post
            $sql = "SELECT u.first_name, u.last_name, p.profile_picture FROM users u JOIN profiles p ON u.user_id = p.user_id WHERE u.user_id = :user_id";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $post['user_id'], PDO::PARAM_INT);
            $stmt->execute();
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            // Store postee name and profile picture data
            $user_first = $data['first_name'];
            $user_last = $data['last_name'];
            $user_name = $user_first.' '.$user_last; // Users full name
            $profile_picture = $data['profile_picture'];

            // Get each posts like count
            $postLikesStmt = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ?");
            $postLikesStmt->execute([$post['post_id']]);
            $postLikeCount = $postLikesStmt->fetchColumn();
            // Check whether the user signed in has liked each post returned
            if (isset($_SESSION['user_id'])) {
                $postLikedStmt = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?");
                $postLikedStmt->execute([$post['post_id'], $_SESSION['user_id']]);
                $postLiked = $postLikedStmt->fetchColumn() ? true : false;
            } else {
                $postLiked = false;
            }

            //Post timestamp data
            $timestamp = date('g:iA j/n/y', strtotime($post['post_created']));
            $post_timestamp = !empty($post['post_edited']) ? $timestamp . ' (edited)' : $timestamp;

            // Display each post in HTML
            // Users Profile Picture & Full Name
            echo('<div class="post-container py-4 px-4">
                    <div class="d-flex align-items-start pt-1">
                        <div>
                            <a class="post-profile-link" href="profile.php?user_id='.htmlspecialchars($post['user_id']).'">
                            <img class="me-3 rounded-pill post-profile-picture" src="'.htmlspecialchars($profile_picture). '" alt="Post Image">
                        </div>
                        <div class="w-auto">         
                            <h5 class="break-text">'.$user_name.'</a></h5>
                        </div>');
                        
                        // Display a dropdown on each post if the user is logged in and the post is their own
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                            echo('<div id="post-dropdown-'.htmlspecialchars($post['post_id']).'" class="dropdown ms-auto h-100 d-flex align-items-start">
                                <span id="post-options-'.htmlspecialchars($post['post_id']).'" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="height: auto; cursor: pointer;">
                                    <i class="bi bi-three-dots-vertical" style="color:grey; font-size:20px;"></i>
                                </span>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="post-options-'.htmlspecialchars($post['post_id']).'">
                                    <li><button class="dropdown-item edit-post-btn edit-post-dropdown-item" type="button" data-post-id="'.htmlspecialchars($post['post_id']).'" style="border: none; background: none; cursor: pointer;">Edit</button></li>
                                    <li>
                                        <form id="post-options-form-'.htmlspecialchars($post['post_id']).'" method="POST">
                                            <input type="hidden" name="delete_post" value="true">
                                            <input type="hidden" name="post_id" value="'.htmlspecialchars($post['post_id']).'">
                                            <button type="submit" class="dropdown-item edit-post-dropdown-item text-danger" style="border: none; cursor: pointer;">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                                </div>'); 
                        }
                    echo('</div>
                    <form id="edit-post-form-' . htmlspecialchars($post['post_id']) .'" method="POST">');
                        // Display any pictures added to the post
                        echo('<div>
                                <img id="post-picture-' . htmlentities($post['post_id']) . '" class="post-picture mt-3" src="' . (!empty($post['post_picture']) ? htmlspecialchars($post['post_picture']) : "") . '" alt="Post Image"' . (empty($post['post_picture']) ? 'style="display:none;"' : '') .'>
                            </div>
                            <div>
                                <input type="file" name="post-image-upload" id="post-image-upload-' . htmlentities($post['post_id']).'" class="post-image-upload" accept="image/*" hidden>
                                <button type="button" class="btn btn-sml btn-light mt-3" id="post-image-upload-btn-' . htmlentities($post['post_id']) . '" onclick="document.getElementById(\'post-image-upload-' . htmlentities($post['post_id']) . '\').click()" style="border:none; display:none;">
                                    <i class="bi bi-card-image" style="font-size: 16px !important;"></i>
                                </button>
                            </div>');
                        // Display the post text and DAT the post was created
                        echo('<div class="mt-3">
                                <div class="post-like-container mb-2" data-post-id="' . htmlentities($post['post_id']) . '">
                                    <button id="post-like-btn-'.htmlentities($post["post_id"]).'" type="button" class="post-like-btn btn btn-outline-primary btn-sm">
                                        <i class="bi bi-hand-thumbs-up' . ($postLiked ? "-fill" : "") . '"></i>
                                        <span class="post-like-count">' . htmlspecialchars($postLikeCount) . '</span>
                                    </button>
                                </div>
                                <p id="post-description-' . htmlentities($post['post_id']).'" class="break-text mb-2">' .htmlentities($post['post_text']) . '</p>
                                <textarea id="post-textarea-' . htmlentities($post['post_id']).'" class="form-control post-textarea rounded mb-1 responsive-textarea" name="post_textarea" rows="1" maxlength="250" hidden required disabled>'.htmlentities($post['post_text']).'</textarea>      
                                <div class="d-flex">
                                    <p class="mb-0" style="color:grey;">'.htmlentities($post_timestamp).'</p>
                                    <div id="edit-post-btn-group-' . htmlentities($post['post_id']).'" class="ms-auto edit-post-btn-group mt-1">
                                        <input type="hidden" name="post_id" value="'.htmlspecialchars($post['post_id']).'">
                                        <button class="btn btn-sm btn-secondary ms-1 edit-post-cancel-btn" type="button" data-post-id="'.htmlspecialchars($post['post_id']).'" name="edit-cancel-post">Cancel</button>
                                        <button id="edit-post-submit-btn" class="btn btn-sm btn-primary ms-1" type="submit">Post</button>
                                    </div>
                                </div>    
                            </div>
                    </form>');


            // Add a comment section
            if (isset($_SESSION['user_id']) ) {
                $sql = "SELECT profile_picture FROM profiles WHERE user_id =:user_id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
                $stmt->execute();
                $data = $stmt->fetch(PDO::FETCH_ASSOC);
                $your_profile_picture = $data['profile_picture'];
                echo('<hr><div class="d-flex pt-3">
                        <div>
                            <img class="me-3 rounded-pill comment-profile-picture" src="'.htmlspecialchars($your_profile_picture).'" alt="Post Image">
                        </div>
                        <form id="add-comment-form-'.htmlspecialchars($post['post_id']).'" data-post-id="'.htmlspecialchars($post['post_id']).'" method="POST" class="w-100">
                            <textarea id="add-comment-textarea-'.htmlspecialchars($post['post_id']).'" class="form-control w-100 add-comment-textarea rounded responsive-textarea mb-2" name="comment_text" placeholder="Add a comment..." rows="1" maxlength="250"></textarea>
                            <input type="hidden" name="post_id" value="'.htmlspecialchars($post['post_id']).'">
                            <div id="add-comment-btns-'.htmlspecialchars($post['post_id']).'" class="add-comment-btns">
                                <button class="btn btn-sm btn-secondary cancel-btn" type="button" data-post-id="'.htmlspecialchars($post['post_id']).'">Cancel</button>
                                <button class="btn btn-sm btn-primary ms-1">Comment</button>
                            </div>
                        </form>
                    </div>');
            }

        
            // Add any comments on the post
            $post_id = $post['post_id']; 
            $stmt = $pdo->prepare("
                SELECT c.comment_id, c.comment_text, c.comment_created, comment_edited, u.user_id, u.first_name, u.last_name, p.profile_picture 
                FROM comments c 
                INNER JOIN users u ON c.user_id = u.user_id 
                INNER JOIN profiles p ON c.user_id = p.user_id 
                WHERE c.post_id = :pid
            ");

            $stmt->bindParam(':pid', $post['post_id'], PDO::PARAM_INT);
            $stmt->execute();
            $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($comments) {
                foreach ($comments as $comment) {

                    // Get the commentors name and profile picture
                    $commentor_name = htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']);
                    $commentor_profile_picture = htmlspecialchars($comment['profile_picture']);
                    // Get each comments like count
                    $commentLikesStmt = $pdo->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?");
                    $commentLikesStmt->execute([$comment['comment_id']]);
                    $commentLikeCount = $commentLikesStmt->fetchColumn();
                    // Check whether the user signed in has liked the comment
                    if (isset($_SESSION['user_id'])) {
                        $commentLikedStmt = $pdo->prepare("SELECT 1 FROM comment_likes WHERE comment_id = ? AND user_id = ?");
                        $commentLikedStmt->execute([$comment['comment_id'], $_SESSION['user_id']]);
                        $commentLiked = $commentLikedStmt->fetchColumn() ? true : false;
                    } else {
                        $commentLiked = false;
                    }
                    // Get comment timestamp data
                    $timestamp = date('g:iA j/n/y', strtotime($comment['comment_created']));
                    $comment_timestamp = !empty($comment['comment_edited']) ? $timestamp . ' (edited)' : $timestamp;
            
                    echo ('<hr><div class="comment">
                            <div class="d-flex align-items-start">
                                <div>
                                    <a class="post-profile-link" href="profile.php?user_id=' . $comment['user_id'] . '">
                                    <img class="me-3 rounded-pill comment-profile-picture" src="' . $commentor_profile_picture . '" alt="Profile Picture">
                                </div>

                                <div class="w-auto">
                                    <p class="break-text mb-2"><strong>' . $commentor_name . '</strong></a></p>
                                </div>

                                ');
                                    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
                                        echo('<div id="comment-dropdown-'.htmlspecialchars($comment['comment_id']).'" class="dropdown ms-auto" style="height:20px;">
                                            <span id="comment-options-'.htmlspecialchars($comment['comment_id']).'" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
                                                <i class="bi bi-three-dots-vertical" style="color:grey; font-size:20px;"></i>
                                            </span>
                                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="post-options-'.htmlspecialchars($comment['comment_id']).'">
                                                <li><button type="button" class="dropdown-item edit-btn edit-comment-dropdown-item" data-post-id="'.htmlspecialchars($comment['comment_id']).'" style="border: none; background: none; cursor: pointer;">Edit</button></li>
                                                <li>
                                                    <form id="comment-options-form-'.htmlspecialchars($comment['comment_id']).'" method="POST">
                                                        <input type="hidden" name="delete_comment" value="true">
                                                        <input type="hidden" name="comment_id" value="'.htmlspecialchars($comment['comment_id']).'">
                                                        <button type="submit" class="dropdown-item edit-comment-dropdown-item text-danger" style="cursor: pointer;">Delete</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>'); 
                                    }

                            echo ('</div>

                            <div class="mt-2">
                                <form id="edit-comment-form-' . htmlspecialchars($comment["comment_id"]) . '" class="w-100" method="POST">

                                    <div class="comment-like-container mb-2" data-post-id="' . htmlentities($comment['comment_id']) . '">
                                        <button id="comment-like-btn-'.htmlentities($comment['comment_id']).'" type="button" class="comment-like-btn btn btn-outline-primary btn-sm">
                                            <i class="bi bi-hand-thumbs-up' . ($commentLiked ? "-fill" : "") . '"></i>
                                            <span class="comment-like-count">' . htmlspecialchars($commentLikeCount) . '</span>
                                        </button>
                                    </div>

                                    <p id="comment-description-' . htmlentities($comment['comment_id']).'" class="break-text mb-2" >' .htmlspecialchars($comment['comment_text']) . '</p>
                                    <textarea id="comment-textarea-' . htmlspecialchars($comment["comment_id"]) . '" class="form-control comment-textarea rounded mb-1 responsive-textarea" name="edit_comment" data-post-id="'.htmlspecialchars($comment['comment_id']).'" maxlength="250" rows="1" hidden required disabled>' . htmlspecialchars($comment['comment_text']) . '</textarea>
                                    <input type="hidden" name="comment_id" value="' . htmlspecialchars($comment['comment_id']) . '">
                                    
                                    <div class="d-flex">          
                                        <p class="mb-0" style="color:grey;">' . $comment_timestamp . '</p>
                                        <div id="edit-comment-btns-'.htmlspecialchars($comment['comment_id']).'" class="edit-comment-btns ms-auto mt-1">
                                            <button class="btn btn-sm btn-secondary edit-cancel-btn" type="button" data-post-id="'.htmlspecialchars($comment['comment_id']).'">Cancel</button>
                                            <button class="btn btn-sm btn-primary ms-1" type="submit">Comment</button>
                                        </div>
                                    </div>

                                </form>
                                    
                            </div>
                        </div>');
                }
            }
            echo('</div><br><br>'); // CLOSE PARENT (POST) DIV
        }
    } else {
        echo('<p class="text-center">No Posts Available</p>');
    }
}
?>