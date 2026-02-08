<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['assign_course'])) {
    $t_id = $_POST['t_id'];
    $c_id = $_POST['c_id'];

    $check = $conn->query("SELECT * FROM tbl_teacher_courses WHERE t_id = $t_id AND c_id = $c_id");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO tbl_teacher_courses (t_id, c_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $t_id, $c_id);
        $stmt->execute();
        $stmt->close();
        $msg = "Course assigned to teacher successfully!";
    } else {
        $msg = "This course is already assigned to this teacher.";
    }
}

// Fetch all teachers
$teachers = $conn->query("SELECT * FROM tbl_teachers ORDER BY t_first_name");

// Get selected teacher ID from request
$selected_teacher_id = isset($_GET['t_id']) ? (int)$_GET['t_id'] : null;
$assigned_courses = null;
$available_courses = null;

if ($selected_teacher_id) {
    // Fetch courses already assigned to this teacher
    $assigned_courses = $conn->query("
        SELECT c.*, d.d_name FROM tbl_courses c
        JOIN tbl_teacher_courses tc ON c.c_id = tc.c_id
        JOIN tbl_departments d ON c.d_id = d.d_id
        WHERE tc.t_id = $selected_teacher_id
        ORDER BY d.d_name, c.c_name
    ");

    // Fetch courses NOT assigned to this teacher
    $available_courses = $conn->query("
        SELECT c.*, d.d_name FROM tbl_courses c
        LEFT JOIN tbl_teacher_courses tc ON (c.c_id = tc.c_id AND tc.t_id = $selected_teacher_id)
        JOIN tbl_departments d ON c.d_id = d.d_id
        WHERE tc.tc_id IS NULL
        ORDER BY d.d_name, c.c_name
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Courses to Teachers</title>
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
        .table-container { flex: 1.5; min-width: 350px; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(0, 33, 71, 0.9); }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; color: white; }
        th { background: #C5B358; color: #002147; }
        
        .btn-assign { width: 100%; margin-top: 15px; cursor: pointer; }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; background: rgba(52, 147, 4, 0.2); border: 1px solid #349304; color: #349304; }
        .form-container label { color: black; font-weight: bold; }
        .form-container select, .form-container input { color: black; background: white; max-width: 100%; }
        .form-container select option { color: black; background: white; }
        .form-container select { max-width: 350px; }
        .btn-remove { padding: 4px 8px; font-size: 0.75rem; background: #c0392b; color: white; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1400px;">
        <h1>Assign Courses to Teachers</h1>
        <p>Select a teacher and assign courses to them.</p>

        <?php if (!empty($msg)): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="layout-flex">
            <!-- Teacher Selection -->
            <div class="form-container">
                <h3 style="color: #C5B358;">Select Teacher</h3>
                <form method="GET">
                    <label>Teacher:</label>
                    <select name="t_id" onchange="this.form.submit();" style="padding: 8px;">
                        <option value="">-- Choose Teacher --</option>
                        <?php if ($teachers->num_rows > 0): ?>
                            <?php while($t = $teachers->fetch_assoc()): ?>
                            <option value="<?php echo $t['t_id']; ?>" <?php echo ($selected_teacher_id == $t['t_id']) ? 'selected' : ''; ?>>
                                <?php echo $t['t_first_name'] . " " . $t['t_last_name'] . " (" . $t['t_department'] . ")"; ?>
                            </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </form>
            </div>

            <!-- Assign Course Form -->
            <?php if ($selected_teacher_id): ?>
            <div class="form-container">
                <h3 style="color: #C5B358;">Assign Course</h3>
                <form method="POST">
                    <input type="hidden" name="t_id" value="<?php echo $selected_teacher_id; ?>">
                    
                    <label>Available Courses:</label>
                    <select name="c_id" required style="padding: 8px;">
                        <option value="">-- Select Course --</option>
                        <?php if ($available_courses && $available_courses->num_rows > 0): ?>
                            <?php while($c = $available_courses->fetch_assoc()): ?>
                            <option value="<?php echo $c['c_id']; ?>">
                                <?php echo $c['c_name'] . " (" . $c['d_name'] . ")"; ?>
                            </option>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <option value="">No available courses</option>
                        <?php endif; ?>
                    </select>
                    
                    <button type="submit" name="assign_course" class="btn-a btn-assign">Assign Course</button>
                </form>
            </div>
            <?php endif; ?>
        </div>

        <!-- Assigned Courses -->
        <?php if ($selected_teacher_id && $assigned_courses): ?>
        <div style="margin-top: 30px;">
            <h2 style="text-align:left;">Courses Assigned to Selected Teacher (<?php echo $assigned_courses->num_rows; ?>)</h2>
            <table>
                <thead>
                    <tr>
                        <th>Course Code</th>
                        <th>Course Name</th>
                        <th>Department</th>
                        <th style="width:100px; text-align:center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($assigned_courses->num_rows > 0): ?>
                        <?php while($ac = $assigned_courses->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $ac['c_code']; ?></td>
                            <td><?php echo $ac['c_name']; ?></td>
                            <td><?php echo $ac['d_name']; ?></td>
                            <td style="text-align:center;">
                                <a href="remove_teacher_course.php?tc_id=<?php echo $ac['tc_id'] ?? ''; ?>&t_id=<?php echo $selected_teacher_id; ?>" 
                                   class="btn-remove" onclick="return confirm('Remove this course?')">Remove</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="4" style="text-align:center;">No courses assigned yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <div class="footer-actions" style="margin-top: 20px;">
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
