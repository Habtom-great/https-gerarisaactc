
<?php
include('db.php');
include('header_loggedin.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $score = 0;
    $correct_answers = 0;

    // Fetch correct answers from the database
    $sql = "SELECT id, answer FROM exam_questions";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $correct_answers_data = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    // Fetch all questions
    $sql = "SELECT id, question, option_a, option_b, option_c, option_d FROM exam_questions";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $questions_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($_POST)) {
        echo "<h1>No answers submitted.</h1>";
        exit;
    }

    $user_answers = [];
    $answers_summary = [];

    foreach ($_POST as $question_id => $user_answer) {
        if (strpos($question_id, 'answer_') === 0) {
            $id = str_replace('answer_', '', $question_id);

            if (isset($correct_answers_data[$id])) {
                $correct_answer = $correct_answers_data[$id];
                $is_correct = $correct_answer == $user_answer;
                if ($is_correct) {
                    $correct_answers++;
                }

                $answers_summary[$id] = [
                    'user_answer' => $user_answer,
                    'correct_answer' => $correct_answer,
                    'is_correct' => $is_correct
                ];
            }
        }
    }

    // Calculate the score as a percentage out of 100
    $total_questions = count($correct_answers_data);
    $score = ($correct_answers / $total_questions) * 100;

    // Determine the result category
    $result_category = 'Fail';
    $color_class = 'fail';
    if ($score >= 90) {
        $result_category = 'Excellent';
        $color_class = 'excellent';
    } elseif ($score >= 75) {
        $result_category = 'Very Good';
        $color_class = 'very-good';
    } elseif ($score >= 65) {
        $result_category = 'Good';
        $color_class = 'good';
    } elseif ($score >= 50) {
        $result_category = 'Pass';
        $color_class = 'pass';
    }

    // Insert results into the database if user is logged in
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
    if ($user_id) {
        $stmt = $pdo->prepare("INSERT INTO exam_result (user_id, score, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$user_id, $score]);
    }

    // Display the result
    echo "<div class='container mt-5'>";
    echo "<div class='card'>";
    echo "<div class='card-body'>";
    echo "<h5 class='card-title'>Exam Summary</h5>";
    echo "<h5>User Full Name: " . htmlspecialchars($_SESSION['full_name']) . "</h5>";
    echo "<h5>User ID: " . htmlspecialchars($user_id) . "</h5>";
    echo "<h5>Your score: " . $correct_answers . " out of " . $total_questions . " (" . round($score, 2) . "%)</h5>";
    echo "<h5 class='$color_class'>Result: " . htmlspecialchars($result_category) . "</h5>";

    // List the user's answers and correct answers
    echo "<h5>Answers Summary:</h5>";
    echo "<table class='table table-striped table-bordered'>
            <thead>
                <tr>
                    <th>Question ID</th>
                    <th>Question</th>
                    <th>Your Answer</th>
                    <th>Correct Answer</th>
                    <th>Result</th>
                </tr>
            </thead>
            <tbody>";

    foreach ($questions_data as $question) {
        $id = $question['id'];
        $summary = $answers_summary[$id] ?? [
            'user_answer' => 'Not Answered',
            'correct_answer' => $correct_answers_data[$id] ?? 'N/A',
            'is_correct' => false
        ];

        $result = $summary['is_correct'] ? 'Correct' : 'Incorrect';
        $result_class = $summary['is_correct'] ? 'correct' : 'incorrect';

        echo "<tr>
                <td>" . htmlspecialchars($id) . "</td>
                <td>" . htmlspecialchars($question['question']) . "</td>
                <td>" . htmlspecialchars($summary['user_answer']) . "</td>
                <td>" . htmlspecialchars($summary['correct_answer']) . "</td>
                <td class='$result_class'>" . htmlspecialchars($result) . "</td>
              </tr>";
    }

    echo "  </tbody>
          </table>";

    echo "<a href='dashboard.php' class='btn btn-primary'>Back to Dashboard</a>";
    echo "</div></div></div>";
} else {
    echo "<h1>Invalid request method.</h1>";
}
?>

<style>
.fail {
    color: #dc3545; /* Red */
}

.pass {
    color: #28a745; /* Green */
}

.good {
    color: #ffc107; /* Yellow */
}

.very-good {
    color: #007bff; /* Blue */
}

.excellent {
    color: #6f42c1; /* Purple */
}

.correct {
    color: #28a745; /* Green */
}

.incorrect {
    color: #dc3545; /* Red */
}

.table-striped tbody tr:nth-of-type(odd) {
    background-color: #f2f2f2;
}

.table-bordered th, .table-bordered td {
    border: 1px solid #dee2e6;
}
</style>

<?php include('footer.php'); ?>


