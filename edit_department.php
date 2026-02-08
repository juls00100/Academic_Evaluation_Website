<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_departments.php');
    exit;
}

$dept_id = $_GET['id'];
$dept = $conn->query("SELECT * FROM tbl_departments WHERE d_id = $dept_id")->fetch_assoc();

if (!$dept) {
    header('Location: manage_departments.php');
    exit;
}

$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_department'])) {
    $dept_name = $_POST['d_name'];
    
    // Check if another department with same name exists
    $check = $conn->query("SELECT * FROM tbl_departments WHERE d_name = '$dept_name' AND d_id != $dept_id");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("UPDATE tbl_departments SET d_name = ? WHERE d_id = ?");
        $stmt->bind_param("si", $dept_name, $dept_id);
        if ($stmt->execute()) {
            $msg = "Department updated successfully!";
            $dept['d_name'] = $dept_name;
        }
        $stmt->close();
    } else {
        $msg = "Department name already exists!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Department</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .edit-container {
            max-width: 500px;
            background: rgba(255, 255, 255, 0.05);
            padding: 30px;
            border-radius: 10px;
            border: 1px solid #C5B358;
            margin: 30px auto;
            text-align: left;
        }
        .edit-container h2 {
            color: #C5B358;
            margin-bottom: 20px;
        }
        .edit-container label {
            color: black;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        .edit-container input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: black;
            background: white;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-update {
            flex: 1;
            padding: 10px;
            background: #27ae60;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn-update:hover {
            background: #2ecc71;
        }
        .btn-cancel {
            flex: 1;
            padding: 10px;
            background: #95a5a6;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .btn-cancel:hover {
            background: #bdc3c7;
        }
        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
            background: rgba(52, 147, 4, 0.2);
            border: 1px solid #349304;
            color: #349304;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit Department</h1>
        
        <?php if (!empty($msg)): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="edit-container">
            <h2>Edit Department Details</h2>
            <form method="POST">
                <label for="d_name" style="color: white;">Department Name:</label>
                <input type="text" id="d_name" name="d_name" value="<?php echo htmlspecialchars($dept['d_name']); ?>" required>
                
                <div class="button-group">
                    <button type="submit" name="update_department" class="btn-update">Update Department</button>
                    <a href="manage_departments.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
