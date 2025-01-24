<?php
include('header_loggedin.php');
require 'vendor/autoload.php'; // PhpSpreadsheet and PHPWord for Excel and Word processing

use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory; // Aliased for PhpSpreadsheet
use PhpOffice\PhpWord\IOFactory as WordIOFactory; // Aliased for PhpWord

// Ensure only admins can upload exams
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Fetch all courses for dropdown
try {
    $stmt = $pdo->prepare("SELECT course_id, course_title FROM courses");
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}

// Handle form submission and file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exam_name = $_POST['exam_name'] ?? '';
    $exam_type = $_POST['exam_type'] ?? '';
    $course_id = $_POST['course_id'] ?? '';
    $uploadDir = 'uploads/'; // Directory where uploaded files will be stored
    $uploadedDate = date('Y-m-d H:i:s'); // Get the current date and time for the uploaded date

    try {
        // Insert exam into the database (with uploaded date)
        $stmt = $pdo->prepare("INSERT INTO exams (course_name, exam_type, course_id, uploaded_date) VALUES (:course_name, :exam_type, :course_id, :uploaded_date)");
        $stmt->bindParam(':course_name', $exam_name);
        $stmt->bindParam(':exam_type', $exam_type);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':uploaded_date', $uploadedDate);
        $stmt->execute();

        // Get the ID of the newly inserted exam
        $exam_id = $pdo->lastInsertId();

        // Handle file upload
        if (isset($_FILES['question_file']) && $_FILES['question_file']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['question_file']['tmp_name'];
            $fileName = $_FILES['question_file']['name'];
            $fileType = pathinfo($fileName, PATHINFO_EXTENSION);
            $newFileName = time() . '_' . $fileName;
            $destination = $uploadDir . $newFileName;

            // Move the uploaded file to the server's destination directory
            if (move_uploaded_file($fileTmpPath, $destination)) {
                // Update the exam record with the file path
                $stmt = $pdo->prepare("UPDATE exams SET file_path = :file_path WHERE exam_id = :exam_id");
                $stmt->bindParam(':file_path', $newFileName);
                $stmt->bindParam(':exam_id', $exam_id);
                $stmt->execute();

                echo "<p class='alert alert-success'>File uploaded successfully!</p>";
            } else {
                echo "<p class='alert alert-danger'>Error moving the uploaded file.</p>";
            }
        }

        echo "<p class='alert alert-success'>Exam added successfully!</p>";
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
            <label for="question_file">Upload Questions File (Excel, Word):</label>
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
