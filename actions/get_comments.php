<?php
// Base query for all comments on a post
$get_post_comments = "
    SELECT 
        c.comment_id,
        c.comment_text,
        c.comment_created,
        c.comment_edited,
        u.user_id,
        u.first_name,
        u.last_name,
        p.profile_id,
        p.profile_picture,
        COUNT(DISTINCT cl.comment_like_id) AS like_count";

// Append user-specific flag if logged in
if (isLoggedIn()) {
    $get_post_comments .= ",
        EXISTS(
            SELECT 1
            FROM comment_likes
            WHERE comment_id = c.comment_id
              AND user_id = :current_user_id
        ) AS user_has_liked";
}

$get_post_comments .= "
    FROM comments AS c
    INNER JOIN users AS u 
        ON c.user_id = u.user_id
    INNER JOIN profiles AS p 
        ON c.user_id = p.user_id
    LEFT JOIN comment_likes AS cl 
        ON cl.comment_id = c.comment_id
    WHERE c.post_id = :pid
    GROUP BY c.comment_id
    ORDER BY c.comment_created ASC";
?>