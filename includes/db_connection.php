<?php
try {
    // Simple PDO connection
    $pdo = new PDO('mysql:host=localhost;dbname=messageboard', 'jackm', 'Zr8!vLx@29eQ#fT1');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Catch connection errors
    die("Connection failed: " . $e->getMessage());
}
?>
