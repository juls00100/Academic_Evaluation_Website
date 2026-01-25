<?php
include 'db.php';
session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['u_username'];
    $password = $_POST['u_password'];

$stmt = $conn->prepare("SELECT u_id, u_password, u_type, u_status, u_first_name FROM tbl_user WHERE u_email = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $stmt->bind_result($id, $hashed_password, $type, $status, $fname); 
$stmt->fetch();

if (password_verify($password, $hashed_password)) {
    if ($status == 'Pending') {
        echo "Your account is still pending approval.";
    } else {
        $_SESSION['u_id'] = $id;
        $_SESSION['u_type'] = $type;
        $_SESSION['u_first_name'] = $fname;  
            switch ($type) {
                case 'Admin':
                    header("Location: admin_dash.php");
                    break;
                case 'Teacher':
                    header("Location: teacher_dash.php");
                    break;
                case 'Student':
                    header("Location: student_dash.php");
                    break;
                default:
                    header("Location: dashboard.php");
                    break;
            }
            exit();
        }

    } else {
        echo "Invalid password.";
    }
}

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container">
        <h1>Login</h1>
        <form method="POST" action="login.php">
            <label for="u_username">Username:</label>
            <input type="text" id="u_username" name="u_username" required>
            
            <label for="u_password">Password:</label>
            <input type="password" id="u_password" name="u_password" required>
            
            <br>
            
            <button type="submit" class="btn-a">Login</button>
        </form>
        <a style="color:white;" href="index.php">Back to Main Menu</a>
    </div> 
</body>
</html>
