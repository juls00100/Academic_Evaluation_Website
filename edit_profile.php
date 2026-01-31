<?php
include 'db.php';
session_start();
// Security: Ensure user is logged in and is a edit_profile
if (!isset($_SESSION['u_id'])) {
    header('Location: login.php');
    exit;
}
$u_id = $_SESSION['u_id'];
// Fetch current user data
$user_query = $conn->query("SELECT * FROM tbl_user WHERE u_id = '$u_id'");
$user = $user_query ? $user_query->fetch_assoc() : null;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['u_first_name'];
    $last_name = $_POST['u_last_name'];
    $email = $_POST['u_email'];
    
    $stmt = $conn->prepare("UPDATE tbl_user SET u_first_name = ?, u_last_name = ?, u_email = ? WHERE u_id = ?");
    $stmt->bind_param("sssi", $first_name, $last_name, $email, $u_id);
    
    if ($stmt->execute()) {
        $_SESSION['u_first_name'] = $first_name; // Update session variable
        header("Location: dashboard.php?msg=profile_updated");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container { 
            max-width: 500px; 
            margin: auto; 
            background: rgba(255, 255, 255, 0.05); 
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid #C5B358;
        }
        .form-container h2 { text-align: center; color: #C5B358; }
        .form-container label { display: block; margin-top: 15px; color: white; }
        .form-container input[type="text"], 
        .form-container input[type="email"] {
            width: 100%; 
            padding: 10px; 
            margin-top: 8px; 
            border: 1px solid #ccc; 
            border-radius: 4px;
        }
        .form-container .btn-save {
            width: 100%; 
            margin-top: 20px; 
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 600px;">
        <div class="form-container">
            <h2>Edit Profile</h2>
            <form method="POST" action="">
                <label for="u_first_name">First Name:</label>
                <input type="text" id="u_first_name" name="u_first_name" value="<?php echo htmlspecialchars($user['u_first_name']); ?>" required>
                
                <label for="u_last_name">Last Name:</label>
                <input type="text" id="u_last_name" name="u_last_name" value="<?php echo htmlspecialchars($user['u_last_name']); ?>" required>
                
                <label for="u_email">Email:</label>
                <input type="email" id="u_email" name="u_email" value="<?php echo htmlspecialchars($user['u_email']); ?>" required>
                
                <button type="submit" class="btn-save">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>