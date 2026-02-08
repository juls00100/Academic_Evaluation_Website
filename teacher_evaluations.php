<?php
include 'db.php';
session_start();

// 1. SECURITY CHECK
if (!isset($_SESSION['u_id']) || $_SESSION['u_type'] !== 'Teacher') {
    header('Location: login.php');
    exit;
}

$u_id = $_SESSION['u_id'];

// 2. FETCH TEACHER DATA 
$teacher_query = "SELECT t_id FROM tbl_teachers WHERE t_user_id = ?";
$stmt = $conn->prepare($teacher_query);
$stmt->bind_param("i", $u_id);
$stmt->execute();
$teacher_result = $stmt->get_result();

if ($teacher_result->num_rows === 0) {
    die("<div style='color:white; text-align:center; padding:50px;'>Error: Your User ID ($u_id) is not linked to a Teacher Profile.</div>");
}

$teacher = $teacher_result->fetch_assoc();
$actual_t_id = $teacher['t_id'];

// --- NEW: FETCH ALL QUESTIONS FIRST (To prevent empty labels) ---
$all_questions = [];
$q_query = "SELECT * FROM tbl_questions ORDER BY q_category, q_id";
$q_res = $conn->query($q_query);
while($q_row = $q_res->fetch_assoc()) {
    $all_questions[$q_row['q_id']] = $q_row;
}

// 3. FETCH EVALUATIONS & CALCULATE
$eval_query = "SELECT e.e_id, ea.q_id, ea.rating, c.c_name as course_name, 
                      CONCAT(u.u_first_name, ' ', u.u_last_name) as student_name
               FROM tbl_evaluations e
               JOIN tbl_evaluation_answers ea ON e.e_id = ea.e_id
               LEFT JOIN tbl_courses c ON e.c_id = c.c_id
               JOIN tbl_user u ON e.u_id = u.u_id
               WHERE e.t_id = ? AND e.e_status = 'approved'
               ORDER BY e.e_id DESC";

$stmt = $conn->prepare($eval_query);
$stmt->bind_param("i", $actual_t_id);
$stmt->execute();
$eval_result = $stmt->get_result();

$all_evaluations = [];
$question_totals = [];
$question_counts = [];
$student_scores = [];

while ($row = $eval_result->fetch_assoc()) {
    $eid = $row['e_id'];
    $qid = $row['q_id'];
    $rating = (int)$row['rating'];

    // Track totals for the criteria breakdown
    if (!isset($question_totals[$qid])) {
        $question_totals[$qid] = 0;
        $question_counts[$qid] = 0;
    }
    $question_totals[$qid] += $rating;
    $question_counts[$qid]++;

    // Group by evaluation ID for the Student Feedback Log
    if (!isset($student_scores[$eid])) {
        $student_scores[$eid] = [
            'student_name' => $row['student_name'],
            'course_name' => $row['course_name'] ?? 'N/A',
            'ratings' => []
        ];
    }
    $student_scores[$eid]['ratings'][] = $rating;
}

// --- NEW: CALCULATE ACTUAL AVERAGES ---
$question_averages = [];
foreach ($question_totals as $qid => $total) {
    $question_averages[$qid] = round($total / $question_counts[$qid], 2);
}

// Reformat for the table display
foreach ($student_scores as $eval) {
    $eval['responses'] = $eval['ratings']; 
    $all_evaluations[] = $eval;
}

