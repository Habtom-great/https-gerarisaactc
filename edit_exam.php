<?php
include('header_loggedin.php');

// Fetch the specific exam by ID
if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM exams WHERE exam_id = :exam_id");
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exam) {
            echo 'Exam not found.';
            exit();
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}

// Handle form submission for updating exam
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_name = $_POST['exam_name'];
    $exam_type = $_POST['exam_type'];
    $exam_date = $_POST['exam_date'];

    try {
        $stmt = $pdo->prepare("UPDATE exams SET course_name = :course_name, exam_type = :exam_type, exam_date = :exam_date WHERE exam_id = :exam_id");
        $stmt->bindParam(':course_name', $exam_name);
        $stmt->bindParam(':exam_type', $exam_type);
        $stmt->bindParam(':exam_date', $exam_date);
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();

        echo "<p class='alert alert-success'>Exam updated successfully!</p>";
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Exam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>Edit Exam</h2>
    <form action="edit_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>" method="POST">
        <div class="form-group">
            <label for="exam_name">Exam Name:</label>
            <input type="text" class="form-control" id="exam_name" name="exam_name" value="<?= htmlspecialchars($exam['course_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="exam_type">Exam Type:</label>
            <select class="form-control" id="exam_type" name="exam_type" required>
                <option value="Quiz" <?= $exam['exam_type'] === 'Quiz' ? 'selected' : '' ?>>Quiz</option>
                <option value="Midterm" <?= $exam['exam_type'] === 'Midterm' ? 'selected' : '' ?>>Midterm</option>
                <option value="Final" <?= $exam['exam_type'] === 'Final' ? 'selected' : '' ?>>Final</option>
            </select>
        </div>
        <div class="form-group">
            <label for="exam_date">Exam Date:</label>
            <input type="date" class="form-control" id="exam_date" name="exam_date" value="<?= htmlspecialchars($exam['exam_date']) ?>" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Exam</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>


kkkkkkkkkkkk
<?php
session_start();
include('db.php');
include('header_loggedin.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Number of questions per page
$questions_per_page =15;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $questions_per_page;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Store answers in the session
    foreach ($_POST as $key => $value) {
        if (strpos($key, 'answer_') === 0) {
            $_SESSION['answers'][$key] = $value;
        }
    }
    
    if (isset($_POST['submit'])) {
        header("Location: submit_exam.php");
        exit;
    } else {
        header("Location: exam_questions.php?page=" . ($page + 1));
        exit;
    }
}

try {
    // Fetch the total number of questions
    $total_questions_query = "SELECT COUNT(*) FROM exam_questions";
    $total_questions_stmt = $pdo->prepare($total_questions_query);
    $total_questions_stmt->execute();
    $total_questions = $total_questions_stmt->fetchColumn();

    // Fetch questions for the current page
    $sql = "SELECT id, question, option_a, option_b, option_c, option_d FROM exam_questions LIMIT :offset, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $questions_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate the total number of pages
    $total_pages = ceil($total_questions / $questions_per_page);

} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Questions</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .exam-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .exam-container h1 {
            text-align: center;
            color: #007bff;
        }
        .card {
            margin-bottom: 20px;
        }
        .card-title {
            font-size: 1.2rem;
        }
        .form-check-label {
            font-size: 1rem;
        }
        .timer {
            font-size: 18px;
            font-weight: bold;
            color: #ff0000;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="exam-container">
            <h1>Exam Questions - Page <?= $page ?> of <?= $total_pages ?></h1>
            <form method="post" action="submit_exam.php">
                <?php if ($questions): ?>
                    <?php foreach ($questions as $index => $question): ?>
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Question <?php echo ($offset + $index + 1); ?>:</h5>
                                <p class="card-text"><?php echo htmlspecialchars($question['question']); ?></p>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer_<?php echo $question['id']; ?>" value="A" required>
                                    <label class="form-check-label">A) <?php echo htmlspecialchars($question['option_a']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer_<?php echo $question['id']; ?>" value="B">
                                    <label class="form-check-label">B) <?php echo htmlspecialchars($question['option_b']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer_<?php echo $question['id']; ?>" value="C">
                                    <label class="form-check-label">C) <?php echo htmlspecialchars($question['option_c']); ?></label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answer_<?php echo $question['id']; ?>" value="D">
                                    <label class="form-check-label">D) <?php echo htmlspecialchars($question['option_d']); ?></label>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                    <div class="text-center">
                        <?php if ($page > 1): ?>
                            <a href="exam_questions.php?page=<?php echo ($page - 1); ?>" class="btn btn-secondary">Previous</a>
                        <?php endif; ?>
                        <?php if ($page < $total_pages): ?>
                            <button type="submit" name="next" value="next" class="btn btn-primary">Next</button>
                        <?php else: ?>
                            <button type="submit" name="submit" value="submit" class="btn btn-success">Submit Exam</button>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <p>No questions found.</p>
                <?php endif; ?>
            </form>
            <div id="timer" class="timer"></div>
        </div>
    </div>
    <script>
        let timeLeft = 1000; // 10 minutes in seconds
        const timerElement = document.getElementById('timer');

        function updateTimer() {
            if (timeLeft <= 0) {
                alert('Time is up!');
                document.querySelector('form').submit();
            } else {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;
                timerElement.textContent = `Time left: ${minutes}m ${seconds}s`;
                timeLeft--;
            }
        }

        updateTimer();
        setInterval(updateTimer, 1000);
    </script>
</body>
</html>
<?php include('footer.php'); ?>