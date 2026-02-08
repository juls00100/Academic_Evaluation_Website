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

// Fetch Categories
$categories = $conn->query("SELECT * FROM tbl_question_categories ORDER BY cat_name ASC");
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
        .action-btns { display: flex; gap: 6px; justify-content: center; }
        .btn-small { padding: 5px 10px; font-size: 0.85rem; border-radius: 4px; text-decoration: none; display: inline-block; text-align: center; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #c0392b; color: white; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Evaluation Questions</h1>
        <p>Define the criteria for teacher evaluations. </p>

        <div class="layout-flex">
            <div class="form-container">
                <h3 style="color: #C5B358;">Add New Question</h3>
                <form method="POST">
                    <label>Category:</label>
                    <select name="q_category" required style="padding: 10px; margin-bottom: 15px; color: black;">
                        <option value="">-- Select Category --</option>
                        <?php 
                        $categories->data_seek(0);
                        if ($categories && $categories->num_rows > 0):
                            while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($cat['cat_name']); ?>"><?php echo htmlspecialchars($cat['cat_name']); ?></option>
                        <?php endwhile;
                        endif; ?>
                    </select>
                    
                    <label>Question Text:</label>
                    <textarea name="q_text" rows="4" required style="width:100%; border-radius: 4px; padding: 10px;"></textarea>
                    
                    <button type="submit" name="add_question" class="btn-a" style="width:100%; margin-top: 15px;">Save Question</button>
                    <a href="manage_question_categories.php" style="width:100%; margin-top: 10px; padding: 12px 20px; background: #9b59b6; color: white; border: none; border-radius: 8px; text-decoration: none; text-align: center; font-weight: bold; cursor: pointer; display: block; transition: 0.3s; box-sizing: border-box;" onmouseover="this.style.background='#a76fb8'" onmouseout="this.style.background='#9b59b6'">🏷️ Manage Categories</a>
                </form>
            </div>

            <div class="table-container">
                <h3 style="text-align: left;">Current Questionnaire (<?php echo $questions->num_rows; ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Question</th>
                            <th style="width: 130px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($questions->num_rows > 0): ?>
                            <?php while($row = $questions->fetch_assoc()): ?>
                            <tr>
                                <td><span class="category-tag"><?php echo $row['q_category']; ?></span></td>
                                <td><?php echo $row['q_text']; ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_question.php?id=<?php echo $row['q_id']; ?>" class="btn-small btn-edit">Edit</a>
                                        <a href="delete_question.php?id=<?php echo $row['q_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Delete this question?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;">No questions added yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="footer-actions">
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>