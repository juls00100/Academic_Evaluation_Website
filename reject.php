<?php
include 'db.php';
session_start();

if (isset($_GET['id']) && $_SESSION['u_type'] === 'Admin') {
    $id = $_GET['id'];
    
    $stmt = $conn->prepare("DELETE FROM tbl_user WHERE u_id = ? AND u_status = 'Pending'");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        header("Location: admin_dash.php?msg=rejected");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>