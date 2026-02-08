<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: questions.php');
    exit;
}

$question_id = $_GET['id'];

// Delete the question
$stmt = $conn->prepare("DELETE FROM tbl_questions WHERE q_id = ?");
$stmt->bind_param("i", $question_id);
$stmt->execute();
$stmt->close();

// Redirect back to questions
header('Location: questions.php');
exit;
?>
