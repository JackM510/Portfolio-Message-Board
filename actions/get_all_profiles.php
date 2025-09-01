<?php
    if (isLoggedIn() === false && !isset($_SESSION['role'])) {
        header("Location: ".LOGIN_URL);
    }
    // Retrieve all users from mysql
    $sql = "SELECT
        u.user_id,
        u.email,
        u.role,
        p.profile_id
        FROM users AS u
        LEFT JOIN profiles AS p
        ON p.user_id = u.user_id;";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>