
<?php

include('db.php');
include 'header.php';
session_start();
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

// Fetch all exams
$stmt = $pdo->prepare("SELECT * FROM exams");
$stmt->execute();
$exams = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
        .exam-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-size: 2rem;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            font-weight: 500;
        }
        .table thead th {
            background-color: #007bff;
            color: #ffffff;
        }
        .table td {
            vertical-align: middle;
        }
        .table td a {
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }
        .table td a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="welcome-container">
        <h3>Welcome to the Exam Selection Page , <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
    </div>
    <div class="exam-container">
        <h2>Select an Exam</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <?php if (empty($exams)): ?>
            <p class="text-center">No exams available.</p>
        <?php else: ?>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course ID</th>
                        <th>Course Name</th>
                        <th>Course Description</th>
                        <th>Exam ID</th>
                        <th>Exam Type</th>
                        <th>Exam Description</th>
                        
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($exams as $exam): ?>
                        <tr>
                        <td><?= htmlspecialchars($exam['exam_id'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($exam['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($exam['description'] ?? 'No Description') ?></td>
                            <td><?= htmlspecialchars($exam['exam_id'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($exam['name'] ?? 'N/A') ?></td>
                            <td><?= htmlspecialchars($exam['description'] ?? 'No Description') ?></td>
                            <td><a href="examss.php?exam_id=<?= htmlspecialchars($exam['id'] ?? '') ?>">Start Exam</a></td>
                         
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<script>
    // Function to display the current time
    function updateTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Update time every second
    setInterval(updateTime, 1000);
    // Initial call to display the time immediately
    updateTime();
</script>

<?php include('footer.php'); ?>

</body>
</html>


kkkkkkkk
<?php
include('db.php');
include 'header.php';
session_start();
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

// Fetch courses with associated exams
$query = "SELECT courses.course_id, courses.course_title, exams.exam_id, exams.exam_type, exams.description 
          FROM courses
          LEFT JOIN exams ON courses.course_id = exams.course_id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$exams_by_course = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $exams_by_course[$row['course_title']][] = $row;
}
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
        .exam-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-size: 2rem;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            font-weight: 500;
        }
        .table thead th {
            background-color: #007bff;
            color: #ffffff;
        }
        .table td {
            vertical-align: middle;
        }
        .table td a {
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }
        .table td a:hover {
            text-decoration: underline;
        }
        .course-title {
            margin-top: 30px;
            color: #343a40;
            font-size: 1.5rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="welcome-container mb-4">
        <h3>Welcome to the Exam Selection Page, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
    </div>
    <div class="exam-container">
        <h2>Select an Exam</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <?php if (empty($exams_by_course)): ?>
            <p class="text-center">No exams available.</p>
        <?php else: ?>
            <?php foreach ($exams_by_course as $course_name => $exams): ?>
                <div class="course-section">
                    <h3 class="course-title"><?= htmlspecialchars($course_name) ?></h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Exam Type</th>
                                <th>Description</th>
                                <th>View</th>
                                <th>Add</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exam['exam_id']) ?></td>
                                    <td><?= htmlspecialchars($exam['exam_type']) ?></td>
                                    <td><?= htmlspecialchars($exam['description'] ?? 'No Description') ?></td>
                                    <td><a href="view_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>">View</a></td>
                                    <td><a href="add_exam.php?course_id=<?= htmlspecialchars($exam['course_id']) ?>">Add</a></td>
                                    <td><a href="edit_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>">Edit</a></td>
                                    <td><a href="remove_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>">Remove</a></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    function updateTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    }

    setInterval(updateTime, 1000);
    updateTime();
</script>

<?php include('footer.php'); ?>

</body>
</html>


kkkkkkkkkkk
<?php
include('db.php');


if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $time_limit = $_POST['time_limit'];

    try {
        $stmt = $pdo->prepare("INSERT INTO exams (name, time_limit) VALUES (:name, :time_limit)");
        $stmt->execute(['name' => $name, 'time_limit' => $time_limit]);
        $message = "Exam created successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

include('header.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: Arial, sans-serif;
        }
        .dashboard-container {
            margin-top: 50px;
        }
        .form-control {
            margin-bottom: 15px;
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

<div class="container dashboard-container">
    <h1>Admin Dashboard</h1>
    <?php if (isset($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>
    <form method="post">
        <div class="form-group">
            <label for="name">Exam Name:</label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="form-group">
            <label for="time_limit">Time Limit (minutes):</label>
            <input type="number" class="form-control" name="time_limit" required>
        </div>
        <button type="submit" class="btn btn-primary">Create Exam</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>

kkkkkkkkkkkkk


<?php
include('db.php');
include 'header.php';

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

// Fetch courses with associated exams
$query = "SELECT courses.course_id, courses.course_title, exams.exam_id, exams.exam_type, exams.description 
          FROM courses
          LEFT JOIN exams ON courses.course_id = exams.course_id";
$stmt = $pdo->prepare($query);
$stmt->execute();
$exams_by_course = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $exams_by_course[$row['course_title']][] = $row;
}
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
        .exam-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #007bff;
            font-size: 2rem;
        }
        .error-message {
            color: #dc3545;
            text-align: center;
            font-weight: 500;
        }
        .table thead th {
            background-color: #007bff;
            color: #ffffff;
        }
        .table td {
            vertical-align: middle;
        }
        .table td a {
            text-decoration: none;
            color: #007bff;
            font-weight: 600;
        }
        .table td a:hover {
            text-decoration: underline;
        }
        .course-title {
            margin-top: 30px;
            color: #343a40;
            font-size: 1.5rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="welcome-container mb-4">
        <h3>Welcome to the Exam Selection Page, <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
    </div>
    <div class="exam-container">
        <h2>Select an Exam</h2>
        <?php if (isset($error_message)): ?>
            <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        <?php endif; ?>
        <?php if (empty($exams_by_course)): ?>
            <p class="text-center">No exams available.</p>
        <?php else: ?>
            <?php foreach ($exams_by_course as $course_name => $exams): ?>
                <div class="course-section">
                    <h3 class="course-title"><?= htmlspecialchars($course_name) ?></h3>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Exam Type</th>
                                <th>Description</th>
                                <th>View</th>
                                <th>Add</th>
                                <th>Edit</th>
                                <th>Remove</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($exams as $exam): ?>
                                <tr>
                                    <td><?= htmlspecialchars($exam['exam_id']) ?></td>
                                    <td><?= htmlspecialchars($exam['exam_type']) ?></td>
                                    <td><?= htmlspecialchars($exam['description'] ?? 'No Description') ?></td>
                                    <td><a href="view_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>">View</a></td>
                                    <td><a href="add_question.php?course_id=<?= htmlspecialchars($exam['course_id']) ?>">Add</a></td>
                                    <td><a href="edit_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>">Edit</a></td>
                                    <td><a href="remove_exam.php?exam_id=<?= htmlspecialchars($exam['exam_id']) ?>">Remove</a></td>
                                </tr>
                                
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
    // Function to display the current time
    function updateTime() {
        const now = new Date();
        const hours = now.getHours().toString().padStart(2, '0');
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const seconds = now.getSeconds().toString().padStart(2, '0');
        document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Update time every second
    setInterval(updateTime, 1000);
    // Initial call to display the time immediately
    updateTime();
</script>

<?php include('footer.php'); ?>

</body>
</html>


kkkkkkkkkkkkkk
