<?php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['u_id'])) {
    $u_id = $_SESSION['u_id'];
    $t_id = $_POST['t_id'];
    $c_id = $_POST['c_id'];
    
    // Insert main record
    $stmt = $conn->prepare("INSERT INTO tbl_evaluations (u_id, t_id, c_id, e_status) VALUES (?, ?, ?, 'submitted')");
    $stmt->bind_param("iii", $u_id, $t_id, $c_id);
    
    if ($stmt->execute()) {
        $e_id = $conn->insert_id;
        $stmt_ans = $conn->prepare("INSERT INTO tbl_evaluation_answers (e_id, q_id, rating) VALUES (?, ?, ?)");
        
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'q') === 0) {
                $q_id = substr($key, 1);
                $rating = (int)$value;
                $stmt_ans->bind_param("iii", $e_id, $q_id, $rating);
                $stmt_ans->execute();
            }
        }
        header("Location: student_dash.php?msg=success");
    }
}
?>