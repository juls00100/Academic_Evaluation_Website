<?php
include 'db.php';
session_start();


if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}
$teacher_query = $conn->query("SELECT COUNT(*) as count FROM tbl_teachers");
$teacher_count = $teacher_query->fetch_assoc()['count'];
$course_query = $conn->query("SELECT COUNT(*) as count FROM tbl_courses");
$course_count = $course_query->fetch_assoc()['count'];
$questions_query = $conn->query("SELECT COUNT(*) as count FROM tbl_questions");
$questions_count = $questions_query->fetch_assoc()['count'];
$student_count_query = $conn->query("SELECT COUNT(*) as count FROM tbl_user WHERE u_type = 'Student' AND u_status = 'Pending'");
$pending_count = $student_count_query->fetch_assoc()['count'];
$pending_users_list = $conn->query("SELECT * FROM tbl_user WHERE u_type = 'Student' AND u_status = 'Pending'");
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
    <div class="container" style="width: 90%; max-width: 1100px;">
        <h1>Admin Dashboard</h1>
        <h2 style="text-align:left;">System Overview</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Teachers</h3>
                <p><?php echo $teacher_count; ?> Registered</p>
            </div>
            <div class="stat-card">
                <a href="manage_courses.php" style="text-decoration: none; color: inherit;">
                <div class="stat-card">
                <h3>Courses</h3>
                <p><?php echo $course_count; ?> Courses</p>
            </div>
            <a href="questions.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Questions</h3>
                <p><?php echo $questions_count; ?> Questions</p>
            </div>
            <a href="admin_dash.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Pending Users</h3>
                <p><?php echo $pending_count; ?> Awaiting Approval </p>
            </div>
            <a href="view_evaluations.php" style="text-decoration: none; color: inherit;">
            <div class="stat-card">
                <h3>Evaluations</h3>
                <p><?php echo $evaluations_count; ?> Completed</p>
        </div>
        </div>

        <h2 style="text-align:left;">Pending Student Approvals</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pending_users_list->num_rows > 0): ?>
                    <?php while($row = $pending_users_list->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['u_email']; ?></td>
                        <td><?php echo $row['u_type']; ?></td>
                        <td><span style="color:orange;"><?php echo $row['u_status']; ?></span></td>
                        <td>
                            <a href="approve.php?id=<?php echo $row['u_id']; ?>" class="btn-a" style="padding: 5px 10px; font-size: 0.8em;">Approve</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4">All registered students have been approved.</td></tr>
                <?php endif; ?>
            </tbody>

        </table>

        <div class="footer-actions">   
            <a href="login.php" class="back-btn">← Logout</a>
        </div>
    </div>
 </body>
</html>