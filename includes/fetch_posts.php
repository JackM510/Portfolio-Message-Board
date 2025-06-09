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

            $user_first = $data['first_name'];
            $user_last = $data['last_name'];
            $user_name = $user_first.' '.$user_last; // Users full name
            $profile_picture = $data['profile_picture'];

            $post_timestamp = date('Y-m-d H:i', strtotime($post['post_created']));

            $post_timestamp = $post['post_edited'] 
                        ?  date('Y-m-d H:i', strtotime($post['post_created'])) . " (edited)" 
                        :  date('Y-m-d H:i', strtotime($post['post_created']));

            // Display each post in HTML
            // Users Profile Picture & Full Name
            echo('<div class="post-container p-4">
                    <div class="d-flex align-items-center">
                        <a class="post-profile-link" href="profile.php?user_id='.htmlspecialchars($post['user_id']).'">
                            <img class="me-3 rounded-pill post-profile-picture" src="'.htmlspecialchars($profile_picture). '" alt="Post Image">
                            <h5>'.$user_name.'</a></h5>');
                        // Display a dropdown on each post if the user is logged in and the post is their own
                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                            echo('<div id="post-dropdown-'.htmlspecialchars($post['post_id']).'" class="dropdown ms-auto">
                                <span id="post-options-'.htmlspecialchars($post['post_id']).'" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
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
                        echo('<div class="mt-3">
                                <img id="post-picture-' . htmlentities($post['post_id']) . '" class="post-picture" src="' . (!empty($post['post_picture']) ? htmlspecialchars($post['post_picture']) : "") . '" alt="Post Image"' . (empty($post['post_picture']) ? 'style="display:none;"' : '') .'>
                            </div>
                            <div class="mt-4">
                                <input type="file" name="post-image-upload" id="post-image-upload-' . htmlentities($post['post_id']).'" class="post-image-upload" accept="image/*" hidden>
                                <button type="button" class="btn btn-sml btn-light" id="post-image-upload-btn-' . htmlentities($post['post_id']) . '" onclick="document.getElementById(\'post-image-upload-' . htmlentities($post['post_id']) . '\').click()" style="border:none; display:none;">
                                    <i class="bi bi-card-image" style="font-size: 16px;"></i>
                                </button>
                            </div>');
                        // Display the post text and DAT the post was created
                        echo('<div class="mt-3">
                                <textarea id="post-textarea-' . htmlentities($post['post_id']).'" class="post-textarea rounded" name="post_textarea" disabled>'.htmlentities($post['post_text']).'</textarea>      
                                <div id="edit-post-btn-group-' . htmlentities($post['post_id']).'" class="ms-auto edit-post-btn-group">
                                    <input type="hidden" name="post_id" value="'.htmlspecialchars($post['post_id']).'">
                                    <button class="btn btn-sm btn-secondary ms-1 edit-post-cancel-btn" type="button" data-post-id="'.htmlspecialchars($post['post_id']).'" name="edit-cancel-post">Cancel</button>
                                    <button id="edit-post-submit-btn" class="btn btn-sm btn-primary ms-1" type="submit">Post</button>
                                </div>
                                <p style="color:grey;">'.htmlentities($post_timestamp).'</p>
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
                            <textarea id="add-comment-textarea-'.htmlspecialchars($post['post_id']).'" class="w-100 add-comment-textarea rounded" name="comment_text" placeholder="Add a comment..." style="height: auto; resize:none;"></textarea>
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
                    $commentor_name = htmlspecialchars($comment['first_name'] . ' ' . $comment['last_name']);
                    $commentor_profile_picture = htmlspecialchars($comment['profile_picture']);
                    $comment_timestamp = $comment['comment_edited'] 
                        ?  date('Y-m-d H:i', strtotime($comment['comment_created'])) . " (edited)" 
                        :  date('Y-m-d H:i', strtotime($comment['comment_created']));
            
                    echo ('<hr><div class="comment">
                            <div class="d-flex">
                                <div>
                                    <a class="post-profile-link" href="profile.php?user_id=' . $comment['user_id'] . '">
                                    <img class="me-3 rounded-pill comment-profile-picture" src="' . $commentor_profile_picture . '" alt="Profile Picture">
                                </div>
                                <div class="w-100">
                                    <p><strong>' . $commentor_name . '</strong></a></p>
                                    <form id="edit-comment-form-' . htmlspecialchars($comment["comment_id"]) . '" class="w-100" method="POST">
                                        <textarea id="comment-textarea-' . htmlspecialchars($comment["comment_id"]) . '" class="w-100 comment-textarea rounded" name="edit_comment" data-post-id="'.htmlspecialchars($comment['comment_id']).'" style="height: auto; min-height: unset; resize:none;" required disabled>' . htmlspecialchars($comment['comment_text']) . '</textarea>
                                        <input type="hidden" name="comment_id" value="' . htmlspecialchars($comment['comment_id']) . '">
                                        <div id="edit-comment-btns-'.htmlspecialchars($comment['comment_id']).'" class="edit-comment-btns ms-auto">
                                            <button class="btn btn-sm btn-secondary edit-cancel-btn" type="button" data-post-id="'.htmlspecialchars($comment['comment_id']).'">Cancel</button>
                                            <button class="btn btn-sm btn-primary ms-1" type="submit">Comment</button>
                                        </div>
                                    </form>
                                    <p style="color:grey;">' . $comment_timestamp . '</p>
                                </div>');
                            
                            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $comment['user_id']) {
                                echo('<div id="comment-dropdown-'.htmlspecialchars($comment['comment_id']).'" class="dropdown ms-auto">
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

                        echo('</div>');
                    echo "</div>";
                }
            }
            echo('</div><br><br>'); // CLOSE PARENT DIV
        }
    } else {
        echo('<p>No Posts Available</p>');
    }
}
?>