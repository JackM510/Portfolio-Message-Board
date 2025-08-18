<?php
require_once('../includes/db_connection.php');
session_start();

if (!isset($_POST['user_id'])) {
  echo json_encode(['success' => false, 'error' => 'Missing user ID']);
  exit;
}

$userId = (int) $_POST['user_id'];

$sql = "
  SELECT
    u.user_id,
    u.first_name,
    u.last_name,
    u.email,
    u.role,
    u.created_at,
    p.profile_id,
    CASE WHEN p.profile_id IS NULL THEN 0 ELSE 1 END AS has_profile
  FROM users AS u
  LEFT JOIN profiles AS p
    ON p.user_id = u.user_id
  WHERE u.user_id = :uid
  LIMIT 1
";

$stmt = $pdo->prepare($sql);
$stmt->execute([':uid' => $userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);


if ($user) {
  echo json_encode(['success' => true, 'user' => $user]);
} else {
  echo json_encode(['success' => false, 'error' => 'User not found']);
}
