<?php
    session_start();
    require_once('db_connection.php');

    // Add a comment functionality


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/index.css" rel="stylesheet">
    <script src="js/index.js"></script>
    <title>Message Board</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>

    <!-- Example Post structure -->
    <section class="container w-50 mx-auto mt-5">
        <div>
            <?php

                // Get all posts from the DB
                $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY post_created DESC");
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
                                    <img class="me-3 rounded-pill" src="'.htmlspecialchars($profile_picture). '" alt="Post Image" style="width:40px;">
                                    <h5>'.$user_name.'</h5>
                                </div>');
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
                                    <form id="add-comment-form-'.htmlspecialchars($post['post_id']).'" method="POST" class="w-100">
                                        <textarea id="add-comment-textarea-'.htmlspecialchars($post['post_id']).'" class="w-100 add-comment-textarea" placeholder="Add a comment..." rows="3" style="resize:none;"></textarea>
                                        <div id="add-comment-btns-'.htmlspecialchars($post['post_id']).'" class="add-comment-btns">
                                            <button class="btn btn-sm btn-secondary cancel-btn" type="button" data-post-id="'.htmlspecialchars($post['post_id']).'">Cancel</button>
                                            <button class="btn btn-sm btn-primary ms-1">Comment</button>
                                        </div>
                                    </form>
                                </div>');
                        }

                    
                        // ADD ALL COMMENTS ON THE POST
                        $post_id = $post['post_id']; 
                        $stmt = $pdo->prepare("
                            SELECT c.comment_text, c.comment_created, u.first_name, u.last_name, p.profile_picture 
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
                                        echo "<img class='me-3 rounded-pill' src='" . $commentor_profile_picture . "' alt='Profile Picture' style='max-width:40px;'>";
                                        echo "<p><strong>" . $commentor_name . ":</strong> " . htmlspecialchars($comment['comment_text']) . "</p>";
                                    echo('</div>');
                                echo "</div>";
                            }
                        }
                        


                        echo('</div><br><br>'); // CLOSE PARENT DIV

                    }
                } else {
                    echo('<p>No Posts Available</p>');
                }

            ?>    

        </div>
    </section>


</body>
</html>