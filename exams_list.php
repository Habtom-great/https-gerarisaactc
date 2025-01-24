

kkkkkkkkkk
<?php
include('db.php');
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('header_loggedin.php');

// Fetch all exams
$stmt = $pdo->prepare("SELECT * FROM exams");
$stmt->execute();
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($exams === false) {
    echo "<p class='error-message'>Failed to retrieve exams. Please try again later.</p>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Exams</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .exams-container {
            margin-top: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-section h1 {
            color: #007bff;
            font-size: 2.5rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            display: inline-block;
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
        }
    </style>
</head>
<body>

<div class="container exams-container">
    <div class="title-section">
        <h1>Exams Management</h1>
    </div>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Course</th>
                <th>Exam Name</th>
                <th>Description</th>
                <th>Exam Type</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($exams): ?>
                <?php foreach ($exams as $exam): ?>
                    <tr>
                        <td><?= htmlspecialchars($exam['exam_id']) ?></td>
                        <td>
                            <?php 
                                $courseStmt = $pdo->prepare("SELECT course_title FROM courses WHERE course_id = ?");
                                $courseStmt->execute([$exam['course_id']]);
                                $course = $courseStmt->fetch(PDO::FETCH_ASSOC);
                                echo htmlspecialchars($course['course_title'] ?? 'Unknown Course');
                            ?>
                        </td>
                        <td><?= htmlspecialchars($exam['exam_name'] ?? 'No Name') ?></td>
                        <td><?= htmlspecialchars($exam['description'] ?? 'No Description') ?></td>
                        <td><?= htmlspecialchars($exam['exam_type'] ?? 'Unknown Type') ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No exams found</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>


kkkkkkkkkkkkkkkk

<?php
include('db.php');


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch available exams from the database
$stmt = $pdo->query("SELECT * FROM exams");
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Debugging: Check the structure of the fetched data
echo '<pre>';
print_r($exams);
echo '</pre>';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Selection</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .container {
            margin-top: 50px;
        }
        .exam-box {
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        .exam-box h3 {
            margin-bottom: 10px;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
        .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Select an Exam</h2>
    <?php if ($exams): ?>
        <?php foreach ($exams as $exam): ?>
            <div class="exam-box">
                <h3><?= !empty($exam['name']) ? htmlspecialchars($exam['name']) : 'Unnamed Exam' ?></h3>
                <p><?= !empty($exam['description']) ? htmlspecialchars($exam['description']) : 'No description available.' ?></p>
                <a href="exam_selection.php?exam_id=<?= $exam['exam_id'] ?>" class="btn btn-primary">Start Exam</a>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No exams available.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>
<?php include('footer.php'); ?>

kkkk
<?php
include('db.php');
include('header.php');

try {
    $sql = "SELECT exam_id, question, option_a, option_b, option_c, option_d, correct_answer FROM exam_questions";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        echo "No questions found.";
    } else {
        ?>
        <div id="quiz-container">
            <?php
            foreach ($questions as $index => $question) {
                ?>
                <div class="question-container" id="question_<?php echo $question['exam_id']; ?>" <?php if ($index !== 0) echo 'style="display: none;"'; ?>>
                    <h2>Question <?php echo $index + 1; ?></h2>
                    <p><?php echo htmlspecialchars($question['question']); ?></p>
                    <form id="form_<?php echo $question['exam_id']; ?>" onsubmit="return false;">
                        <input type="hidden" id="correct_answer_<?php echo $question['exam_id']; ?>" value="<?php echo $question['correct_answer']; ?>">
                        <label><input type="radio" name="answer_<?php echo $question['exam_id']; ?>" value="A"> <?php echo htmlspecialchars($question['option_a']); ?></label><br>
                        <label><input type="radio" name="answer_<?php echo $question['exam_id']; ?>" value="B"> <?php echo htmlspecialchars($question['option_b']); ?></label><br>
                        <label><input type="radio" name="answer_<?php echo $question['exam_id']; ?>" value="C"> <?php echo htmlspecialchars($question['option_c']); ?></label><br>
                        <label><input type="radio" name="answer_<?php echo $question['exam_id']; ?>" value="D"> <?php echo htmlspecialchars($question['option_d']); ?></label><br>
                        <button type="button" onclick="checkAnswer(<?php echo $question['exam_id']; ?>)">Submit Answer</button>
                        <div id="result_<?php echo $question['exam_id']; ?>"></div>
                    </form>
                    <hr>
                </div>
                <?php
            }
            ?>
            <div id="result-container" style="display: none;">
                <h2>Quiz Results</h2>
                <div id="quiz-results"></div>
            </div>
            <div id="navigation-buttons">
                <button onclick="previousQuestion()" id="prevButton" style="display: none;">Previous</button>
                <button onclick="nextQuestion()" id="nextButton">Next</button>
                <button onclick="showResults()" id="resultButton" style="display: none;">Show Results</button>
            </div>
        </div>
        <?php
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>

<script>
let currentQuestion = 0;
let totalQuestions = <?php echo count($questions); ?>;

function nextQuestion() {
    if (currentQuestion < totalQuestions - 1) {
        document.getElementById('question_' + currentQuestion).style.display = 'none';
        currentQuestion++;
        document.getElementById('question_' + currentQuestion).style.display = 'block';
        resetFeedback(currentQuestion);
    }
    updateNavigationButtons();
}

function previousQuestion() {
    if (currentQuestion > 0) {
        document.getElementById('question_' + currentQuestion).style.display = 'none';
        currentQuestion--;
        document.getElementById('question_' + currentQuestion).style.display = 'block';
        resetFeedback(currentQuestion);
    }
    updateNavigationButtons();
}

function checkAnswer(questionId) {
    var selectedAnswer = document.querySelector('input[name="answer_' + questionId + '"]:checked');
    var correctAnswer = document.getElementById('correct_answer_' + questionId).value;
    var resultDiv = document.getElementById('result_' + questionId);

    if (selectedAnswer) {
        var selectedValue = selectedAnswer.value;
        if (selectedValue === correctAnswer) {
            resultDiv.innerHTML = "<span class='feedback correct-answer'>Correct Answer!</span>";
        } else {
            resultDiv.innerHTML = "<span class='feedback incorrect-answer'>Incorrect. Correct Answer is " + correctAnswer + "</span>";
        }
    } else {
        resultDiv.innerHTML = "<span class='feedback error'>Please select an answer.</span>";
    }
}

function resetFeedback(questionIndex) {
    document.getElementById('result_' + questionIndex).innerHTML = '';
}

function updateNavigationButtons() {
    if (currentQuestion === 0) {
        document.getElementById('prevButton').style.display = 'none';
    } else {
        document.getElementById('prevButton').style.display = 'inline-block';
    }

    if (currentQuestion === totalQuestions - 1) {
        document.getElementById('nextButton').style.display = 'none';
        document.getElementById('resultButton').style.display = 'inline-block';
    } else {
        document.getElementById('nextButton').style.display = 'inline-block';
        document.getElementById('resultButton').style.display = 'none';
    }
}

function showResults() {
    document.getElementById('quiz-container').style.display = 'none';
    document.getElementById('result-container').style.display = 'block';

    let resultsHTML = '';
    for (let i = 0; i < totalQuestions; i++) {
        let questionId = <?php echo $questions[$i]['exam_id']; ?>;
        let correctAnswer = '<?php echo $questions[$i]['correct_answer']; ?>';
        let selectedAnswer = document.querySelector('input[name="answer_' + questionId + '"]:checked');
        let selectedValue = selectedAnswer ? selectedAnswer.value : '';

        resultsHTML += "<div><strong>Question " + (i + 1) + ": </strong>" + (selectedValue === correctAnswer ? "<span class='correct-answer'>Correct</span>" : "<span class='incorrect-answer'>Incorrect</span>") + "</div>";
    }
    document.getElementById('quiz-results').innerHTML = resultsHTML;
}

updateNavigationButtons();
</script>

<style>
.question-container {
    margin-bottom: 20px;
    border: 1px solid #ccc;
    padding: 10px;
    border-radius: 5px;
    background-color: #f9f9f9;
}

.question-container h2 {
    font-size: 1.2em;
    margin-bottom: 10px;
}

.question-container p {
    margin-bottom: 10px;
}

.question-container form {
    margin-bottom: 10px;
}

.question-container label {
    display: block;
    margin-bottom: 5px;
}

.question-container button {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 8px 16px;
    cursor: pointer;
}

.question-container button:hover {
    background-color: #0056b3;
}

.feedback {
    margin-top: 10px;
    display: block;
    font-weight: bold;
}

.correct-answer {
    color: green;
}

.incorrect-answer {
    color: red;
}

.error {
    color: orange;
}

#result-container {
    margin-top: 20px;
}

#navigation-buttons {
    margin-top: 20px;
}

#navigation-buttons button {
    margin-right: 10px;
}
</style>

<?php include('footer.php'); ?>


kkkk
<?php
include('db.php');
include('header.php');

// Display timer countdown script here

try {
    $sql = "SELECT question, option_a, option_b, option_c, option_d, correct_answer FROM exam_questions";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        echo "No questions found.";
    } else {
        foreach ($questions as $question) {
            echo "Question: " . htmlspecialchars($question['question']) . "<br>";
            echo "Option A: <input type='radio' name='answer_" . $question['question'] . "' value='A'>" . htmlspecialchars($question['option_a']) . "<br>";
            echo "Option B: <input type='radio' name='answer_" . $question['question'] . "' value='B'>" . htmlspecialchars($question['option_b']) . "<br>";
            echo "Option C: <input type='radio' name='answer_" . $question['question'] . "' value='C'>" . htmlspecialchars($question['option_c']) . "<br>";
            echo "Option D: <input type='radio' name='answer_" . $question['question'] . "' value='D'>" . htmlspecialchars($question['option_d']) . "<br>";
            echo "<button onclick='checkAnswer(\"" . $question['correct_answer'] . "\", \"" . $question['question'] . "\")'>Check Answer</button>";
            echo "<div id='result_" . $question['question'] . "'></div><br><br>";
        }
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>

<script>
// Function to check the selected answer
function checkAnswer(correctAnswer, questionId) {
    var selectedAnswer = document.querySelector('input[name="answer_' + question+ '"]:checked');
    if (selectedAnswer) {
        var selectedValue = selectedAnswer.value;
        var resultDiv = document.getElementById('result_' + question);
        if (selectedValue === correctAnswer) {
            resultDiv.innerHTML = "<span style='color:green;'>Correct Answer!</span>";
        } else {
            resultDiv.innerHTML = "<span style='color:red;'>Incorrect. Correct Answer is " + correctAnswer + "</span>";
        }
    } else {
        alert("Please select an answer.");
    }
}
</script>

<?php include('footer.php'); ?>

kkkk
<?php

include('db.php');
include('header.php');
try {
    $sql = "SELECT question, option_a, option_b, option_c, option_d, correct_answer FROM exam_questions";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$questions) {
        echo "No questions found.";
    } else {
        foreach ($questions as $question) {
            echo "Question: " . htmlspecialchars($question['question']) . "<br>";
            echo "Option A: " . htmlspecialchars($question['option_a']) . "<br>";
            echo "Option B: " . htmlspecialchars($question['option_b']) . "<br>";
            echo "Option C: " . htmlspecialchars($question['option_c']) . "<br>";
            echo "Option D: " . htmlspecialchars($question['option_d']) . "<br>";
            echo "correct_answer: " . htmlspecialchars($question['correct_answer']) . "<br><br>";
        }
    }
} catch (PDOException $e) {
    echo "Query failed: " . $e->getMessage();
}
?>
<?php include('footer.php'); ?>