$overall_average = 0;
if (count($question_averages) > 0) {
    $overall_average = round(array_sum($question_averages) / count($question_averages), 2);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Results | Faculty Evaluation</title>
    <style>
        :root {
            --primary: #C5B358; /* Gold */
            --bg: #001a33;      /* Deep Blue */
            --card-bg: rgba(255, 255, 255, 0.08);
        }

        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background-color: var(--bg); 
            color: #f4f4f4; 
            margin: 0; padding: 0; 
        }

        .container { width: 95%; max-width: 1000px; margin: 40px auto; }

        /* HEADER SECTION */
        .header-box { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        h1 { color: var(--primary); margin: 0; font-size: 1.8rem; }

        /* TOP STATS CARDS */
        .stats-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); 
            gap: 20px; margin-bottom: 40px; 
        }
        .stat-card { 
            background: var(--card-bg); 
            padding: 25px; border-radius: 12px; 
            border-bottom: 4px solid var(--primary); 
            text-align: center; 
        }
        .stat-value { font-size: 2.2rem; color: var(--primary); font-weight: 800; }
        .stat-label { font-size: 0.9rem; text-transform: uppercase; letter-spacing: 1px; opacity: 0.8; }

        /* CATEGORY BADGE */
        .category-header { 
            background: #2a4d69; color: white;
            padding: 8px 15px; border-radius: 5px;
            font-size: 0.85rem; font-weight: bold;
            margin: 30px 0 10px 0; display: inline-block;
        }

        /* QUESTION ROWS */
        .question-row { 
            background: var(--card-bg); 
            padding: 20px; margin-bottom: 10px; 
            border-radius: 8px; display: flex; 
            justify-content: space-between; align-items: center;
        }
        .question-text { flex: 1; padding-right: 20px; }
        
        /* PROGRESS BAR STYLING */
        .score-box { width: 150px; text-align: right; }
        .progress-bg { background: #333; height: 8px; border-radius: 10px; margin-top: 5px; overflow: hidden; }
        .progress-fill { background: var(--primary); height: 100%; border-radius: 10px; }

        /* TABLE STYLING */
        .table-container { background: var(--card-bg); border-radius: 12px; overflow: hidden; margin-top: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background: rgba(197, 179, 88, 0.2); color: var(--primary); padding: 15px; text-align: left; }
        td { padding: 15px; border-bottom: 1px solid rgba(255,255,255,0.05); }

        .back-btn { 
            text-decoration: none; color: var(--primary); 
            border: 1px solid var(--primary); padding: 10px 20px; 
            border-radius: 6px; transition: 0.3s;
        }
        .back-btn:hover { background: var(--primary); color: var(--bg); }

        @media (max-width: 600px) {
            .question-row { flex-direction: column; align-items: flex-start; }
            .score-box { width: 100%; margin-top: 15px; text-align: left; }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-box">
        <h1>Evaluation Analytics</h1>
        <a href="teacher_dash.php" class="back-btn">Dashboard</a>
    </div>

    <?php if (count($all_evaluations) > 0): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $overall_average; ?></div>
                <div class="stat-label">Overall Rating</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($all_evaluations); ?></div>
                <div class="stat-label">Total Submissions</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?php echo count($all_questions); ?></div>
                <div class="stat-label">Criteria Measured</div>
            </div>
        </div>

        <h2 style="color: var(--primary); font-size: 1.2rem;">Detailed Criteria Breakdown</h2>

        <?php 
        $current_cat = '';
        foreach ($all_questions as $q_id => $q): 
            if ($current_cat !== $q['q_category']): 
                $current_cat = $q['q_category'];
                echo "<div class='category-header'>" . strtoupper(htmlspecialchars($current_cat)) . "</div>";
            endif;

            $avg = $question_averages[$q_id] ?? 0;
            $percent = ($avg / 5) * 100;
        ?>
            <div class="question-row">
                <div class="question-text">
                    <?php echo htmlspecialchars($q['q_text']); ?>
                </div>
                <div class="score-box">
                    <strong><?php echo $avg; ?> / 5.0</strong>
                    <div class="progress-bg">
                        <div class="progress-fill" style="width: <?php echo $percent; ?>%;"></div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <h2 style="color: var(--primary); font-size: 1.2rem; margin-top: 50px;">Student Feedback Log</h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th style="text-align: right;">Average Score</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($all_evaluations as $eval): 
                        $score = !empty($eval['responses']) ? round(array_sum($eval['responses']) / count($eval['responses']), 2) : 0;
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($eval['student_name']); ?></td>
                        <td><?php echo htmlspecialchars($eval['course_name'] ?? 'N/A'); ?></td>
                        <td style="text-align: right; color: var(--primary); font-weight: bold;">
                            <?php echo $score; ?> / 5
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <div class="stat-card">
            <p>No evaluation data available yet.</p>
        </div>
    <?php endif; ?>
</div>

</body>
</html>