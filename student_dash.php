<?php
include 'db.php';
session_start();

// Security: Ensure user is logged in and is a Student
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Student') {
    header('Location: login.php');
    exit;
}

$u_id = $_SESSION['u_id'];

$teacher_query = $conn->query("SELECT COUNT(*) as count FROM tbl_teachers");
$total_teachers = $teacher_query ? $teacher_query->fetch_assoc()['count'] : 0;


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
    <title>Student Dashboard</title>
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
        
        .user-table { width: 100%; border-collapse: collapse; background: #002147; }
        .user-table th, .user-table td { padding: 12px; border: 1px solid #444; text-align: left; }
        .user-table th { background: #C5B358; color: #002147; }
        
        .dashboard-container {
            max-width: 1100px !important; 
            width: 90%;
        }
    </style>
</head>
<body>
    <div class="container dashboard-container">
        <h1>Student Dashboard</h1>
        <h2 style="text-align:left;">Evaluation Progress</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Teachers</h3>
                <p><?php echo $total_teachers; ?> Registered</p>
            </div>
            <div class="stat-card">
                <h3>Completed</h3>
                <p><?php echo $evals_done; ?> Evaluations</p>
            </div>
            <div class="stat-card">
                <h3>Remaining</h3>
                <p><?php echo ($total_teachers - $evals_done); ?> To-Do</p>
            </div>
        </div>

        <h2 style="text-align:left;">Available Teachers</h2>
        <table class="user-table"> <thead>
                <tr>
                    <th>Teacher Name</th>
                    <th>Department</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($pending_evals->num_rows > 0): ?>
                    <?php while($row = $pending_evals->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['t_first_name'] . " " . $row['t_last_name']; ?></td>
                        <td><?php echo $row['t_department']; ?></td>
                         <td>
                            <a href="evaluate.php?t_id=<?php echo $row['t_id']; ?>" class="btn-a" style="padding: 5px 10px; font-size: 0.8em;">Evaluate</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" style="text-align:center;">
                            Excellent! You have evaluated all available teachers.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <h2 style="text-align:left;">Completed Evaluations</h2>
        <table class="user-table">
            <thead>
                <tr>
                    <th>Teacher Name</th>
                    <th>Department</th>
                    <th>Date Evaluated</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $completed_evals = $conn->query("
                    SELECT t.t_first_name, t.t_last_name, t.t_department, e.e_date
                    FROM tbl_evaluations e
                    JOIN tbl_teachers t ON e.t_id = t.t_id
                    WHERE e.u_id = $u_id
                    ORDER BY e.e_date DESC
                ");
                if ($completed_evals && $completed_evals->num_rows > 0): ?>
                    <?php while($row = $completed_evals->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['t_first_name'] . " " . $row['t_last_name']; ?></td>
                        <td><?php echo $row['t_department']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['e_date'])); ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="3" style="text-align:center;">
                            No evaluations completed yet.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <div class="footer-actions" style="display: flex; gap: 10px; justify-content: center;">   
            <a href="edit_profile.php" class="back-btn" style="background: #C5B358; color: #002147;">Edit Profile</a>
            <a href="logout.php" class="back-btn">← Logout</a>
        </div>
    </div> 
</body>
</html>