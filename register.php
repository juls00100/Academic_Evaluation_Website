<?php
include 'db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['u_email'];
    $password = password_hash($_POST['u_password'], PASSWORD_BCRYPT);

    $type = "Student";
    $status = "Pending";

    $stmt = $conn->prepare("INSERT INTO tbl_user (u_email, u_password, u_type, u_status) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $password, $type, $status);

    if ($stmt->execute()) {
        echo "Registration successful! Please for the Admin to approved."." <a href='index.php'>Go to Main Menu</a>";
    } else {
        echo "Error: " . $stmt->error;

    $stmt->close();
    $conn->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Register</h1>
            <div class="register">

        <form method="POST" action="register.php">
            <label for="u_email" > Email:</label>
            <input type="email" id="u_email" name="u_email" required>
            
            <label for="u_password" >Password:</label>
            <input type="password" id="u_password" name="u_password" required>
            <br><button type="submit" class="btn-a">Register</button>
        </form>
        <a style="color:white;" href="index.php">Back to Main Menu</a>
            </div>
    </div>
</body>
</html>
