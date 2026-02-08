<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_departments.php');
    exit;
}

$dept_id = $_GET['id'];

// Delete the department
$stmt = $conn->prepare("DELETE FROM tbl_departments WHERE d_id = ?");
$stmt->bind_param("i", $dept_id);
$stmt->execute();
$stmt->close();

// Redirect back to manage departments
header('Location: manage_departments.php');
exit;
?>
