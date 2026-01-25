<?php
include 'db.php';
session_start();

if (isset($_GET['id']) && $_SESSION['u_type'] === 'Admin') {
    $id = $_GET['id'];
    $stmt = $conn->prepare("UPDATE tbl_user SET u_status = 'Approved' WHERE u_id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin_dash.php?msg=success");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>