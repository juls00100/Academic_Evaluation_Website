<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_courses.php');
    exit;
}

$course_id = $_GET['id'];

// Delete the course
$stmt = $conn->prepare("DELETE FROM tbl_courses WHERE c_id = ?");
$stmt->bind_param("i", $course_id);
$stmt->execute();
$stmt->close();

// Redirect back to manage courses
header('Location: manage_courses.php');
exit;
?>
