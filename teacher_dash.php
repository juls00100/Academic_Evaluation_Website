<?php
include 'db.php';
session_start();
// Security: Ensure user is logged in and is a Teacher
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Teacher') {
    header('Location: login.php');
    exit;
}   
$u_id = $_SESSION['u_id'];
$teacher_query = $conn->query("SELECT COUNT(*) as count FROM tbl_teachers");
$total_teachers = $teacher_query ? $teacher_query->fetch_assoc()['count'] 
: 0;
$evals_done_query = $conn->query("SELECT COUNT(*) as count FROM tbl_evaluations WHERE u_id = '$u_id'");
$evals_done = $evals_done_query ? $evals_done_query->fetch_assoc()['count'] : 0; 
$pending_evals = $conn->query("
    SELECT * FROM tbl_teachers 
    WHERE t_id NOT IN (SELECT t_id FROM tbl_evaluations WHERE u_id = '$u_id')
");
$has_pending = ($pending_evals && $pending_evals->num_rows > 0);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
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
        .stat-card p { font-size: 0.9em; margin: 5px 0; color: white; }
        
        .dashboard-container {
            max-width: 1100px !important; 
            width: 90%;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
    <div style="margin-bottom: 20px;">
        <a href="login.php" class="back-btn">← Logout</a>
    </div>

    <h1>Teacher Dashboard</h1>
    
    <div class="stats-grid">
    <a href="view_teachers.php" style="text-decoration: none; flex: 2; max-width: 400px;">
        <div class="stat-card">
            <h3>Total Teachers</h3>
            <p><?php echo $total_teachers; ?></p>
        </div>
    </a>

    <a href="view_evaluations.php" style="text-decoration: none; flex: 1; max-width: 400px;">
        <div class="stat-card">
            <h3>Evaluations Completed</h3>
            <p><?php echo $evals_done; ?></p>
            </div>
    </a>
</div>

<div style="text-align: center; margin-top: 20px;">
    <a href="edit_profile.php" class="back-btn" style="background: #C5B358; color: black;">Edit My Info</a>
</div>
        </a>
    </div>
</div>
</body>
</html>
