<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// 1. Fetch PENDING students first (Needs attention)
$pending_students = $conn->query("SELECT * FROM tbl_user WHERE u_type = 'Student' AND u_status = 'Pending' ORDER BY u_id DESC");

// 2. Fetch APPROVED students
$approved_students = $conn->query("SELECT * FROM tbl_user WHERE u_type = 'Student' AND u_status = 'Approved' ORDER BY u_id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .table-container { margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; background: rgba(0,33,71,0.9); margin-bottom: 20px; }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; color: white; }
        th { background: #C5B358; color: #002147; }
        
        /* Category Headers */
        .category-header { 
            background: rgba(197, 179, 88, 0.2); 
            padding: 10px; 
            border-left: 5px solid #C5B358; 
            margin-bottom: 10px;
            color: #C5B358;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
        }
        
        .action-btns { display:flex; gap:8px; justify-content:center; }
        .btn-small { padding:6px 10px; font-size:0.85rem; border-radius:6px; text-decoration:none; display:inline-block; text-align:center; }
        .btn-edit { background: #3498db; color: #fff; }
        .btn-delete { background: #c0392b; color: #fff; }
        .btn-approve { background: #27ae60; color: #fff; }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.8em;
            font-weight: bold;
        }
        .status-pending { background-color: #f39c12; color: white; }
        .status-approved { background-color: #27ae60; color: white; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Manage Students</h1>
        <p>List of all students categorized by status.</p>

        <div class="table-container">
            <div class="category-header">
                <span>NEW REGISTRATIONS (Pending Approval)</span>
                <span>Count: <?php echo $pending_students->num_rows; ?></span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="width:220px; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($pending_students->num_rows > 0): ?>
                        <?php while($row = $pending_students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['u_id']; ?></td>
                            <td><?php echo $row['u_first_name'] . ' ' . $row['u_last_name']; ?></td>
                            <td><?php echo $row['u_email']; ?></td>
                            <td><span class="status-badge status-pending"><?php echo $row['u_status']; ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="approve.php?id=<?php echo $row['u_id']; ?>" class="btn-small btn-approve">Approve</a>
                                    <a href="edit_user.php?id=<?php echo $row['u_id']; ?>" class="btn-small btn-edit">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $row['u_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Reject and delete this student?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No pending registrations.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-container">
            <div class="category-header" style="border-left-color: #27ae60;">
                <span>OFFICIALLY REGISTERED (Approved)</span>
                <span>Count: <?php echo $approved_students->num_rows; ?></span>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Status</th>
                        <th style="width:220px; text-align:center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($approved_students->num_rows > 0): ?>
                        <?php while($row = $approved_students->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['u_id']; ?></td>
                            <td><?php echo $row['u_first_name'] . ' ' . $row['u_last_name']; ?></td>
                            <td><?php echo $row['u_email']; ?></td>
                            <td><span class="status-badge status-approved"><?php echo $row['u_status']; ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit_user.php?id=<?php echo $row['u_id']; ?>" class="btn-small btn-edit">Edit</a>
                                    <a href="delete_user.php?id=<?php echo $row['u_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Delete this student record?')">Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" style="text-align:center;">No approved students found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="footer-actions" style="margin-top: 20px;">
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>