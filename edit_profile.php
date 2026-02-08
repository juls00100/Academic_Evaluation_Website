<?php
include 'db.php';
session_start();

// Security: Ensure user is logged in
if (!isset($_SESSION['u_id'])) {
    header('Location: login.php');
    exit;
}

$u_id = $_SESSION['u_id'];
$u_type = $_SESSION['u_type']; // Get user type for redirection

// Fetch current user data using Prepared Statement for security
$stmt = $conn->prepare("SELECT * FROM tbl_user WHERE u_id = ?");
$stmt->bind_param("i", $u_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['u_first_name'];
    $last_name = $_POST['u_last_name'];
    $email = $_POST['u_email'];
    
    $update_stmt = $conn->prepare("UPDATE tbl_user SET u_first_name = ?, u_last_name = ?, u_email = ? WHERE u_id = ?");
    $update_stmt->bind_param("sssi", $first_name, $last_name, $email, $u_id);
    
    if ($update_stmt->execute()) {
        $_SESSION['u_first_name'] = $first_name; // Update session
        
        // Redirect based on user type
        $redirect = ($u_type === 'Teacher') ? 'teacher_dash.php' : 'student_dash.php';
        header("Location: $redirect?msg=profile_updated");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile | Evaluation System</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Theme-specific form styling */
        .profile-form {
            width: 100%;
            max-width: 450px;
            margin: 0 auto;
        }
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        .input-group label {
            display: block;
            color: #C5B358; /* Gold color from your theme */
            margin-bottom: 8px;
            font-size: 0.9rem;
            font-weight: bold;
        }
        .input-group input {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            box-sizing: border-box;
            outline: none;
            transition: 0.3s;
        }
        .input-group input:focus {
            border-color: #C5B358;
            background: rgba(255, 255, 255, 0.15);
        }
        .save-btn {
            width: 100%;
            padding: 12px;
            background: #C5B358;
            color: #002147;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 10px;
        }
        .save-btn:hover {
            background: #E6D67E;
            transform: scale(1.02);
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Profile</h1>
        <p style="color: #aaa; margin-bottom: 30px;">Update your personal information below.</p>

        <form method="POST" class="profile-form">
            <div class="input-group">
                <label>First Name</label>
                <input type="text" name="u_first_name" value="<?php echo htmlspecialchars($user['u_first_name']); ?>" required>
            </div>
            
            <div class="input-group">
                <label>Last Name</label>
                <input type="text" name="u_last_name" value="<?php echo htmlspecialchars($user['u_last_name']); ?>" required>
            </div>
            
            <div class="input-group">
                <label>Email Address</label>
                <input type="email" name="u_email" value="<?php echo htmlspecialchars($user['u_email']); ?>" required>
            </div>
            
            <button type="submit" class="save-btn">Save Changes</button>
        </form>

        <div style="margin-top: 25px;">
            <div style="margin-top: 25px;">
                <?php 
                    // Determine the correct dashboard to return to
                    if ($u_type === 'Teacher') {
                        $back_link = 'teacher_dash.php';
                    } elseif ($u_type === 'Student') {
                        $back_link = 'student_dash.php';
                    } else {
                        $back_link = 'admin_dash.php';
                    }
                ?>
                <a href="<?php echo $back_link; ?>" class="back-btn">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>