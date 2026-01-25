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

    $stmt = $conn->prepare("INSERT INTO tbl_teachers (t_first_name, t_last_name, t_department) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $fname, $lname, $dept);
    $stmt->execute();
    $stmt->close();
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
        .table-container { flex: 2; min-width: 450px; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(0, 33, 71, 0.9); }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; color: white; }
        th { background: #C5B358; color: #002147; }
        
        .btn-add { width: 100%; margin-top: 15px; cursor: pointer; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Manage Teachers</h1>
        <p>Register new teachers into the evaluation system. <a href="admin_dash.php" style="color:#C5B358;">Return to Dashboard</a></p>

        <div class="layout-flex">
            <div class="form-container">
                <h3 style="color: #C5B358;">Add New Teacher</h3>
                <form method="POST">
                    <label>First Name:</label>
                    <input type="text" name="t_first_name" required style="width:100%;">
                    
                    <label>Last Name:</label>
                    <input type="text" name="t_last_name" required style="width:100%;">
                    
                    <label>Department:</label>
                    <input type="text" name="t_department" placeholder="e.g. Computer Studies" required style="width:100%;">
                    
                    <button type="submit" name="add_teacher" class="btn-a btn-add">Add Teacher</button>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($teachers->num_rows > 0): ?>
                            <?php while($row = $teachers->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['t_id']; ?></td>
                                <td><?php echo $row['t_first_name'] . " " . $row['t_last_name']; ?></td>
                                <td><?php echo $row['t_department']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;">No teachers found in the records.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>