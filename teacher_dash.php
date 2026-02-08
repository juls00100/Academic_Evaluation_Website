<?php
include 'db.php';
session_start();

// Security: Ensure user is logged in and is a Teacher
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Teacher') {
    header('Location: login.php');
    exit;
}   

$u_id = $_SESSION['u_id'];

// 1. Fetch teacher info based on logged-in user
$stmt = $conn->prepare("SELECT t_id FROM tbl_teachers WHERE t_user_id = ?");
$stmt->bind_param("i", $u_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();
$actual_t_id = $teacher ? $teacher['t_id'] : null;

// Initialize variables
$approved_evals = 0;
$total_evals = 0;
$avg_rating = 0;

if ($actual_t_id) {
    // 2. Get counts
    $count_query = "SELECT 
        SUM(CASE WHEN e_status = 'approved' THEN 1 ELSE 0 END) as approved,
        COUNT(*) as total
        FROM tbl_evaluations WHERE t_id = ?";
    
    $stmt = $conn->prepare($count_query);
    $stmt->bind_param("i", $actual_t_id);
    $stmt->execute();
    $counts = $stmt->get_result()->fetch_assoc();
    
    $approved_evals = $counts['approved'] ?? 0;
    $total_evals = $counts['total'] ?? 0;

    // 3. Get average rating
    if ($approved_evals > 0) {
        $avg_query = "SELECT AVG(rating) as average FROM tbl_evaluation_answers ea 
                      JOIN tbl_evaluations e ON ea.e_id = e.e_id 
                      WHERE e.t_id = ? AND e.e_status = 'approved'";
        $stmt = $conn->prepare($avg_query);
        $stmt->bind_param("i", $actual_t_id);
        $stmt->execute();
        $avg_rating = round($stmt->get_result()->fetch_assoc()['average'] ?? 0, 2);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Specific dashboard layout helpers not in main styles.css */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 30px 0;
        }
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            padding: 25px;
            border-radius: 12px;
            border-left: 5px solid #C5B358;
            transition: transform 0.2s;
        }
        .stat-card:hover { transform: translateY(-5px); background: rgba(255, 255, 255, 0.1); }
        .stat-card h3 { color: #C5B358; margin: 0; font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; }
        .stat-card .value { font-size: 2rem; margin: 10px 0; color: white; font-weight: bold; }
        .stat-label { font-size: 0.8em; color: #aaa; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Teacher Dashboard</h1>
        <p style="color: #ccc;">Welcome back! Here is your performance overview.</p>
        
        <div class="stats-grid">
            <a href="teacher_evaluations.php" style="text-decoration: none;">
                <div class="stat-card">
                    <h3>Average Rating</h3>
                    <div class="value"><?php echo $avg_rating; ?> <span style="font-size: 0.5em; opacity: 0.6;">/ 5.0</span></div>
                    <span class="stat-label">Based on approved feedback</span>
                </div>
            </a>

            <a href="view_evaluations.php" style="text-decoration: none;">
                <div class="stat-card">
                    <h3>Total Submissions</h3>
                    <div class="value"><?php echo $total_evals; ?></div>
                    <span class="stat-label">Evaluations received to date</span>
                </div>
            </a>
        </div>

        <div class="footer-actions">
           
            <a href="edit_profile.php" class="btn-a" style="background: #34495e;">Account Settings</a>
           </div>
            
            <div class="footer-actions">   
            <a href="login.php" class="back-btn">← Logout</a>
        </div>
    </div>
</body>
</html>