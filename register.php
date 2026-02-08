<?php
include 'db.php';
$msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fname = $_POST['u_first_name'];
    $lname = $_POST['u_last_name'];
    $email = $_POST['u_email'];
    $pass = password_hash($_POST['u_password'], PASSWORD_DEFAULT);
    $type = $_POST['u_type'];

    // Check if email exists
    $check = $conn->prepare("SELECT u_id FROM tbl_user WHERE u_email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        $msg = "❌ This email is already registered.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tbl_user (u_first_name, u_last_name, u_email, u_password, u_type, u_status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("sssss", $fname, $lname, $email, $pass, $type);
        
        if ($stmt->execute()) {
            // Send them to login with a success message
            header("Location: login.php?registered=true");
            exit();
        } else {
            $msg = "❌ Registration failed. System error.";
        }
    }
}
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
    <div class="container registration-wide">
    <header style="margin-bottom: 30px;">
        <h1>Create Your Account</h1>
        <p style="color: #C5B358;">Join the Academic Evaluation System</p>
    </header>

  

    <form method="POST" action="register.php" class="reg-form">
        <div class="form-row">
            <div class="input-group">
                <label>First Name</label>
                <input type="text" name="u_first_name" placeholder="Julios" required>
            </div>
            <div class="input-group">
                <label>Last Name</label>
                <input type="text" name="u_last_name" placeholder="Palautog" required>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="u_email" placeholder="jejemon@example.com" required>
            </div>
            <div class="input-group">
                <label>I am a...</label>
                <select name="u_type" id="u_type" onchange="toggleStudentFields()">
                    <option style="color: #000000;" value="Student">Student</option>
                    <option style="color: #000000;"value="Teacher">Teacher</option>
                    <option style="color: #000000;"value="Admin">Administrator</option>
                </select>
            </div>
        </div>

        <div class="form-row">
            <div id="student-fields" class="input-group">
                <label>School ID</label>
                <input type="text" name="u_schoolID" placeholder="ID Number">
            </div>
            <div class="input-group" style="position: relative;">
                <label>Password</label>
                <input type="password" id="u_password" name="u_password" required>
                <span id="togglePassword" style="position: absolute; right: 15px; top: 45px; cursor: pointer; '🕶️' : '👓'"></span>
            </div>
        </div>

        <div class="button-container">
            <button type="submit" class="btn-a">Complete Registration</button>
            
        </div>
    </form>
    <div class="footer-actions">   
            <a href="login.php" class="back-btn">← Back to Menu</a>
        </div>
</div>

<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#u_password');

togglePassword.addEventListener('click', function (e) {
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    this.textContent = type === 'password' ? '🕶️' : '👓';
});
</script>
</body>
</html>
     