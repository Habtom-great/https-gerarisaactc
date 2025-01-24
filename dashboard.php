

<?php
session_start();
include 'db.php'; // Include the database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['login'])) {
        // Handle login
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        
        // Prepare and execute SQL query for login
        $sql = "SELECT * FROM users WHERE email = :email AND role = :role";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':role', $role);
        $stmt->execute();
        
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Successful login
            $_SESSION['user'] = $user;
            header('Location: admin_dashboard.php'); // Redirect to dashboard or appropriate page
        } else {
            // Login error
            $login_error = 'Invalid email, password, or role.';
        }
    } elseif (isset($_POST['register'])) {
        // Handle registration
        $username = $_POST['register_username'];
        $first_name = $_POST['register_first_name'];
        $last_name = $_POST['register_last_name'];
        $email = $_POST['register_email'];
        $password = password_hash($_POST['register_password'], PASSWORD_DEFAULT);
        
        // Prepare and execute SQL query for registration
        $sql = "INSERT INTO users (username, first_name, last_name, email, password) VALUES (:username, :first_name, :last_name, :email, :password)";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':first_name', $first_name);
        $stmt->bindValue(':last_name', $last_name);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':password', $password);
        
        if ($stmt->execute()) {
            // Successful registration
            $register_success = 'Registration successful. You can now log in.';
        } else {
            // Registration error
            $register_error = 'An error occurred. Please try again.';
        }
    }
}
?>


<?php

include('db.php');
include('header_loggedin.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$user_name = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Unknown User';
$user_first_name = isset($_SESSION['user_first_name']) ? $_SESSION['user_first_name'] : 'user_First Name';
$user_last_name = isset($_SESSION['user_last_name']) ? $_SESSION['user_last_name'] : 'user_Last Name';

// Fetch available courses
$courses_query = "SELECT course_title, course_id, description FROM courses";
$courses_stmt = $pdo->prepare($courses_query);
$courses_stmt->execute();
$courses = $courses_stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available exams
$exams_query = "SELECT exam_id, exam_type, description FROM exams"; // Change `name` to the correct column name
$exams_stmt = $pdo->prepare($exams_query);
$exams_stmt->execute();
$exams = $exams_stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .dashboard-container h1 {
            text-align: center;
            color: #007bff;
            margin-bottom: 30px;
        }
        .list-group-item {
            font-size: 1.1rem;
        }
        .list-group-item strong {
            color: #343a40;
        }
        .list-group-item p {
            margin: 5px 0;
        }
        .btn-take-exam {
            margin-top: 10px;
            display: inline-block;
        }
        .exam-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .exam-item div {
            flex-grow: 1;
        }
        .table-header {
            font-weight: bold;
            margin-top: 20px;
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="dashboard-container">
            <h1>Welcome to Your Dashboard, <?php echo htmlspecialchars($full_name); ?></h1>
            <p><strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?></p>
            <p><strong>First Name:</strong> <?php echo htmlspecialchars($user_first_name); ?></p>
            <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user_last_name); ?></p>

            <h2 class="mt-4">Available Courses</h2>
            <div class="table-header">
                <span>ID</span>
                <span>Description</span>
                <span>Action</span>
            </div>
            <div class="list-group">
                <?php if ($courses): ?>
                    <?php foreach ($courses as $course): ?>
                        <a href="courses.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" class="list-group-item list-group-item-action">
                            <strong><?php echo htmlspecialchars($course['course_id']); ?></strong>
                            <p><?php echo htmlspecialchars($course['description']); ?></p>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="list-group-item">No courses available.</p>
                <?php endif; ?>
            </div>

            <h2 class="mt-4">Available Exams</h2>
            <div class="table-header">
                <span>ID</span>
                <span>Description</span>
                <span>Action</span>
            </div>
            <div class="list-group">
                <?php if ($exams): ?>
                    <?php foreach ($exams as $exam): ?>
                        <div class="list-group-item exam-item">
                            <div>
                                <strong><?php echo htmlspecialchars($exam['exam_id']); ?></strong>
                                <p><?php echo htmlspecialchars($exam['description']); ?></p>
                            </div>
                            <a href="exam_questions.php?exam_id=<?php echo htmlspecialchars($exam['exam_id']); ?>" class="btn btn-primary btn-take-exam">Take Exam</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="list-group-item">No exams available.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
<?php include('footer.php'); ?>
