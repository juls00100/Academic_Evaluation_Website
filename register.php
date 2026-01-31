<?php
include 'db.php';
$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $schoolID = $_POST['u_schoolID'] ?? NULL;
    $fname = $_POST['u_first_name'];
    $lname = $_POST['u_last_name'];
    $email = $_POST['u_email'];
    $password = password_hash($_POST['u_password'], PASSWORD_BCRYPT);
    $type = $_POST['u_type'];
    $status = ($type == 'Admin') ? 'Approved' : 'Pending'; 

    $stmt = $conn->prepare("INSERT INTO tbl_user (u_schoolID, u_first_name, u_last_name, u_email, u_password, u_type, u_status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    // Ang "sssssss" nagpasabot nga 7 ka string variables ang imong gi-bind
    $stmt->bind_param("sssssss", $schoolID, $fname, $lname, $email, $password, $type, $status);

    if ($stmt->execute()) {
        $msg = "<div class='message success'>Registration successful! Please wait for Admin approval.</div>";
    } else {
        $msg = "<div class='message error'>Error: " . $conn->error . "</div>";
    }
    $stmt->close();
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

    <?php echo $msg; ?>

    <div id="loader-container" style="display:none; text-align:center; padding: 20px;">
        <div class="loader"></div>
        <p>Processing your registration...</p>
    </div>

    <div class="register" id="register-form">
        <form method="POST" action="register.php" onsubmit="showLoader()">
    <label for="u_type">User Type:</label>
    <select id="u_type" name="u_type" required onchange="toggleStudentFields()">
        <option value="Student">Student</option>
        <option value="Teacher">Teacher</option>
        <option value="Admin">Admin</option>
    </select>

    <div id="student-fields">
        <label for="u_schoolID">School ID:</label>
        <input type="text" id="u_schoolID" name="u_schoolID">

        
    </div>

    <label for="u_first_name">First Name:</label>
    <input type="text" id="u_first_name" name="u_first_name" required>

    <label for="u_last_name">Last Name:</label>
    <input type="text" id="u_last_name" name="u_last_name" required>

    <label for="u_email">Email:</label>
    <input type="email" id="u_email" name="u_email" required>
    
   <label for="u_password">Password:</label>
        <div style="position: relative; width: 100%;">
            <input type="password" id="u_password" name="u_password" required style="width: 100%; padding-right: 40px;">
            <span id="togglePassword" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
                👁️
            </span>
        </div>
    <br><button type="submit" class="btn-a">Register</button>
    <br><button type="button" class="btn-a" onclick="window.location.href='index.php'">Back to Main Menu</button>
</form>

<script>
function toggleStudentFields() {
    var type = document.getElementById("u_type").value;
    var studentFields = document.getElementById("student-fields");
    
    // Kon ang pinili kay "Student", ipakita ang fields. Kon dili, itago.
    if (type === "Student") {
        studentFields.style.display = "block";
    } else {
        studentFields.style.display = "none";
    }
}
</script>
<script>
const togglePassword = document.querySelector('#togglePassword');
const password = document.querySelector('#u_password');

togglePassword.addEventListener('click', function (e) {
    // I-toggle ang type attribute
    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
    password.setAttribute('type', type);
    
    // I-toggle ang icon (mata nga abli ug mata nga naay slash/piyong)
    this.textContent = type === 'password' ? '🕶️' : '👓';
});
</script>
</body>
</html>
