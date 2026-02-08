<?php
include 'db.php';
session_start();

// Security: Only Admin can manage teachers
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Handle Form Submission (Add Teacher)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_teacher'])) {
    $fname = $_POST['t_first_name'];
    $lname = $_POST['t_last_name'];
    $dept = $_POST['t_department'];
    $email = $_POST['t_email'];
    $password = password_hash($_POST['t_password'], PASSWORD_DEFAULT);
    
    // Check if email already exists
    $check = $conn->query("SELECT * FROM tbl_user WHERE u_email = '$email'");
    if ($check->num_rows > 0) {
        echo "<script>alert('Email already exists!'); window.location='manage_teachers.php';</script>";
        exit;
    }
    
    // Create user account first
    $stmt = $conn->prepare("INSERT INTO tbl_user (u_first_name, u_last_name, u_email, u_password, u_type, u_status) VALUES (?, ?, ?, ?, ?, ?)");
    $u_type = 'Teacher';
    $u_status = 'Active';
    $stmt->bind_param("ssssss", $fname, $lname, $email, $password, $u_type, $u_status);
    $stmt->execute();
    $user_id = $stmt->insert_id;
    $stmt->close();
    
    // Create teacher profile linked to user account
    $stmt = $conn->prepare("INSERT INTO tbl_teachers (t_first_name, t_last_name, t_department, t_user_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $fname, $lname, $dept, $user_id);
    $stmt->execute();
    $stmt->close();
    
    echo "<script>alert('Teacher account created successfully! Email: $email'); window.location='manage_teachers.php';</script>";
}

// Fetch Current Teachers
$teachers = $conn->query("SELECT * FROM tbl_teachers ORDER BY t_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Teachers</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .layout-flex {
            display: flex;
            gap: 30px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .form-container { 
            flex: 1; 
            min-width: 300px; 
            background: rgba(255, 255, 255, 0.05); 
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid #C5B358;
            text-align: left;
        }
        .form-container label { color: black; font-weight: bold; }
        .form-container input { color: black; background: white; }
        .table-container { flex: 2; min-width: 450px; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(0, 33, 71, 0.9); }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; color: white; }
        th { background: #C5B358; color: #002147; }
        
        .btn-add { width: 100%; margin-top: 15px; cursor: pointer; }
        .action-btns { display: flex; gap: 6px; justify-content: center; }
        .btn-small { padding: 5px 10px; font-size: 0.85rem; border-radius: 4px; text-decoration: none; display: inline-block; text-align: center; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #c0392b; color: white; }
        .message { padding: 12px; margin-bottom: 15px; border-radius: 4px; }
        .message.success { background: rgba(52, 147, 4, 0.2); border: 1px solid #349304; color: #349304; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Manage Teachers</h1>
        <p>Register new teachers into the evaluation system. </p>

        <?php if (isset($_GET['msg'])): ?>
            <div class="message success" style="margin-bottom: 20px;">
                <?php 
                if ($_GET['msg'] === 'updated') echo "Teacher updated successfully!";
                elseif ($_GET['msg'] === 'deleted') echo "Teacher deleted successfully!";
                ?>
            </div>
        <?php endif; ?>

        <div class="layout-flex">
            <div class="form-container">
                <h3 style="color: #C5B358;">Add New Teacher</h3>
                <form method="POST">
                    <label style="color: white;">First Name:</label>
                    <input type="text" name="t_first_name" required style="width:100%; padding: 8px; margin-bottom: 10px;">
                    
                    <label style="color: white;">Last Name:</label>
                    <input type="text" name="t_last_name" required style="width:100%; padding: 8px; margin-bottom: 10px;">
                    
                    <label style="color: white;">Department:</label>
                    <input type="text" name="t_department" placeholder="e.g. Computer Studies" required style="width:100%; padding: 8px; margin-bottom: 10px;">
                    
                    <label style="color: white;">Email (for login):</label>
                    <input type="email" name="t_email" required style="width:100%; padding: 8px; margin-bottom: 10px;">
                    
                    <label style="color: white;">Password (for login):</label>
                    <input type="password" name="t_password" required style="width:100%; padding: 8px; margin-bottom: 10px;">
                    
                    <button type="submit" name="add_teacher" class="btn-a btn-add">Create Teacher & Account</button>
                </form>
            </div>

            <div class="table-container">
                <h3 style="text-align: left;">Registered Teachers (<?php echo $teachers->num_rows; ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Full Name</th>
                            <th>Department</th>
                            <th style="width: 150px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($teachers->num_rows > 0): ?>
                            <?php while($row = $teachers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['t_id']; ?></td>
                                <td><?php echo $row['t_first_name'] . " " . $row['t_last_name']; ?></td>
                                <td><?php echo $row['t_department']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_teacher.php?id=<?php echo $row['t_id']; ?>" class="btn-small btn-edit">Edit</a>
                                        <a href="delete_teacher.php?id=<?php echo $row['t_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Delete this teacher?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" style="text-align:center;">No teachers found in the records.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
               
            </div>
        </div>
        <div class="footer-actions">
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>