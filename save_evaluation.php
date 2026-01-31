<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $u_id = $_SESSION['u_id'];
    $t_id = $_POST['t_id'];
    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    
    // Kwentahon ang average score
    $average = ($q1 + $q2) / 2;

    $sql = "INSERT INTO tbl_evaluations (u_id, t_id, score, date_added) 
            VALUES ('$u_id', '$t_id', '$average', NOW())";
        echo "<script>alert('Evaluation submitted successfully!'); window.location='student_dash.php';</script>";
    } else {
        echo "Error: " . $conn->error;
    }
?>