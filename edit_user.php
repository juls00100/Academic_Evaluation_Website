<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_students.php');
    exit;
}

$id = (int)$_GET['id'];
$user_q = $conn->prepare("SELECT * FROM tbl_user WHERE u_id = ?");
$user_q->bind_param("i", $id);
$user_q->execute();
$result = $user_q->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: manage_students.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first = $_POST['u_first_name'];
    $last = $_POST['u_last_name'];
    $email = $_POST['u_email'];
    $status = $_POST['u_status'];

    $stmt = $conn->prepare("UPDATE tbl_user SET u_first_name = ?, u_last_name = ?, u_email = ?, u_status = ? WHERE u_id = ?");
    $stmt->bind_param("ssssi", $first, $last, $email, $status, $id);
    if ($stmt->execute()) {
        header('Location: manage_students.php?msg=updated');
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
    <title>Edit Student</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .form-container { max-width:600px; margin:30px auto; background: rgba(255,255,255,0.03); padding:20px; border-radius:8px; }
        label { display:block; margin-top:12px; color:white; }
        input, select { width:100%; padding:8px; margin-top:6px; border-radius:4px; }
    </style>
</head>
<body>
    <div class="container" style="max-width:800px; width:95%;">
        <h1>Edit Student</h1>
        <?php if (!empty($error)): ?><div class="message error"><?php echo $error; ?></div><?php endif; ?>
        <div class="form-container">
            <form method="POST">
                <label for="u_first_name">First Name</label>
                <input type="text" name="u_first_name" id="u_first_name" value="<?php echo htmlspecialchars($user['u_first_name']); ?>" required>

                <label for="u_last_name">Last Name</label>
                <input type="text" name="u_last_name" id="u_last_name" value="<?php echo htmlspecialchars($user['u_last_name']); ?>" required>

                <label for="u_email">Email</label>
                <input type="email" name="u_email" id="u_email" value="<?php echo htmlspecialchars($user['u_email']); ?>" required>

                <label for="u_status">Status</label>
                <select name="u_status" id="u_status">
                    <option value="Pending" <?php echo ($user['u_status'] === 'Pending') ? 'selected' : ''; ?>>Pending</option>
                    <option value="Approved" <?php echo ($user['u_status'] === 'Approved') ? 'selected' : ''; ?>>Approved</option>
                    <option value="Suspended" <?php echo ($user['u_status'] === 'Suspended') ? 'selected' : ''; ?>>Suspended</option>
                </select>

                <button type="submit" class="btn-a" style="margin-top:12px;">Save Changes</button>
                <a href="manage_students.php" class="back-btn" style="margin-left:10px;">Cancel</a>
            </form>
        </div>
    </div>
</body>
</html>
