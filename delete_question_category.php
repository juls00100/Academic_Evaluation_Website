<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_question_categories.php');
    exit;
}

$cat_id = $_GET['id'];

// Delete the category
$stmt = $conn->prepare("DELETE FROM tbl_question_categories WHERE cat_id = ?");
$stmt->bind_param("i", $cat_id);
$stmt->execute();
$stmt->close();

// Redirect back to manage categories
header('Location: manage_question_categories.php');
exit;
?>
