<?php
try {
    // Simple PDO connection
    $pdo = new PDO('mysql:host=localhost;dbname=messageboard', 'jackm', 'password3490');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected successfully!";
} catch (PDOException $e) {
    // Catch connection errors
    die("Connection failed: " . $e->getMessage());
}
?>