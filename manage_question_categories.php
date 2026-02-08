<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Admin') {
    header('Location: login.php');
    exit;
}

// Handle Adding Category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_category'])) {
    $cat_name = $_POST['cat_name'];
    
    $check = $conn->query("SELECT * FROM tbl_question_categories WHERE cat_name = '$cat_name'");
    if ($check->num_rows == 0) {
        $stmt = $conn->prepare("INSERT INTO tbl_question_categories (cat_name) VALUES (?)");
        $stmt->bind_param("s", $cat_name);
        $stmt->execute();
        $stmt->close();
        $msg = "Category added successfully!";
    } else {
        $msg = "Category already exists!";
    }
}

// Fetch All Categories
$categories = $conn->query("SELECT * FROM tbl_question_categories ORDER BY cat_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Question Categories</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .layout-flex {
            display: flex;
            gap: 30px;
            align-items: flex-start;
            flex-wrap: wrap;
            margin-top: 20px;
        }
        .form-container { 
            flex: 1; 
            min-width: 300px; 
            background: rgba(255, 255, 255, 0.05); 
            padding: 25px; 
            border-radius: 10px; 
            border: 1px solid #C5B358;
            text-align: left;
        }
        .table-container { flex: 2; min-width: 450px; }
        
        table { width: 100%; border-collapse: collapse; background: rgba(0, 33, 71, 0.9); }
        th, td { padding: 12px; border: 1px solid #444; text-align: left; color: white; }
        th { background: #C5B358; color: #002147; }
        
        .btn-add { width: 100%; margin-top: 15px; cursor: pointer; box-sizing: border-box; }
        .btn-questions { width: 100%; margin-top: 10px; padding: 12px 20px; background: #f39c12; color: white; border: none; border-radius: 8px; text-decoration: none; text-align: center; font-weight: bold; cursor: pointer; display: block; transition: 0.3s; box-sizing: border-box; }
        .btn-questions:hover { background: #f5af19; transform: translateY(-2px); }
        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; background: rgba(52, 147, 4, 0.2); border: 1px solid #349304; color: #349304; }
        .form-container label { color: black; font-weight: bold; }
        .form-container input { color: black; background: white; }
        .action-btns { display: flex; gap: 6px; justify-content: center; }
        .btn-small { padding: 5px 10px; font-size: 0.85rem; border-radius: 4px; text-decoration: none; display: inline-block; text-align: center; }
        .btn-edit { background: #3498db; color: white; }
        .btn-delete { background: #c0392b; color: white; }
    </style>
</head>
<body>
    <div class="container" style="width: 95%; max-width: 1200px;">
        <h1>Manage Question Categories</h1>
        <p>Create and manage question categories for your evaluation system.</p>

        <?php if (!empty($msg)): ?>
            <div class="message"><?php echo $msg; ?></div>
        <?php endif; ?>

        <div class="layout-flex">
            <div class="form-container">
                <h3 style="color: #C5B358;">Add New Category</h3>
                <form method="POST">
                    <label>Category Name:</label>
                    <input type="text" name="cat_name" placeholder="e.g. Teaching Skills" required style="width:100%; padding: 10px; margin-bottom: 15px;">
                    
                    <button type="submit" name="add_category" class="btn-a btn-add">Add Category</button>
                    <a href="questions.php" class="btn-questions">📋 Manage Questions</a>
                </form>
            </div>

            <div class="table-container">
                <h3 style="text-align: left;">Question Categories (<?php echo $categories->num_rows; ?>)</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Category Name</th>
                            <th style="width: 130px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($categories->num_rows > 0): ?>
                            <?php while($row = $categories->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['cat_id']; ?></td>
                                <td><?php echo htmlspecialchars($row['cat_name']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <a href="edit_question_category.php?id=<?php echo $row['cat_id']; ?>" class="btn-small btn-edit">Edit</a>
                                        <a href="delete_question_category.php?id=<?php echo $row['cat_id']; ?>" class="btn-small btn-delete" onclick="return confirm('Delete this category?')">Delete</a>
                                    </div>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="3" style="text-align:center;">No categories found. Create one to get started.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="footer-actions">
            <a href="questions.php" class="btn-a" style="padding: 10px 20px; margin-right: 10px;">→ Manage Questions</a>
            <a href="admin_dash.php" class="back-btn">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
