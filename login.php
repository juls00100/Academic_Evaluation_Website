<?php
include 'db.php';
session_start();

$error = ""; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['u_email'];
    $password = $_POST['u_password'];

    $stmt = $conn->prepare("SELECT u_id, u_password, u_type, u_status, u_first_name FROM tbl_user WHERE u_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        if (password_verify($password, $user['u_password'])) {
            if ($user['u_status'] === 'Pending') {
                $error = "⏳ Your account is awaiting Admin approval. Please check back later!";
            } else {
                $_SESSION['u_id'] = $user['u_id'];
                $_SESSION['u_type'] = $user['u_type'];
                $_SESSION['u_first_name'] = $user['u_first_name'];
                
                // Redirect based on type
                header("Location: " . strtolower($user['u_type']) . "_dash.php");
                exit();
            }
        } else {
            $error = "❌ Invalid password. Please try again.";
        }
    } else {
        $error = "❌ No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="max-width: 400px;">
        <h1>Login</h1>
        
        <?php if($error): ?>
            <div style="background: rgba(255, 0, 0, 0.2); border: 1px solid #ff4d4d; padding: 10px; border-radius: 8px; margin-bottom: 20px; color: #ffcccc; font-size: 0.9em;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="input-group">
                <label>Email</label>
                <input type="email" name="u_email" required>
            </div>
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="u_password" required>
            </div>
            <button type="submit" class="btn-a" style="width:100%; margin-top:20px; border:none;">Login</button>
        </form>
        <p></p> <a href="register.php" class="btn-a btn-register">Don't have an account? Register Now</a></p>
    </div>
</body>
</html>