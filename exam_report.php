<?php
include('header.php');
include('db.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$exam_id = isset($_GET['id']) ? $_GET['id'] : null;

if ($exam_id) {
    $stmtExams = $conn->prepare("SELECT * FROM exams_tbl WHERE exam_id = ?");
    $stmtExams->execute([$exam_id]);
    $selExams = $stmtExams->fetch(PDO::FETCH_ASSOC);

    if ($selExams) {
        $selExamTimeLimit = $selExams['ex_time_limit'];
        $exDisplayLimit = $selExams['ex_questlimit_display'];
        
        $stmtQuest = $conn->prepare("SELECT * FROM exam_question_tbl WHERE exam_id = ? ORDER BY RAND() LIMIT ?");
        $stmtQuest->execute([$exam_id, $exDisplayLimit]);
        $questions = $stmtQuest->fetchAll(PDO::FETCH_ASSOC);
    } else {
        die("Exam not found.");
    }
} else {
    die("Exam ID not provided.");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($selExams['ex_title']); ?> - Exam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <a href="exam_details.php?exam_id=<?= $exam['id'] ?>">View Details</a>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 50px;
        }
        .question-container {
            margin-bottom: 20px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .question-number {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .options {
            margin-left: 20px;
        }
        .option-label {
            margin-bottom: 5px;
        }
        .submit-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4"><?php echo htmlspecialchars($selExams['ex_title']); ?></h1>
        <p class="text-center mb-4"><?php echo htmlspecialchars($selExams['ex_description']); ?></p>
        <div class="row">
            <div class="col-md-12">
                <form method="post" id="submitAnswerFrm">
                    <input type="hidden" name="exam_id" id="exam_id" value="<?php echo htmlspecialchars($exam_id); ?>">
                    <input type="hidden" name="examAction" id="examAction">
                    <?php
                    if (!empty($questions)) {
                        $i = 1;
                        foreach ($questions as $question) { ?>
                            <div class="question-container">
                                <div class="question-number">Question <?php echo $i++; ?></div>
                                <div class="question-text">
                                    <?php echo htmlspecialchars($question['exam_question']); ?>
                                </div>
                                <div class="options">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[<?php echo $question['eqt_id']; ?>]" id="option1_<?php echo $question['eqt_id']; ?>" value="<?php echo htmlspecialchars($question['exam_ch1']); ?>">
                                        <label class="form-check-label" for="option1_<?php echo $question['eqt_id']; ?>">
                                            <?php echo htmlspecialchars($question['exam_ch1']); ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[<?php echo $question['eqt_id']; ?>]" id="option2_<?php echo $question['eqt_id']; ?>" value="<?php echo htmlspecialchars($question['exam_ch2']); ?>">
                                        <label class="form-check-label" for="option2_<?php echo $question['eqt_id']; ?>">
                                            <?php echo htmlspecialchars($question['exam_ch2']); ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[<?php echo $question['eqt_id']; ?>]" id="option3_<?php echo $question['eqt_id']; ?>" value="<?php echo htmlspecialchars($question['exam_ch3']); ?>">
                                        <label class="form-check-label" for="option3_<?php echo $question['eqt_id']; ?>">
                                            <?php echo htmlspecialchars($question['exam_ch3']); ?>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="answer[<?php echo $question['eqt_id']; ?>]" id="option4_<?php echo $question['eqt_id']; ?>" value="<?php echo htmlspecialchars($question['exam_ch4']); ?>">
                                        <label class="form-check-label" for="option4_<?php echo $question['eqt_id']; ?>">
                                            <?php echo htmlspecialchars($question['exam_ch4']); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        <?php }
                    } else { ?>
                        <p>No questions available for this exam.</p>
                    <?php } ?>
                    <div class="text-center">
                        <button type="submit" class="btn btn-primary submit-btn">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
<?php include('footer.php'); ?>

kkk
<?php
session_start();
include('db.php'); // Include your database connection file

// Check if the user is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Retrieve courses for which the user has taken exams
$sql = "SELECT course_name, grade FROM exams e
        JOIN courses ON course_id = course_id
        WHERE user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Error handling for prepare statement failure
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$result = $stmt->execute();

if (!$result) {
    // Error handling for execution failure
    die('Execution failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();

// Check if there are any courses
if ($result && $result->num_rows > 0) {
    // Display the exam report
    echo "<h2>Exam Report</h2>";
    echo "<table class='table table-bordered'>";
    echo "<thead><tr><th>Course Name</th><th>Grade</th></tr></thead>";
    echo "<tbody>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['course_name']) . "</td>";
        echo "<td>" . htmlspecialchars($row['grade']) . "</td>";
        echo "</tr>";
    }
    echo "</tbody>";
    echo "</table>";
} else {
    // No courses found
    echo "<p>No courses found.</p>";
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>
kkkkkkkkkkkkk
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
include('db.php');

$course_id = $_GET['course_id'];
$user_id = $_SESSION['user_id'];

// Fetch the grade (assuming you have a grade calculation logic)
$sql = "SELECT grade FROM grades WHERE student_id = ? AND course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$grade = $row['grade'];

include('header.php');
?>
<h2>Exam Result</h2>
<p>Your grade: <?php echo htmlspecialchars($grade); ?></p>
<?php include('footer.php'); ?>

kkkk
<?php

include('db.php'); // Include your database connection file


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Retrieve courses for which the user has taken exams
$sql = "SELECT c.course_name, e.grade FROM exams e
        JOIN courses c ON e.course_id = course_id
        WHERE e.user_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    // Error handling for prepare statement failure
    die('Prepare failed: ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("i", $user_id);
$result = $stmt->execute();

if (!$result) {
    // Error handling for execution failure
    die('Execution failed: ' . htmlspecialchars($stmt->error));
}

$result = $stmt->get_result();

// Check if there are any courses
if ($result && $result->num_rows > 0) {
    // Display the exam report
    echo "<h2>Exam Report</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Course Name</th><th>Grade</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>".$row['course_name']."</td>";
        echo "<td>".$row['grade']."</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    // No courses found
    echo "No courses found.";
}

// Close the statement and database connection
$stmt->close();
$conn->close();
?>


kkkk


<?php
include('db.php');

// Fetch all courses
$courses_sql = "SELECT id, title FROM courses";
$courses_result = $conn->query($courses_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Report</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 20px;
        }
        .report-card {
            background: #fff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2, h3 {
            color: #343a40;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-5 mb-4">Exam Report</h1>
        <div class="row">
            <div class="col-md-12">
                <?php
                if ($courses_result->num_rows > 0) {
                    while ($course = $courses_result->fetch_assoc()) {
                        echo "<div class='report-card'>";
                        echo "<h2>Course: " . $course['title'] . "</h2>";
                        
                        include('db.php'); // Include the file where your database connection is established
                        
                        // Check if the user is logged in
                        if (!isset($_SESSION['user_id'])) {
                            header("Location: login.php");
                            exit;
                        }
                        
                        // Get the user ID from the session
                        $user_id = $_SESSION['user_id'];
                        
                        // Prepare the SQL query
                        $sql = "SELECT c.course_name, e.grade 
                                FROM exams e 
                                JOIN courses c ON e.course_id = c.course_id 
                                WHERE e.user_id = ?";
                        $stmt = $conn->prepare($sql);
                        
                        // Check for prepare statement failure
                        if (!$stmt) {
                            die('Prepare failed: ' . htmlspecialchars($conn->error));
                        }
                        
                        // Bind parameters
                        $stmt->bind_param("i", $user_id);
                        
                        // Execute the statement
                        $result = $stmt->execute();
                        
                        // Check for execution failure
                        if (!$result) {
                            die('Execution failed: ' . htmlspecialchars($stmt->error));
                        }
                        
                        // Get the result set
                        $result = $stmt->get_result();
                        
                        // Check if there are any courses
                        if ($result && $result->num_rows > 0) {
                            // Display the exam report
                            echo "<h2>Exam Report</h2>";
                            echo "<table>";
                            echo "<tr><th>Course Name</th><th>Grade</th></tr>";
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row['course_name'] . "</td>";
                                echo "<td>" . $row['grade'] . "</td>";
                                echo "</tr>";
                            }
                            echo "</table>";
                        } else {
                            // No courses found
                            echo "No courses found.";
                        }
                        
                        // Close the statement and database connection
                        $stmt->close();
                        $conn->close();
                        
                        
                        // Fetch all exams for the current course
                        $exams_sql = "SELECT id, title, date FROM exams WHERE course_id = ?";
                        $exams_stmt = $conn->prepare($exams_sql);
                        $exams_stmt->bind_param('i', $course['id']);
                        $exams_stmt->execute();
                        $exams_result = $exams_stmt->get_result();

                        if ($exams_result->num_rows > 0) {
                            while ($exam = $exams_result->fetch_assoc()) {
                                echo "<h3>Exam: " . $exam['title'] . " (Date: " . $exam['date'] . ")</h3>";

                                // Fetch all students
                                $students_sql = "SELECT id, full_name FROM users WHERE role = 'student'";
                                $students_result = $conn->query($students_sql);

                                if ($students_result->num_rows > 0) {
                                    echo "<table class='table table-bordered'>";
                                    echo "<thead>";
                                    echo "<tr>";
                                    echo "<th>Student Name</th>";
                                    echo "<th>Score</th>";
                                    echo "<th>Status</th>";
                                    echo "</tr>";
                                    echo "</thead>";
                                    echo "<tbody>";

                                    while ($student = $students_result->fetch_assoc()) {
                                        // Check if the student took the exam
                                        $attempt_sql = "SELECT score FROM exam_attempts WHERE exam_id = ? AND user_id = ?";
                                        $attempt_stmt = $conn->prepare($attempt_sql);
                                        $attempt_stmt->bind_param('ii', $exam['id'], $student['id']);
                                        $attempt_stmt->execute();
                                        $attempt_result = $attempt_stmt->get_result();

                                        echo "<tr>";
                                        echo "<td>" . $student['full_name'] . "</td>";

                                        if ($attempt_result->num_rows > 0) {
                                            $attempt = $attempt_result->fetch_assoc();
                                            echo "<td>" . $attempt['score'] . "</td>";
                                            echo "<td>Completed</td>";
                                        } else {
                                            echo "<td>N/A</td>";
                                            echo "<td>Missed</td>";
                                        }

                                        echo "</tr>";
                                    }

                                    echo "</tbody>";
                                    echo "</table>";
                                } else {
                                    echo "<p>No students found.</p>";
                                }
                            }
                        } else {
                            echo "<p>No exams found for this course.</p>";
                        }

                        echo "</div>";
                    }
                } else {
                    echo "<p>No courses found.</p>";
                }
                ?>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
