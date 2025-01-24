<?php
include('header_loggedin.php');
require 'vendor/autoload.php'; // Autoload PhpSpreadsheet and PHPWord

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all courses to populate the course selection dropdown
try {
    $stmt = $pdo->prepare("SELECT course_id, course_title FROM courses");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_name = $_POST['exam_name'] ?? '';
    $exam_type = $_POST['exam_type'] ?? '';
    $course_id = $_POST['course_id'] ?? '';
    $exam_date = $_POST['exam_date'] ?? '';

    try {
        // Insert the new exam into the database
        $stmt = $pdo->prepare("INSERT INTO exams (course_name, exam_type, course_id, exam_date) VALUES (:course_name, :exam_type, :course_id, :exam_date)");
        $stmt->bindParam(':exam_name', $exam_name);
        $stmt->bindParam(':exam_type', $exam_type);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':exam_date', $exam_date);
        $stmt->execute();

        // Get the newly created exam ID
        $exam_id = $pdo->lastInsertId();

        echo "<p class='alert alert-success'>Exam added successfully!</p>";

        // Handle file upload
        if (isset($_FILES['question_file']) && $_FILES['question_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['question_file']['tmp_name'];
            $fileName = $_FILES['question_file']['name'];
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);

            // Process Excel files
            if ($fileType === 'xlsx' || $fileType === 'xls') {
                $spreadsheet = IOFactory::load($fileTmpPath);
                $sheet = $spreadsheet->getActiveSheet();
                foreach ($sheet->getRowIterator() as $row) {
                    $cells = $row->getCellIterator();
                    $questionData = [];
                    foreach ($cells as $cell) {
                        $questionData[] = $cell->getValue();
                    }
                    if (count($questionData) >= 2) {
                        $stmt = $pdo->prepare("INSERT INTO exam_questions (exam_id, question_text, correct_answer) VALUES (:exam_id, :question_text, :correct_answer)");
                        $stmt->bindParam(':exam_id', $exam_id);
                        $stmt->bindParam(':question_text', $questionData[0]);
                        $stmt->bindParam(':correct_answer', $questionData[1]);
                        $stmt->execute();
                    }
                }
                echo "<p class='alert alert-success'>Questions imported successfully from Excel!</p>";
            }

            // Process Word files
            elseif ($fileType === 'docx') {
                $phpWord = WordIOFactory::load($fileTmpPath);
                $text = '';
                foreach ($phpWord->getSections() as $section) {
                    foreach ($section->getElements() as $element) {
                        if (method_exists($element, 'getText')) {
                            $text .= $element->getText() . "\n";
                        }
                    }
                }
                $questions = explode("\n", $text);
                foreach ($questions as $questionLine) {
                    list($questionText, $correctAnswer) = explode('|', $questionLine);
                    if (!empty($questionText) && !empty($correctAnswer)) {
                        $stmt = $pdo->prepare("INSERT INTO exam_questions (exam_id, question_text, correct_answer) VALUES (:exam_id, :question_text, :correct_answer)");
                        $stmt->bindParam(':exam_id', $exam_id);
                        $stmt->bindParam(':question_text', trim($questionText));
                        $stmt->bindParam(':correct_answer', trim($correctAnswer));
                        $stmt->execute();
                    }
                }
                echo "<p class='alert alert-success'>Questions imported successfully from Word!</p>";
            }
        }
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
    <title>Add Exam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-container {
            margin-top: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<div class="container form-container">
    <h2>Add Exam</h2>
    <form action="add_exam.php" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="exam_name">Exam Name:</label>
            <input type="text" class="form-control" id="exam_name" name="exam_name" required>
        </div>
        <div class="form-group">
            <label for="exam_type">Exam Type:</label>
            <select class="form-control" id="exam_type" name="exam_type" required>
                <option value="Quiz">Quiz</option>
                <option value="Midterm">Midterm Exam</option>
                <option value="Final">Final Exam</option>
            </select>
        </div>
        <div class="form-group">
            <label for="course_id">Course Name:</label>
            <select class="form-control" id="course_id" name="course_id" required>
                <?php foreach ($courses as $course): ?>
                    <option value="<?= htmlspecialchars($course['course_id']) ?>">
                        <?= htmlspecialchars($course['course_title']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="exam_date">Exam Date:</label>
            <input type="date" class="form-control" id="exam_date" name="exam_date" required>
        </div>

        <div class="form-group">
            <label for="question_file">Upload Questions File (Excel or Word):</label>
            <input type="file" class="form-control-file" id="question_file" name="question_file" accept=".xlsx,.xls,.docx" required>
        </div>

        <button type="submit" class="btn btn-primary">Add Exam</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>

