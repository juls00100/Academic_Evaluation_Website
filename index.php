<?php
include 'db.php';
session_start();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Main Menu</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header style="margin-bottom: 50px;">
            <h1 style="font-size: 2.5rem; line-height: 1.2;">
                Welcome to Academic Evaluation System:<br>
                <span style="font-weight: 300; opacity: 0.8;">Elevating Education.</span>
            </h1>
        </header>

        <div class="features-grid" style="display: flex; justify-content: space-around; margin-bottom: 50px;">
            <div class="feature">
                <h3 style="color: #fff;">Students</h3>
                <p style="font-size: 0.9rem; opacity: 0.7;">Track Progress<br>Submit Feedback</p>
            </div>
            <div class="feature">
                <h3 style="color: #fff;">Teachers</h3>
                <p style="font-size: 0.9rem; opacity: 0.7;">Evaluate Students<br>Access Analytics</p>
            </div>
            <div class="feature">
                <h3 style="color: #fff;">Admins</h3>
                <p style="font-size: 0.9rem; opacity: 0.7;">Manage Data<br>System Oversight</p>
            </div>
        </div>

        <div class="button-group">
            <a href="login.php" class="btn-a btn-login">Login</a>
            <a href="register.php" class="btn-a btn-register">Register Now</a>
        </div>
    </div>
</body>
</html>
     
