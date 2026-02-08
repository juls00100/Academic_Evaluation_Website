<?php
include 'db.php';
session_start();

// Security check
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Student') {
    header('Location: login.php');
    exit;
}

// Get IDs from URL/Session
$t_id = isset($_GET['t_id']) ? $_GET['t_id'] : null;
$u_id = $_SESSION['u_id'];

if (!$t_id) {
    die("Teacher ID missing.");
}

// Fetch Teacher Details
$result = $conn->query("SELECT t_first_name, t_last_name FROM tbl_teachers WHERE t_id = '$t_id'");
$teacher = $result->fetch_assoc();

// Fetch Courses assigned to this teacher (or all if no assignment exists)
$courses = $conn->query("
    SELECT c.*, d.d_name FROM tbl_courses c
    LEFT JOIN tbl_departments d ON c.d_id = d.d_id
    LEFT JOIN tbl_teacher_courses tc ON (c.c_id = tc.c_id AND tc.t_id = $t_id)
    WHERE tc.tc_id IS NOT NULL OR (SELECT COUNT(*) FROM tbl_teacher_courses) = 0
    ORDER BY d.d_name, c.c_name
");

// Fallback: If no teacher-course assignments exist, fetch all courses
if (!$courses || $courses->num_rows == 0) {
    $courses = $conn->query("SELECT c.*, d.d_name FROM tbl_courses c LEFT JOIN tbl_departments d ON c.d_id = d.d_id ORDER BY d.d_name, c.c_name");
}

// Fetch Questions
$questions = $conn->query("SELECT * FROM tbl_questions ORDER BY q_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Teacher</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h1>Evaluating: <?php echo $teacher['t_first_name'] . " " . $teacher['t_last_name']; ?></h1>
        
        <form action="save_evaluation.php" method="POST" class="eval-form">
    <input type="hidden" name="t_id" value="<?php echo $t_id; ?>">

    <div class="selection-card">
        <label for="c_id"><strong>Select the Course you are evaluating:</strong></label>
        <select name="c_id" id="c_id" required>
            <option value="">-- Choose Course --</option>
            <?php while($c = $courses->fetch_assoc()): ?>
                <option value="<?php echo $c['c_id']; ?>">
                    <?php echo $c['c_name'] . (isset($c['d_name']) ? " (" . $c['d_name'] . ")" : ""); ?>
                </option>
            <?php endwhile; ?>
        </select>
    </div>

        <table class="user-table">
            <thead>
                <tr>
                    <th>Question</th>
                    <th>5</th><th>4</th><th>3</th><th>2</th><th>1</th>
                </tr>
            </thead>
            <tbody>
                <?php while($question = $questions->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $question['q_text']; ?></td>
                    <?php for($i=5; $i>=1; $i--): ?>
                        <td style="text-align:center;">
                            <input type="radio" name="q<?php echo $question['q_id']; ?>" value="<?php echo $i; ?>" required>
                        </td>
                    <?php endfor; ?>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <div style="text-align:center; margin-top:20px;">
            <button type="submit" class="btn-a">Submit Evaluation</button>
        </div>
    </form>
        <div class="footer-actions">   
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>