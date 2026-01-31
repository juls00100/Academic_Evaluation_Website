<?php
include 'db.php';
session_start();

if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Teacher') {
    header('Location: login.php');
    exit;
}

$query = "SELECT * FROM tbl_teachers";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List of Teachers</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .search-container {
            margin: 20px 0;
            display: flex;
            gap: 10px;
        }
        #teacherSearch {
            width: 100%;
            padding: 12px;
            border-radius: 5px;
            border: 1px solid #C5B358;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            font-size: 1rem;
        }
        .table-container {
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
        table { width: 100%; border-collapse: collapse; color: white; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid rgba(255, 255, 255, 0.2); }
        th { color: #C5B358; }
        .back-btn { display: inline-block; margin-bottom: 20px; color: #C5B358; text-decoration: none; }
        
        /* Style para sa "No Results" message */
        #noResults { display: none; color: #ff6b6b; padding: 20px; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <a href="teacher_dash.php" class="back-btn">← Back to Dashboard</a>
        <h1>All Teachers</h1>

        <div class="search-container">
            <input type="text" id="teacherSearch" placeholder="Pangita og pangalan sa teacher..." onkeyup="filterTeachers()">
        </div>
        
        <div class="table-container">
            <table id="teacherTable">
                <thead>
                    <tr>
                        <th>ID No.</th>
                        <th>Teacher Name</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="teacher-row">
                                <td><?php echo $row['u_id']; ?></td>
                                <td class="teacher-name"><?php echo $row['t_fname']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2">No records found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <div id="noResults">Walay teacher nga nakit-an sa maong pangalan.</div>
        </div>
    </div>

    <script>
    function filterTeachers() {
        let input = document.getElementById('teacherSearch').value.toLowerCase();
        let rows = document.getElementsByClassName('teacher-row');
        let noResults = document.getElementById('noResults');
        let hasMatch = false;

        for (let i = 0; i < rows.length; i++) {
            let name = rows[i].getElementsByClassName('teacher-name')[0].innerText.toLowerCase();
            if (name.includes(input)) {
                rows[i].style.display = "";
                hasMatch = true;
            } else {
                rows[i].style.display = "none";
            }
        }

        // I-pakita ang "No Results" message kung walay match
        noResults.style.display = hasMatch ? "none" : "block";
    }
    </script>
</body>
</html>