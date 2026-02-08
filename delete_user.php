<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $conn->prepare("DELETE FROM tbl_user WHERE u_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header('Location: manage_students.php?msg=deleted');
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
} else {
    header('Location: manage_students.php');
}

?>
