<?php
require_once('../includes/db_connection.php');
session_start();

if (!isset($_POST['profile_id'])) {
  echo json_encode(['success' => false, 'error' => 'Missing profile ID']);
  exit;
}

$profileId = (int) $_POST['profile_id'];

$sql = "SELECT u.first_name, u.last_name, u.email, u.role, u.created_at, p.profile_id, p.profile_picture 
        FROM users u
        INNER JOIN profiles p ON u.user_id = p.user_id
        WHERE p.profile_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$profileId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($user) {
  echo json_encode(['success' => true, 'user' => $user]);
} else {
  echo json_encode(['success' => false, 'error' => 'User not found']);
}
