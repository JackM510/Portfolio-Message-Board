<?php
// SQL for fetching posts by profile_id
$get_posts_by_profile = "
    SELECT 
        p.*,
        u.first_name,
        u.last_name,
        pr.profile_id,
        pr.profile_picture,
        COUNT(DISTINCT pl.post_like_id) AS like_count,
        COUNT(DISTINCT c.comment_id) AS comment_count";

// Append user-specific flags if logged in
if (isLoggedIn()) {
    $get_posts_by_profile .= ",
        EXISTS(
            SELECT 1 
            FROM post_likes 
            WHERE post_id = p.post_id 
              AND user_id = :current_user_id
        ) AS user_has_liked,
        EXISTS(
            SELECT 1 
            FROM comments 
            WHERE post_id = p.post_id 
              AND user_id = :current_user_id
        ) AS user_has_commented";
}

$get_posts_by_profile .= "
    FROM posts AS p
    INNER JOIN users AS u 
        ON u.user_id = p.user_id
    INNER JOIN profiles AS pr 
        ON pr.user_id = u.user_id
    LEFT JOIN post_likes AS pl 
        ON pl.post_id = p.post_id
    LEFT JOIN comments AS c 
        ON c.post_id = p.post_id
    WHERE pr.profile_id = :profile_id
    GROUP BY p.post_id
    ORDER BY p.post_created DESC";

// SQL for fetching all posts
$get_all_posts = "
    SELECT 
        p.*,
        u.first_name,
        u.last_name,
        pr.profile_id,
        pr.profile_picture,
        COUNT(DISTINCT pl.post_like_id) AS like_count,
        COUNT(DISTINCT c.comment_id) AS comment_count";

// Append user-specific flags if logged in
if (isLoggedIn()) {
    $get_all_posts .= ",
        EXISTS(
            SELECT 1 
            FROM post_likes 
            WHERE post_id = p.post_id 
              AND user_id = :current_user_id
        ) AS user_has_liked,
        EXISTS(
            SELECT 1 
            FROM comments 
            WHERE post_id = p.post_id 
              AND user_id = :current_user_id
        ) AS user_has_commented";
}

$get_all_posts .= "
    FROM posts AS p
    INNER JOIN users AS u 
        ON u.user_id = p.user_id
    INNER JOIN profiles AS pr 
        ON pr.user_id = u.user_id
    LEFT JOIN post_likes AS pl 
        ON pl.post_id = p.post_id
    LEFT JOIN comments AS c 
        ON c.post_id = p.post_id
    GROUP BY p.post_id
    ORDER BY p.post_created DESC";
?>