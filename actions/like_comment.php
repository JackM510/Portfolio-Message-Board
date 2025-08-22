<?php
require_once __DIR__ . '/../config.php';
session_start();
require_once(DB_INC);

header('Content-Type: application/json');

// Make sure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'unauthorized' => true]);
    exit;
} else if (!isset($_POST['comment_id'])) {
    echo json_encode(['success' => false]);
    exit;
}

$userId = $_SESSION['user_id'];
$commentId = (int) $_POST['comment_id'];

// Check if already liked
$checkStmt = $pdo->prepare("SELECT 1 FROM comment_likes WHERE comment_id = ? AND user_id = ?");
$checkStmt->execute([$commentId, $userId]);
$alreadyLiked = $checkStmt->fetchColumn();

if ($alreadyLiked) {
    // Remove like
    $removeStmt = $pdo->prepare("DELETE FROM comment_likes WHERE comment_id = ? AND user_id = ?");
    $removeStmt->execute([$commentId, $userId]);
    $liked = false;
} else {
    // Add like
    $addStmt = $pdo->prepare("INSERT INTO comment_likes (comment_id, user_id) VALUES (?, ?)");
    $addStmt->execute([$commentId, $userId]);
    $liked = true;
}

// Get updated count
$countStmt = $pdo->prepare("SELECT COUNT(*) FROM comment_likes WHERE comment_id = ?");
$countStmt->execute([$commentId]);
$likeCount = $countStmt->fetchColumn();

echo json_encode([
    'success' => true,
    'liked' => $liked,
    'like_count' => $likeCount
]);
