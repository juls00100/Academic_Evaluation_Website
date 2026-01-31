<?php
// If the name is empty, just say "Admin", otherwise show the name
$displayName = !empty($user['u_first_name']) ? $user['u_first_name'] : "Administrator";
include 'db.php';
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
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['u_first_name'] ?? 'User'); ?>!</h1>
    <p>You are logged in as a <?php echo $_SESSION['u_type']; ?>.</p>
        <a href="teacher_dash.php" class="back-btn">← Back to Teacher Dashboard</a>
    </div>
</div>
</body>
</html>
