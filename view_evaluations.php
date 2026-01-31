<?php
include 'db.php';
session_start();

// Siguraduhon nga naka-login ang teacher
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Teacher') {
    header('Location: login.php');
    exit;
}

$u_id = $_SESSION['u_id'];

// I-join nato ang tbl_evaluations ug tbl_teachers 
// para makuha ang ngalan sa teacher nga gi-evaluate
$query = "SELECT e.*, t.t_name 
          FROM tbl_evaluations e 
          JOIN tbl_teachers t ON e.t_id = t.t_id 
          WHERE e.u_id = '$u_id'";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluations Done</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-container {
            margin-top: 50px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }
        th { color: #C5B358; }
        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            color: #C5B358;
            text-decoration: none;
        }
        .status-badge {
            background: #28a745;
            padding: 4px 8px;
            border-radius: 5px;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="teacher_dash.php" class="back-btn">← Back to Dashboard</a>
        <h1>My Evaluations Summary</h1>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Teacher Evaluated</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</body>
</html>