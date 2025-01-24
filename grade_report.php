<?php
session_start();
include('db.php');

// Fetch and display exam grades for the logged-in student
$user_id = $_SESSION['user_id'];
$sql = "SELECT courses.title AS course_title, exams.title AS exam_title, exams.questions, user_answers.answers 
        FROM exams 
        INNER JOIN user_answers ON exams.id = user_answers.exam_id 
        INNER JOIN courses ON exams.course_id = courses.id 
        WHERE user_answers.user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

include('templates/header.php');
?>
<h2>Exam Grade Report</h2>
<table class="table">
    <thead>
        <tr>
            <th>Course</th>
            <th>Exam Title</th>
            <th>Grade</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['course_title']); ?></td>
                <td><?php echo htmlspecialchars($row['exam_title']); ?></td>
                <td><?php echo calculateGrade($row['questions'], $row['answers']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php include('templates/footer.php'); ?>

<?php
function calculateGrade($questions, $answers) {
    // Implement your grading logic here
    // This function should return the calculated grade
}
?>

-------------------------------

<?php
session_start();
include('db.php');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header('Location: login.php');
    exit();
}

// Fetch all courses
$courses_sql = "SELECT id, title FROM courses";
$courses_result = $conn->query($courses_sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Report - Online Accounting Course</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Exam Report</h1>
        <div class="row">
            <div class="col-md-12">
                <?php if ($courses_result->num_rows > 0): ?>
                    <form method="GET" action="exam_report.php">
                        <div class="form-group">
                            <label for="course_id">Select Course</label>
                            <select class="form-control" id="course_id" name="course_id">
                                <?php while ($course = $courses_result->fetch_assoc()): ?>
                                    <option value="<?php echo $course['id']; ?>"><?php echo $course['title']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">View Report</button>
                    </form>
                <?php else: ?>
                    <p>No courses available.</p>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['course_id'])): ?>
            <?php
            $course_id = $_GET['course_id'];

            // Fetch exams for the selected course
            $exams_sql = "SELECT id, title FROM exams WHERE course_id = ?";
            $exams_stmt = $conn->prepare($exams_sql);
            $exams_stmt->bind_param('i', $course_id);
            $exams_stmt->execute();
            $exams_result = $exams_stmt->get_result();
            ?>

            <div class="row mt-5">
                <div class="col-md-12">
                    <?php if ($exams_result->num_rows > 0): ?>
                        <?php while ($exam = $exams_result->fetch_assoc()): ?>
                            <h2><?php echo $exam['title']; ?></h2>
                            <?php
                            // Fetch exam attempts for the selected exam
                            $attempts_sql = "SELECT u.full_name, ea.score, ea.attempt_date 
                                             FROM exam_attempts ea 
                                             JOIN users u ON ea.user_id = u.id 
                                             WHERE ea.exam_id = ?";
                            $attempts_stmt = $conn->prepare($attempts_sql);
                            $attempts_stmt->bind_param('i', $exam['id']);
                            $attempts_stmt->execute();
                            $attempts_result = $attempts_stmt->get_result();
                            ?>

                            <?php if ($attempts_result->num_rows > 0): ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Score</th>
                                            <th>Attempt Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($attempt = $attempts_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo $attempt['full_name']; ?></td>
                                                <td><?php echo $attempt['score']; ?></td>
                                                <td><?php echo $attempt['attempt_date']; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <p>No exam attempts found.</p>
                            <?php endif; ?>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p>No exams found for this course.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

