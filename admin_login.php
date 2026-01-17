<?php
include 'db.php';
session_start();
?>

<!DOCTYPE html> 
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="styles.css">
    </head>
<body>
    <div class="container">
        <h1>Administrator's Portal</h1>
        <form method="POST" action="admin_login.php">
            <label for="u_username">Username:</label>
            <input type="text" id="u_username" name="u_username" required>
            <label for="u_password">Password:</label>
            <input type="password" id="u_password" name="u_password" required>
            <br><button type="submit" class="btn-a">Secure Login</button>
        </form>
        <a style="color:white;" href="index.php">Back to Main Menu</a>
    </div> 
</body>
</html>
