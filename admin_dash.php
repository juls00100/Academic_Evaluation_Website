<?php
include 'db.php';
session_start();


if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}
// Update your existing query to include an ORDER BY clause
$pending_users_list = $conn->query("SELECT * FROM tbl_user WHERE u_status = 'Pending' ORDER BY u_type ASC");
$pending_students = $conn->query("SELECT COUNT(*) as count FROM tbl_user WHERE u_status = 'Pending' AND u_type = 'Student'")->fetch_assoc()['count'];
$pending_teachers = $conn->query("SELECT COUNT(*) as count FROM tbl_user WHERE u_status = 'Pending' AND u_type = 'Teacher'")->fetch_assoc()['count'];
$pending_students_list = $conn->query("SELECT * FROM tbl_user WHERE u_status = 'Pending' AND u_type = 'Student'")->num_rows;
$pending_teachers_list = $conn->query("SELECT * FROM tbl_user WHERE u_status = 'Pending' AND u_type = 'Teacher'")->num_rows;
$pending_students_list = $conn->query("SELECT * FROM tbl_user WHERE u_status = 'Pending' AND u_type = 'Student' ORDER BY u_id DESC");
$pending_teachers_list = $conn->query("SELECT * FROM tbl_user WHERE u_status = 'Pending' AND u_type = 'Teacher' ORDER BY u_id DESC");
$pending_students_list_count = $pending_students_list->num_rows;
$pending_teachers_list_count = $pending_teachers_list->num_rows;    
$pending_students_list_count_total = $pending_students_list_count + $pending_teachers_list_count;


$teacher_query = $conn->query("SELECT COUNT(*) as count FROM tbl_teachers");
$teacher_count = $teacher_query->fetch_assoc()['count'];
$course_query = $conn->query("SELECT COUNT(*) as count FROM tbl_courses");
$course_count = $course_query->fetch_assoc()['count'];
$questions_query = $conn->query("SELECT COUNT(*) as count FROM tbl_questions");
$questions_count = $questions_query->fetch_assoc()['count'];
$student_count_query = $conn->query("SELECT COUNT(*) as count FROM tbl_user WHERE u_status = 'Pending'");
$pending_count = $student_count_query->fetch_assoc()['count'];
$pending_users_list = $conn->query("SELECT * FROM tbl_user WHERE u_status = 'Pending'");
$students_count_query = $conn->query("SELECT COUNT(*) as count FROM tbl_user WHERE u_type = 'Student'");
$students_count = $students_count_query->fetch_assoc()['count'];
$evaluations_query = $conn->query("SELECT COUNT(*) as count FROM tbl_evaluations");
$evaluations_count = $evaluations_query->fetch_assoc()['count'];


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Specific Styles for the Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            border-left: 5px solid #C5B358;
            text-align: left;
        }
        .stat-card h3 { color: #C5B358; margin: 0; }
        .stat-card p { font-size: 0.9em; margin: 5px 0; }
        
        .user-table { width: 100%; border-collapse: collapse; background: #002147; }
        .user-table th, .user-table td { padding: 12px; border: 1px solid #444; text-align: left; }
        .user-table th { background: #C5B358; color: #002147; }
    </style>
</head>
<body>
    <div class="container" style="width: 100%; max-width: 1100px;">
        <h1>Admin Dashboard</h1>
        <h2 style="text-align:left;">System Overview</h2>
        <div class="stats-grid">
            <a href="manage_teachers.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Teachers</h3>
                <p><?php echo $teacher_count; ?> Registered</p>
            </div>
            </a>
            <a href="manage_courses.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Courses</h3>
                <p><?php echo $course_count; ?> Courses</p>
            </div>
            </a>
            <a href="manage_departments.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Departments</h3>
                <p>Organize Courses</p>
            </div>
            </a>
            <a href="assign_teacher_courses.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Assign Courses</h3>
                <p>Manage Teacher Courses</p>
            </div>
            </a>
            <a href="questions.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Questions</h3>
                <p><?php echo $questions_count; ?> Questions</p>
            </div>
            </a>
            <a href="manage_students.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Students</h3>
                <p><?php echo $students_count; ?> Total Student/s</p>
            </div>
            </a>
            <a href="view_evaluations.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Evaluations</h3>
                <p><?php echo $evaluations_count; ?> Completed</p>
        </div>
        </div>

        <h2 style="text-align:left;">Pending Account Approvals</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th style="width: 100px; text-align: center;">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if ($pending_users_list->num_rows > 0): 
                    $current_group = ""; // Variable to track the role group
                    
                    while($row = $pending_users_list->fetch_assoc()): 
                        // Check if we have moved to a new category (e.g., from Admin to Student)
                        if ($current_group != $row['u_type']): 
                            $current_group = $row['u_type'];
                ?>
                            <tr style="background: rgba(197, 179, 88, 0.2);">
                                <td colspan="5" style="font-weight: bold; color: #C5B358; text-transform: uppercase; letter-spacing: 1px;">
                                    Pending <?php echo $current_group; ?>s
                                </td>
                            </tr>
                <?php 
                        endif; 
                ?>
                    <tr>
                        <td><a class="btn-z"> <?php echo $row['u_first_name'] . " " . $row['u_last_name']; ?></a></td>
                        <td><?php echo $row['u_email']; ?></td>
                        <td><?php echo $row['u_type']; ?></td>
                        <td><span style="color:orange;"><?php echo $row['u_status']; ?></span></td>
                        <td>
                            <div class="action-btns">
                                <a href="approve.php?id=<?php echo $row['u_id']; ?>" class="btn-a btn-small">Approve</a>
                                <a href="reject.php?id=<?php echo $row['u_id']; ?>" class="btn-a btn-small btn-reject" onclick="return confirm('Sure ka ga?')">Reject</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5">All registered users have been approved.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-actions">   
            <a href="login.php" class="back-btn">← Logout</a>
        </div>
    </div>
</body>
</html>