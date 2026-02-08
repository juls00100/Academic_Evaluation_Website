<?php
include 'db.php';
session_start();

// Allow Teacher and Admin to view evaluations
if (!isset($_SESSION['u_id']) || ($_SESSION['u_type'] !== 'Teacher' && $_SESSION['u_type'] !== 'Admin')) {
    header('Location: login.php');
    exit;
}

$u_id = $_SESSION['u_id'];
$u_type = $_SESSION['u_type'];

// Handle status update (Admin only)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status']) && $u_type === 'Admin') {
    $e_id = $_POST['e_id'];
    $new_status = $_POST['status'];
    
    $stmt = $conn->prepare("UPDATE tbl_evaluations SET e_status = ? WHERE e_id = ?");
    $stmt->bind_param("si", $new_status, $e_id);
    $stmt->execute();
    $stmt->close();
}

// Admins see all evaluations; Teachers see only their own
if ($u_type === 'Admin') {
    $query = "SELECT e.*, CONCAT(t.t_first_name, ' ', t.t_last_name) as teacher_name,
                     CONCAT(u.u_first_name, ' ', u.u_last_name) as student_name
              FROM tbl_evaluations e 
              JOIN tbl_teachers t ON e.t_id = t.t_id
              JOIN tbl_user u ON e.u_id = u.u_id
              ORDER BY e.e_status ASC, e.e_date DESC";
} else {
    $query = "SELECT e.*, CONCAT(t.t_first_name, ' ', t.t_last_name) as teacher_name 
              FROM tbl_evaluations e 
              JOIN tbl_teachers t ON e.t_id = t.t_id 
              WHERE e.t_id IN (SELECT t_id FROM tbl_teachers WHERE t_user_id = $u_id)
              ORDER BY e.e_date DESC";
}
$result = $conn->query($query);
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
        .action-btns {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
        }
        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .btn-approve {
            background: #27ae60;
            color: white;
        }
        .btn-approve:hover {
            background: #2ecc71;
        }
        .btn-reject {
            background: #c0392b;
            color: white;
        }
        .btn-reject:hover {
            background: #e74c3c;
        }
        .btn-pending {
            background: #f39c12;
            color: white;
        }
        .btn-pending:hover {
            background: #e67e22;
        }
    </style>
</head>
<body>
    <div class="container">
        
        <h1><?php echo ($_SESSION['u_type'] === 'Admin') ? 'All Evaluations' : 'My Evaluations Summary'; ?></h1>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <?php if ($u_type === 'Admin'): ?>
                        <th>Student</th>
                        <?php endif; ?>
                        <th>Teacher Evaluated</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        <?php if ($u_type === 'Admin'): ?>
                        <th style="width: 200px; text-align: center;">Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <?php if ($u_type === 'Admin'): ?>
                            <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                            <?php endif; ?>
                            <td><?php echo htmlspecialchars($row['teacher_name']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($row['e_date'])); ?></td>
                            <td><span class="status-badge status-<?php echo strtolower($row['e_status']); ?>"><?php echo ucfirst($row['e_status']); ?></span></td>
                            <?php if ($u_type === 'Admin'): ?>
                            <td>
                                <div class="action-btns">
                                    <?php if ($row['e_status'] !== 'approved'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="e_id" value="<?php echo $row['e_id']; ?>">
                                        <input type="hidden" name="status" value="approved">
                                        <button type="submit" name="update_status" class="btn-small btn-approve">✓ Approve</button>
                                    </form>
                                    <?php endif; ?>
                                    <?php if ($row['e_status'] !== 'rejected'): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="e_id" value="<?php echo $row['e_id']; ?>">
                                        <input type="hidden" name="status" value="rejected">
                                        <button type="submit" name="update_status" class="btn-small btn-reject">✕ Reject</button>
                                    </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="<?php echo $u_type === 'Admin' ? 5 : 3; ?>" style="text-align:center;">No evaluations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-actions">   
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>