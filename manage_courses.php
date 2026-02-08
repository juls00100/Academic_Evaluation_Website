<?php
include 'db.php';
session_start();

// Security: Only Admin can manage courses
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Handle Form Submission (Add Course)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $name = $_POST['c_name'];
    $code = $_POST['c_code'];
    $dept_id = $_POST['d_id'];

    $stmt = $conn->prepare("INSERT INTO tbl_courses (c_name, c_code, d_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $code, $dept_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch Departments
$departments = $conn->query("SELECT * FROM tbl_departments ORDER BY d_name");

// Fetch Current Courses with Department Info
$courses = $conn->query("SELECT c.*, d.d_name FROM tbl_courses c LEFT JOIN tbl_departments d ON c.d_id = d.d_id ORDER BY d.d_name, c.c_id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Courses</title>
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
        .form-container label { color: white; font-weight: bold; }
        .form-container select option { color: black; background: white; }
        .form-container input { color: black; background: white; }
        .table-container { flex: 2; min-width: 450px; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(0, 33, 71, 0.9); }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; color: white; }
        th { background: #C5B358; color: #002147; }
        
        .action-btns { display: flex; gap: 6px; justify-content: center; }
        .btn-small { padding: 5px 10px; font-size: 0.85rem; border-radius: 4px; text-decoration: none; display: inline-block; text-align: center; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #c0392b; color: white; }
        .btn-add { width: 100%; margin-top: 15px; cursor: pointer; box-sizing: border-box; }
        .btn-dept { width: 100%; margin-top: 10px; padding: 12px 20px; background: #8e44ad; color: white; border: none; border-radius: 8px; text-decoration: none; text-align: center; font-weight: bold; cursor: pointer; display: block; transition: 0.3s; box-sizing: border-box; }
        .btn-dept:hover { background: #a855c7; transform: translateY(-2px); }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Manage Courses</h1>
        <p>Register new courses into the evaluation system.</p>

        <div class="layout-flex">
            <div class="form-container">
                <h3 style="color: #C5B358;">Add New Course</h3>
                <form method="POST">
                    <label>Department:</label>
                    <select name="d_id" required style="width:100%; padding: 8px;" >
                        <option style="color: black;" value="">-- Select Department --</option>
                        <?php 
                        $dept_result = $conn->query("SELECT * FROM tbl_departments ORDER BY d_name");
                        if ($dept_result && $dept_result->num_rows > 0):
                            while($d = $dept_result->fetch_assoc()): ?>
                            <option value="<?php echo $d['d_id']; ?>"><?php echo $d['d_name']; ?></option>
                        <?php endwhile;
                        else: ?>
                            <option value="">No departments available. <a href="manage_departments.php">Create one</a></option>
                        <?php endif; ?>
                    </select>
                    
                    <label>Course Name:</label>
                    <input type="text" name="c_name" required style="width:100%;" color="#000000">
                    
                    <label>Course Code:</label>
                    <input type="text" name="c_code" required style="width:100%;" color="#000000">
                    
                    <button type="submit" name="add_course" class="btn-a btn-add">Add Course</button>
                    <a href="manage_departments.php" class="btn-dept">Manage Departments</a>
                </form>
            </div>

            <div class="table-container">
                <h3 style="text-align: left;">Registered Courses (<?php echo $courses->num_rows; ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Course Name</th>
                            <th>Course Code</th>
                            <th>Department</th>
                            <th style="width: 130px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($courses->num_rows > 0): ?>
                            <?php 
                            $courses->data_seek(0);
                            $current_dept = '';
                            while($row = $courses->fetch_assoc()): 
                                $dept = isset($row['d_name']) ? $row['d_name'] : 'Unassigned';
                                if ($current_dept !== $dept):
                                    $current_dept = $dept;
                                    if ($current_dept !== (isset($row['d_name']) ? $row['d_name'] : 'Unassigned')):
                                        echo '<tr style="background: rgba(197, 179, 88, 0.1);"><td colspan="4" 
                                        style="font-weight: bold; color: #C5B358;">' . htmlspecialchars($current_dept) . '</td></tr>';
                                    endif;
                                endif;
                            ?>
                            <tr>
                                <td><?php echo $row['c_id']; ?></td>
                                <td><?php echo $row['c_name']; ?></td>
                                <td><?php echo $row['c_code']; ?></td>
                                <td><?php echo htmlspecialchars($dept); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_course.php?id=<?php echo $row['c_id']; ?>" class="btn-small btn-edit">Edit</a>
                                        <a href="delete_course.php?id=<?php echo $row['c_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Delete this course?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" style="text-align:center;">No courses found. Create a department first, then add courses.</td></tr>
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