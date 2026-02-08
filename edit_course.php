<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: manage_courses.php');
    exit;
}

$course_id = $_GET['id'];
$course = $conn->query("SELECT * FROM tbl_courses WHERE c_id = $course_id")->fetch_assoc();

if (!$course) {
    header('Location: manage_courses.php');
    exit;
}

$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_course'])) {
    $c_name = $_POST['c_name'];
    $c_code = $_POST['c_code'];
    $d_id = $_POST['d_id'];
    
    $stmt = $conn->prepare("UPDATE tbl_courses SET c_name = ?, c_code = ?, d_id = ? WHERE c_id = ?");
    $stmt->bind_param("ssii", $c_name, $c_code, $d_id, $course_id);
    if ($stmt->execute()) {
        $msg = "Course updated successfully!";
        $course['c_name'] = $c_name;
        $course['c_code'] = $c_code;
        $course['d_id'] = $d_id;
    }
    $stmt->close();
}

// Fetch departments
$departments = $conn->query("SELECT * FROM tbl_departments ORDER BY d_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Course</title>
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
            color: white;
            font-weight: bold;
            display: block;
            margin-bottom: 8px;
        }
        .edit-container input,
        .edit-container select {
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
        <h1>Edit Course</h1>
        
        <?php if (!empty($msg)): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="edit-container">
            <h2>Edit Course Details</h2>
            <form method="POST">
                <label for="d_id">Department:</label>
                <select id="d_id" name="d_id" required>
                    <option value="">-- Select Department --</option>
                    <?php while($dept = $departments->fetch_assoc()): ?>
                        <option value="<?php echo $dept['d_id']; ?>" <?php echo ($dept['d_id'] == $course['d_id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($dept['d_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="c_name">Course Name:</label>
                <input type="text" id="c_name" name="c_name" value="<?php echo htmlspecialchars($course['c_name']); ?>" required>
                
                <label for="c_code">Course Code:</label>
                <input type="text" id="c_code" name="c_code" value="<?php echo htmlspecialchars($course['c_code']); ?>" required>
                
                <div class="button-group">
                    <button type="submit" name="update_course" class="btn-update">Update Course</button>
                    <a href="manage_courses.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
