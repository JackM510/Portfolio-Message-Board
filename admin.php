<?php
    session_start();
    require_once('utilities.php');
    if (isLoggedIn() === false && !isset($_SESSION['role'])) {
        header("Location: login.php");
    }

    require_once('includes/db_connection.php');
    

    // Delete a user
    if (isset($_POST['delete_user_id'])) {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = :user_id");
            $stmt->bindParam(':user_id', $_POST['delete_user_id'], PDO::PARAM_INT);
            $stmt->execute();
    
            echo "User and associated data deleted successfully.";
        } catch (PDOException $e) {
            echo "Error deleting user: " . $e->getMessage();
        }
    }
    


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php require_once "head.php"; ?>
    <link href="css/index.css" rel="stylesheet">
    <title>Admin Panel</title>
</head>
<body>
    <!-- Navbar -->
    <?php require_once "nav.php"; ?>
    <div class="mt-5 text-center">
        <h1 class="display-5">Admin Control Panel</h1>
    </div>

    <section class="container d-flex justify-content-center w-50 mx-auto mt-5">
    
    <?php
    // Retrieve all users from mysql
    $sql = "SELECT user_id, first_name, last_name, email, role FROM users";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Create a table to store the data
    echo '<table class="table" border="1">
        <tr class="table-secondary">
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
        </tr>';

    // Loop through each user and add as table data
    foreach ($users as $user) {
        echo "<tr>
                <td>{$user['first_name']}</td>
                <td>{$user['last_name']}</td>
                <td>{$user['email']}</td>
                <td>{$user['role']}</td>
                <td>
                    <form method='POST' style='display:inline;'>
                        <input type='hidden' name='delete_user_id' value='{$user['user_id']}'>
                        <button type='submit' onclick='return confirm(\"Are you sure you want to delete this user?\");'>Delete</button>
                    </form>
                </td>
            </tr>";
    }
    
    echo "</table>";

    ?>
    <section>

</body>
</html>