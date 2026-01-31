<?php
include 'db.php';
session_start();

// Siguraduhon nga student ang naka-login
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Student') {
    header('Location: login.php');
    exit;
}

$t_id = $_GET['t_id']; // Makuha gikan sa listahan sa teachers
$u_id = $_SESSION['u_id'];

// Kuhaon ang ngalan sa teacher
$result = $conn->query("SELECT t_fname FROM tbl_teachers WHERE t_id = '$t_id'");
$teacher = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Evaluate Teacher</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .eval-container { background: rgba(255, 255, 255, 0.1); padding: 30px; border-radius: 10px; color: white; margin-top: 20px; }
        .question { margin-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 10px; }
        .ratings { display: flex; gap: 15px; margin-top: 5px; }
        .btn-submit { background: #C5B358; color: black; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; width: 100%; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Evaluating: <?php echo $teacher['t_fname']; ?></h1>
        
        <form action="save_evaluation.php" method="POST" class="eval-container">
            <input type="hidden" name="t_id" value="<?php echo $t_id; ?>">
            
            <div class="question">
                <p>1. The teacher explains the lesson clearly.</p>
                <div class="ratings">
                    <label><input type="radio" name="q1" value="5" required> 5 - Excellent</label>
                    <label><input type="radio" name="q1" value="4"> 4</label>
                    <label><input type="radio" name="q1" value="3"> 3</label>
                    <label><input type="radio" name="q1" value="2"> 2</label>
                    <label><input type="radio" name="q1" value="1"> 1 - Poor</label>
                </div>
            </div>

            <div class="question">
                <p>2. The teacher is punctual in attending classes.</p>
                <div class="ratings">
                    <label><input type="radio" name="q2" value="5" required> 5</label>
                    <label><input type="radio" name="q2" value="4"> 4</label>
                    <label><input type="radio" name="q2" value="3"> 3</label>
                    <label><input type="radio" name="q2" value="2"> 2</label>
                    <label><input type="radio" name="q2" value="1"> 1</label>
                </div>
            </div>

            <button type="submit" class="btn-submit">Submit Evaluation</button>
        </form>
    </div>
</body>
</html>