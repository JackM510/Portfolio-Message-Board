<?php
session_start();
require_once('../includes/db_connection.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id']) && !empty($_POST['delete_checkbox_1']) && !empty($_POST['delete_checkbox_2'])) {

    // Check that the email doesn't already exist in the DB
    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :uid");
    $stmt->bindParam(':uid', $_SESSION['user_id'], PDO::PARAM_INT);
    $stmt->execute();

    header("Location: ../logout.php");
}
?>