<?php
session_start();
if (!isset($_SESSION['u_id'])) {
    header("Location: login.php");
    exit();
}
?>  
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>User Dashboard</h1>
        <p>Welcome, User ID: <?php echo htmlspecialchars($_SESSION['u_id']); ?></p>
        <a href="logout.php" class="btn-b">Logout</a>
    </div>
</body>
</html>
