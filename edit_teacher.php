<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_teachers.php');
    exit;
}

$id = (int)$_GET['id'];
$teacher_q = $conn->prepare("SELECT * FROM tbl_teachers WHERE t_id = ?");
$teacher_q->bind_param("i", $id);
$teacher_q->execute();
$result = $teacher_q->get_result();
$teacher = $result->fetch_assoc();

if (!$teacher) {
    header('Location: manage_teachers.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['t_first_name'];
    $last = $_POST['t_last_name'];
    $dept = $_POST['t_department'];

    $stmt = $conn->prepare("UPDATE tbl_teachers SET t_first_name = ?, t_last_name = ?, t_department = ? WHERE t_id = ?");
    $stmt->bind_param("sssi", $first, $last, $dept, $id);
    if ($stmt->execute()) {
        header('Location: manage_teachers.php?msg=updated');
        exit;
    } else {
        $error = $conn->error;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Teacher</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container { max-width:600px; margin:30px auto; background: rgba(255,255,255,0.03); padding:20px; border-radius:8px; }
        label { display:block; margin-top:12px; color:white; }
        input { width:100%; padding:8px; margin-top:6px; border-radius:4px; color: black; background: white; box-sizing: border-box; }
    </style>
</head>
<body>
    <div class="container" style="max-width:800px; width:95%;">
        <h1>Edit Teacher</h1>
        <?php if (!empty($error)): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>
        <div class="form-container">
            <form method="POST">
                <label for="t_first_name">First Name</label>
                <input type="text" name="t_first_name" id="t_first_name" value="<?php echo htmlspecialchars($teacher['t_first_name']); ?>" required>

                <label for="t_last_name">Last Name</label>
                <input type="text" name="t_last_name" id="t_last_name" value="<?php echo htmlspecialchars($teacher['t_last_name']); ?>" required>

                <label for="t_department">Department</label>
                <input type="text" name="t_department" id="t_department" value="<?php echo htmlspecialchars($teacher['t_department']); ?>" required>

                <button type="submit" class="btn-a" style="margin-top:12px;">Save Changes</button>
                <a href="manage_teachers.php" class="back-btn" style="margin-left:10px;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
