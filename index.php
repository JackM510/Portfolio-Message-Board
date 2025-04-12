<?php
    session_start();
    require_once('db_connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/index.css" rel="stylesheet">
    <title>Message Board</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <section class="container text-center">
        <h1>Message Board</h1>
    </section>

    <!-- Example Post structure -->
    <section class="w-50 mx-auto">
        <div>
            <?php
                
                echo('<br><br><br>');

                // Get all posts from the DB
                $stmt = $pdo->prepare("SELECT * FROM posts ORDER BY post_created DESC");
                $stmt->execute();
                $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($posts) {
                    foreach ($posts as $post) {

                        // Get the user_id for the user that made the post
                        $post_user_id = $post['user_id']; 
                        $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = :uid"); // Get their first and last name from the users table
                        $stmt->bindParam(':uid', $post_user_id, PDO::PARAM_STR);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                        $user_first = $user['first_name'];
                        $user_last = $user['last_name'];
                        $user_name = $user_first.' '.$user_last; // Users full name

                        // Get the users profile pricture from the profile table
                        $stmt = $pdo->prepare("SELECT profile_picture FROM profiles WHERE user_id = :uid");
                        $stmt->bindParam(':uid', $post_user_id, PDO::PARAM_STR);
                        $stmt->execute();
                        $profile = $stmt->fetch(PDO::FETCH_ASSOC);
                        $profile_picture = $profile['profile_picture'];

                        echo('<div class="p-3 border">'); // POST PARENT CONTAINER
                        // PROFILE PIC AND NAME
                        echo('<div class="d-flex align-items-center">');
                            echo('<img class="me-3 rounded-pill" src="'.htmlspecialchars($profile_picture). '" alt="Post Image" style="width:40px;">');
                            echo('<h5>'.$user_name.'</h5>');
                        echo('</div>');
                        // POST IMAGE
                        if (!empty($post['post_picture'])) {
                            echo "<div class='mt-3'><img src='" . htmlspecialchars($post['post_picture']) . "' alt='Post Image' style='max-width:100px;'></div>";
                        }
                        // POST TEXT
                        echo('<div class="mt-3"><p>'.htmlentities($post['post_text']).'</p>');
                        echo('<p style="color:grey;">'.htmlentities($post['post_created']).'</p></div>');


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
                        
                                echo "<div class='comment'>";
                                    echo('<div class="d-flex">');
                                        echo "<img class='me-3 rounded-pill' src='" . $commentor_profile_picture . "' alt='Profile Picture' style='max-width:40px;'>";
                                        echo "<p><strong>" . $commentor_name . ":</strong> " . htmlspecialchars($comment['comment_text']) . "</p>";
                                    echo('</div>');
                                    echo('<div>');
                                        echo "<p><small>Posted on: " . htmlspecialchars($comment['comment_created']) . "</small></p>";
                                    echo('</div>');    
                                echo "</div><hr>";
                            }
                        } else {
                            echo "<p>No comments yet.</p>";
                        }
                        
                        // Add a comment section
                        echo('<div><form><textarea></textarea><button>Comment</button></form></div>');


                        echo('</div><br>'); // CLOSE PARENT DIV

                    }
                } else {
                    echo('<p>No Posts Available</p>');
                }
                /*

                Need to select all comments for the specific post
                $stmt = $pdo->prepare("SELECT * FROM comments WHERE post_id = :pid");
                --- for each comment get the picture, name, and comment from the user:
                $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE user_id = :comment_uid");
                $stmt = $pdo->prepare("SELECT profile_picture FROM profies WHERE user_id = :comment_id");



                        */
            ?>    

            <div id="content">
            <!-- Persons Profile Pic & Name -->
             
            <!-- Their Post -->
            </div>
            <div id="comments">
            <!-- Any existing comments -->
            </div>
            <div id="add_comments">
            <!-- section to add a comment --> 
            </div>
        </div>
    </section>


</body>
</html>