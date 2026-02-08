<?php
include 'db.php';
include 'session.php';


if(isset($_GET['type']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $type = $_GET['type'];
    
    // Mapping table names to prevent SQL injection
    $tables = [
        'teacher' => ['tbl_teachers', 't_id'],
        'course'  => ['tbl_courses', 'c_id'],
        'user'    => ['tbl_user', 'u_id']
    ];

    if(array_key_exists($type, $tables)) {
        $table = $tables[$type][0];
        $col = $tables[$type][1];
        
        $stmt = $conn->prepare("DELETE FROM $table WHERE $col = ?");
        $stmt->execute([$id]);
    }
    header("Location: " . $_SERVER['HTTP_REFERER']);
}
?>