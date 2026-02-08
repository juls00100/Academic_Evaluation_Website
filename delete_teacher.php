<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Delete associated course assignments first
    $conn->query("DELETE FROM tbl_teacher_courses WHERE t_id = $id");
    
    // Then delete the teacher
    $stmt = $conn->prepare("DELETE FROM tbl_teachers WHERE t_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: manage_teachers.php?msg=deleted');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header('Location: manage_teachers.php');
}

?>
