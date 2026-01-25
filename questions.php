<?php
include 'db.php';
session_start();

// Security: Only Admin
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_question'])) {
    $category = $_POST['q_category'];
    $text = $_POST['q_text'];

    $stmt = $conn->prepare("INSERT INTO tbl_questions (q_category, q_text) VALUES (?, ?)");
    $stmt->bind_param("ss", $category, $text);
    $stmt->execute();
    $stmt->close();
}

// Fetch Questions
$questions = $conn->query("SELECT * FROM tbl_questions ORDER BY q_category ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Evaluation Questions</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .layout-flex { display: flex; gap: 30px; margin-top: 20px; flex-wrap: wrap; }
        .form-container { flex: 1; min-width: 300px; background: rgba(255,255,255,0.05); padding: 25px; border-radius: 10px; border: 1px solid #C5B358; text-align: left; }
        .table-container { flex: 2; min-width: 450px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; }
        th { background: #C5B358; color: #002147; }
        .category-tag { background: #349304; padding: 2px 8px; border-radius: 4px; font-size: 0.8em; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Evaluation Questions</h1>
        <p>Define the criteria for teacher evaluations. <a href="admin_dash.php" style="color:#C5B358;">Back to Dashboard</a></p>

        <div class="layout-flex">
            <div class="form-container">
                <h3 style="color: #C5B358;">Add New Question</h3>
                <form method="POST">
                    <label>Category:</label>
                    <select name="q_category" required style="width:100%; padding: 10px; margin-bottom: 15px;">
                        <option value="Teaching Skills">Teaching Skills</option>
                        <option value="Classroom Management">Classroom Management</option>
                        <option value="Professionalism">Professionalism</option>
                        <option value="Student Engagement">Student Engagement</option>
                    </select>
                    
                    <label>Question Text:</label>
                    <textarea name="q_text" rows="4" required style="width:100%; border-radius: 4px; padding: 10px;"></textarea>
                    
                    <button type="submit" name="add_question" class="btn-a" style="width:100%; margin-top: 15px;">Save Question</button>
                </form>
            </div>

            <div class="table-container">
                <h3 style="text-align: left;">Current Questionnaire (<?php echo $questions->num_rows; ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Question</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($questions->num_rows > 0): ?>
                            <?php while($row = $questions->fetch_assoc()): ?>
                            <tr>
                                <td><span class="category-tag"><?php echo $row['q_category']; ?></span></td>
                                <td><?php echo $row['q_text']; ?></td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" style="text-align:center;">No questions added yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>