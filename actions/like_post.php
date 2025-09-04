<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);
require_once(UTIL_INC);
header('Content-Type: application/json');

// Make sure the user is logged in
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'unauthorized' => true]);
    exit;
} else if (!isset($_POST['post_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$userId = (int) $_SESSION['user_id'];
$postId = (int) $_POST['post_id'];
// Check if already liked
$checkStmt = $pdo->prepare("SELECT 1 FROM post_likes WHERE post_id = ? AND user_id = ?");
$checkStmt->execute([$postId, $userId]);
$alreadyLiked = $checkStmt->fetchColumn();

// Remove like if already liked
if ($alreadyLiked) {
    $removeStmt = $pdo->prepare("DELETE FROM post_likes WHERE post_id = ? AND user_id = ?");
    $removeStmt->execute([$postId, $userId]);
    $liked = false;
} 
// Add like if not already liked
else { 
    $addStmt = $pdo->prepare("INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)");
    $addStmt->execute([$postId, $userId]);
    $liked = true;
}

// Get post updated like count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM post_likes WHERE post_id = ?");
$countStmt->execute([$postId]);
$likeCount = $countStmt->fetchColumn();
echo json_encode([
    'success' => true,
    'liked' => $liked,
    'like_count' => $likeCount
]);