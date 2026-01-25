<?php
include 'db.php';
session_start();
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Student') {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Welcome to the Student Dashboard</h1>
        <p>You are successfully logged in as a student.</p>
        <a style="color:white;" href="logout.php">Logout</a>
    </div>
</body>
</html>
