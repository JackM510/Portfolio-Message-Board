<?php
require_once __DIR__ . '/../config.php';
require_once(DB_INC);

$profile_id = isset($_GET['profile_id'])
        ? (int)$_GET['profile_id']
        : (int)$_SESSION['profile_id'];

// Get profile details
$columns = "u.first_name, u.last_name, u.created_at, u.date_of_birth,
            p.location, p.occupation, p.bio, p.profile_picture";
// If admin get additional details
if (isAdmin()) {
    $columns .= ", u.email, p.profile_id";
}

$sql = "SELECT $columns
        FROM users AS u
        JOIN profiles AS p ON u.user_id = p.user_id
        WHERE p.profile_id = :profile_id";

$stmt = $pdo->prepare($sql);
$stmt->bindParam(':profile_id', $profile_id, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if ($data) {
    // Assign profile data
    $first_name = $data['first_name'];
    $last_name = $data['last_name'];
    $full_name = $first_name . ' ' . $last_name;
    $joined = date('F j, Y', strtotime($data['created_at']));
    $age = date_diff(date_create($data['date_of_birth']), date_create('today'))->y;
    $profile_picture = $data['profile_picture'];
    $location = $data['location'];
    $occupation = $data['occupation'];
    $bio = $data['bio'];
    // Admin data 
    if (isAdmin()) {
        $email = $data['email'];
        $profileId = $data['profile_id'];
    }
} else {
    header("Location: " . INDEX_URL);
}
?>