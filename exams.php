<?php 
session_start();
include('db.php'); // Include database connection
include('header_loggedin.php'); // Include the header for logged-in users

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id']; // Current user's ID

try {
    // Check if the user has already taken the exam
    $stmt = $pdo->prepare("SELECT * FROM exam_attempts WHERE id = ?");
    $stmt->execute([$userId]);
    $attempt = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($attempt) {
        // Redirect to results page if the exam has already been attempted
        header('Location: exam_results.php');
        exit;
    }

    // Fetch all questions for the exam
    $stmt = $pdo->prepare("SELECT exam_id, question, option_a, option_b, option_c, option_d, Answer FROM exam_questions");
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Pagination setup
    $questionsPerPage = 5;
    $totalQuestions = count($questions);
    $totalPages = ceil($totalQuestions / $questionsPerPage);
    $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $startIndex = ($currentPage - 1) * $questionsPerPage;
    $currentQuestions = array_slice($questions, $startIndex, $questionsPerPage);
    
    // Display questions or show error if no questions available
    if (!$currentQuestions) {
        echo "<div class='alert alert-danger text-center'>No questions found.</div>";
    } else {
        ?>
        <div class="container mt-5">
            <h2 class="text-center text-primary">Online Exam</h2>
            <div id="timer" class="text-danger text-center mb-4">Time Remaining: <span id="time">20:00</span></div>

            <form method="POST" action="exam_results.php">
                <?php foreach ($currentQuestions as $index => $question): ?>
                    <div class="question-block">
                        <h4 class="text-info">Question <?php echo $startIndex + $index + 1; ?>:</h4>
                        <p><?php echo htmlspecialchars($question['question']); ?></p>
                        <input type="hidden" name="correct_answer_<?php echo $startIndex + $index; ?>" value="<?php echo $question['Answer']; ?>">
                        <div class="form-check">
                            <input type="radio" name="answer_<?php echo $startIndex + $index; ?>" value="A" class="form-check-input">
                            <label class="form-check-label">A) <?php echo htmlspecialchars($question['option_a']); ?></label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="answer_<?php echo $startIndex + $index; ?>" value="B" class="form-check-input">
                            <label class="form-check-label">B) <?php echo htmlspecialchars($question['option_b']); ?></label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="answer_<?php echo $startIndex + $index; ?>" value="C" class="form-check-input">
                            <label class="form-check-label">C) <?php echo htmlspecialchars($question['option_c']); ?></label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="answer_<?php echo $startIndex + $index; ?>" value="D" class="form-check-input">
                            <label class="form-check-label">D) <?php echo htmlspecialchars($question['option_d']); ?></label>
                        </div>
                        <hr>
                    </div>
                <?php endforeach; ?>

                <div class="text-center mt-4">
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?php echo $currentPage - 1; ?>" class="btn btn-secondary">Previous</a>
                    <?php endif; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?php echo $currentPage + 1; ?>" class="btn btn-secondary">Next</a>
                    <?php else: ?>
                        <button type="submit" class="btn btn-success">Submit Exam</button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Timer Script -->
        <script>
        let timerElement = document.getElementById('time');
        let time = 20 * 60; // 20 minutes in seconds

        function updateTimer() {
            let minutes = Math.floor(time / 60);
            let seconds = time % 60;
            timerElement.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
            time--;
            if (time < 0) {
                clearInterval(timerInterval);
                alert("Time's up!");
                document.forms[0].submit();
            }
        }

        let timerInterval = setInterval(updateTimer, 1000);
        </script>
        <?php
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
}

include('footer.php'); // Include footer
?>

kkkkkkkkkk
<?php

include('db.php'); // Database connection
include('header_loggedin.php'); // Header with navigation

try {
    $stmt = $pdo->prepare("SELECT id, course_title, question FROM exam_questions ORDER BY created_at DESC");
    $stmt->execute();
    $exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Debugging the fetched data
    if (empty($exams)) {
        error_log("No exams found in the database.");
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .exam-container {
            margin: 20px auto;
            max-width: 800px;
        }
        .exam-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        .exam-card .card-body {
            background-color: #f9f9f9;
        }
        .exam-card a {
            text-decoration: none;
            color: #007bff;
        }
        .exam-card a:hover {
            text-decoration: underline;
        }
        .no-exams {
            text-align: center;
            color: #888;
            font-style: italic;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="container exam-container">
    <h1 class="text-center">Available Exams</h1>

    <?php if (!empty($exams)): ?>
        <?php foreach ($exams as $exam): ?>
            <?php if (isset($exam['id'], $exam['title']) && !empty($exam['id']) && !empty($exam['title'])): ?>
                <div class="card exam-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= htmlspecialchars($exam['title']); ?></h5>
                        <p class="card-text">
                            <?= htmlspecialchars($exam['description'] ?? 'No description available.'); ?>
                        </p>
                        <a href="exam_questions.php?exam_id=<?= htmlspecialchars($exam['id']); ?>" class="btn btn-primary">
                            Take Exam
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <div class="alert alert-danger">
                    Invalid exam data. Please contact the administrator.
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-exams">No exams available at the moment.</p>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

