<?php
include 'db.php';
session_start();

// Only Admin can access
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $e_id = $_POST['e_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE tbl_evaluations SET e_status = ? WHERE e_id = ?");
    $stmt->bind_param("si", $new_status, $e_id);
    $stmt->execute();
    $stmt->close();
    
    header('Location: manage_evaluations.php');
    exit;
}

// Fetch all evaluations with teacher and course info
$query = "SELECT e.*, 
                 CONCAT(t.t_first_name, ' ', t.t_last_name) as teacher_name,
                 CONCAT(u.u_first_name, ' ', u.u_last_name) as student_name,
                 c.c_name as course_name
          FROM tbl_evaluations e
          JOIN tbl_teachers t ON e.t_id = t.t_id
          JOIN tbl_user u ON e.u_id = u.u_id
          LEFT JOIN tbl_courses c ON e.c_id = c.c_id
          ORDER BY e.e_status ASC, e.e_date DESC";
          
$result = $conn->query($query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Evaluations</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-container {
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: white;
            background: rgba(0, 33, 71, 0.9);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #444;
        }
        th {
            background: #C5B358;
            color: #002147;
            font-weight: bold;
        }
        .status-badge {
            padding: 6px 12px;
            border-radius: 5px;
            font-size: 0.9em;
            font-weight: bold;
            display: inline-block;
        }
        .status-submitted {
            background: #3498db;
            color: white;
        }
        .status-reviewed {
            background: #f39c12;
            color: white;
        }
        .status-approved {
            background: #27ae60;
            color: white;
        }
        .action-form {
            display: inline-flex;
            gap: 8px;
            align-items: center;
        }
        .action-form select {
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: black;
            background: white;
        }
        .action-form button {
            padding: 6px 12px;
            background: #f39c12;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .action-form button:hover {
            background: #e67e22;
        }
        .filter-btns {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
        }
        .filter-btn {
            padding: 8px 16px;
            background: #34495e;
            color: white;
            border: 2px solid #C5B358;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }
        .filter-btn.active {
            background: #C5B358;
            color: #002147;
        }
        .filter-btn:hover {
            background: #C5B358;
            color: #002147;
        }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1400px;">
        <h1>Manage Evaluations</h1>
        <p>Review and approve student evaluations for teachers.</p>

        <div class="filter-btns">
            <a href="manage_evaluations.php" class="filter-btn active">All</a>
            <a href="manage_evaluations.php?status=submitted" class="filter-btn">Submitted</a>
            <a href="manage_evaluations.php?status=reviewed" class="filter-btn">Reviewed</a>
            <a href="manage_evaluations.php?status=approved" class="filter-btn">Approved</a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Student</th>
                        <th>Teacher</th>
                        <th>Course</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <th style="width: 200px;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['e_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($row['e_date'])); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($row['e_status']); ?>"><?php echo ucfirst($row['e_status']); ?></span></td>
                            <td>
                                <form method="POST" class="action-form">
                                    <input type="hidden" name="e_id" value="<?php echo $row['e_id']; ?>">
                                    <select name="status" required>
                                        <option value="">-- Change Status --</option>
                                        <option value="submitted">Submitted</option>
                                        <option value="reviewed">Reviewed</option>
                                        <option value="approved">Approved</option>
                                    </select>
                                    <button type="submit" name="update_status">Update</button>
                                </form>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="7" style="text-align:center;">No evaluations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-actions" style="margin-top: 30px;">
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
