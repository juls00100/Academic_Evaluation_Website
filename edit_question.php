<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: questions.php');
    exit;
}

$question_id = $_GET['id'];
$question = $conn->query("SELECT * FROM tbl_questions WHERE q_id = $question_id")->fetch_assoc();

if (!$question) {
    header('Location: questions.php');
    exit;
}

$msg = '';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_question'])) {
    $q_category = $_POST['q_category'];
    $q_text = $_POST['q_text'];
    
    $stmt = $conn->prepare("UPDATE tbl_questions SET q_category = ?, q_text = ? WHERE q_id = ?");
    $stmt->bind_param("ssi", $q_category, $q_text, $question_id);
    if ($stmt->execute()) {
        $msg = "Question updated successfully!";
        $question['q_category'] = $q_category;
        $question['q_text'] = $q_text;
    }
    $stmt->close();
}

// Fetch categories
$categories = $conn->query("SELECT * FROM tbl_question_categories ORDER BY cat_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Question</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .edit-container {
            max-width: 600px;
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
        .edit-container select,
        .edit-container textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            color: black;
            background: white;
        }
        .edit-container textarea {
            resize: vertical;
            font-family: Arial, sans-serif;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-update {
            flex: 1;
            padding: 12px;
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
            padding: 12px;
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
        <h1>Edit Question</h1>
        
        <?php if (!empty($msg)): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="edit-container">
            <h2>Edit Question Details</h2>
            <form method="POST">
                <label for="q_category">Category:</label>
                <select id="q_category" name="q_category" required>
                    <option value="">-- Select Category --</option>
                    <?php while($cat = $categories->fetch_assoc()): ?>
                        <option value="<?php echo htmlspecialchars($cat['cat_name']); ?>" <?php echo ($question['q_category'] == $cat['cat_name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['cat_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
                
                <label for="q_text">Question Text:</label>
                <textarea id="q_text" name="q_text" rows="6" required><?php echo htmlspecialchars($question['q_text']); ?></textarea>
                
                <div class="button-group">
                    <button type="submit" name="update_question" class="btn-update">Update Question</button>
                    <a href="questions.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
