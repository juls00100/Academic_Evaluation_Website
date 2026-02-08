<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (isset($_GET['tc_id']) && isset($_GET['t_id'])) {
    $tc_id = (int)$_GET['tc_id'];
    $t_id = (int)$_GET['t_id'];

    // Alternative: if tc_id not available, use t_id and c_id
    if ($tc_id > 0) {
        $stmt = $conn->prepare("DELETE FROM tbl_teacher_courses WHERE tc_id = ?");
        $stmt->bind_param("i", $tc_id);
    }
    
    if (isset($stmt)) {
        $stmt->execute();
        $stmt->close();
    }
    
    header("Location: assign_teacher_courses.php?t_id=$t_id&msg=removed");
    exit;
} else {
    header('Location: assign_teacher_courses.php');
    exit;
}
?>
