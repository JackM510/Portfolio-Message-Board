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


            // Display each post in HTML
            // Users Profile Picture & Full Name
            echo('<div class="p-4 border">
                    <div class="d-flex align-items-center">
                        <a class="post-profile-link" href="profile.php?user_id='.htmlspecialchars($post['user_id']).'"><img class="me-3 rounded-pill" src="'.htmlspecialchars($profile_picture). '" alt="Post Image" style="width:40px;">
                        <h5>'.$user_name.'</a></h5>');

                        if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']) {
                            echo('<div class="dropdown ms-auto">
                                <span id="post-options-'.htmlspecialchars($post['post_id']).'" data-bs-toggle="dropdown" aria-expanded="false" role="button" style="cursor: pointer;">
                                    <i class="bi bi-three-dots-vertical" style="color:black; font-size:20px;"></i>
                                </span>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="post-options-'.htmlspecialchars($post['post_id']).'">
                                    <li><a class="dropdown-item" href="#">Edit</a></li>
                                    <li>
                                        <form id="post-options-form-'.htmlspecialchars($post['post_id']).'" method="POST">
                                            <input type="hidden" name="delete_post" value="true">
                                            <input type="hidden" name="post_id" value="'.htmlspecialchars($post['post_id']).'">
                                            <button type="submit" class="dropdown-item text-danger" style="border: none; background: none; cursor: pointer;">Delete</button>
                                        </form>
                                    </li>
                                </ul>
                                </div>'); 
                        }
                    echo('</div>');
            // Display any pictures added to the post
            if (!empty($post['post_picture'])) {
                echo("<div class='mt-3'>
                        <img id='post-profile-picture' src='" . htmlspecialchars($post['post_picture']) . "' alt='Post Image'>
                    </div>");
            }
            // Display the post text and DAT the post was created
            echo('<div class="mt-3">
                <p>'.htmlentities($post['post_text']).'</p>
                <p style="color:grey;">'.htmlentities($post['post_created']).'</p>
                    </div>');


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
                            <img class="me-3 rounded-pill" src="'.htmlspecialchars($your_profile_picture).'" alt="Post Image" style="width:40px;">
                        </div>
                        <form id="add-comment-form-'.htmlspecialchars($post['post_id']).'" data-post-id="'.htmlspecialchars($post['post_id']).'" method="POST" class="w-100">
                            <textarea id="add-comment-textarea-'.htmlspecialchars($post['post_id']).'" class="w-100 add-comment-textarea" name="comment_text" placeholder="Add a comment..." rows="3" style="resize:none;"></textarea>
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
                SELECT c.comment_text, c.comment_created, u.user_id, u.first_name, u.last_name, p.profile_picture 
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
            
                    echo "<hr><div class='comment'>";
                        echo('<div class="d-flex">');
                            echo "<a class='post-profile-link' href='profile.php?user_id=" . $comment['user_id'] . "'><img class='me-3 rounded-pill' src='" . $commentor_profile_picture . "' alt='Profile Picture' style='max-width:40px;'>";
                            echo "<p><strong>" . $commentor_name . ": </strong></a>" . htmlspecialchars($comment['comment_text']) . "</p>";
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