<?php
// Include the database connection file
require_once 'db.php';
require_once 'header_loggedin.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
// Fetch comments from the database (assuming the table is 'comments')
try {
    $sql = "SELECT * FROM comments";  // Replace with your actual query
    $stmt = $pdo->query($sql);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);  // Fetch all comments
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}

// Ensure comments is set and is an array
if (!isset($comments) || !is_array($comments)) {
    $comments = [];  // Initialize as an empty array to avoid errors
}
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
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
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
        .button-container {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .button-container .btn {
            width: 150px;
            margin-bottom: 15px;
        }
        .admin-comments {
            padding: 20px;
        }
        .admin-comments table {
            width: 100%;
            border-collapse: collapse;
        }
        .admin-comments table, .admin-comments th, .admin-comments td {
            border: 1px solid #ddd;
        }
        .admin-comments th, .admin-comments td {
            padding: 10px;
            text-align: left;
        }
        .admin-comments th {
            background-color: #f4f4f4;
        }
    </style>
</head>
<body>
<?php
// Include database connection and header files
require_once 'db.php';
require_once 'header_loggedin.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- FontAwesome for Icons -->
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            margin-top: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .title-section h1 {
            color:rgb(94, 94, 99);
            font-size: 2.5rem;
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
        }
        .accordion .card-header {
            background-color:rgba(65, 65, 68, 0.93);
            color: #fff;
        }
        .accordion .card-header button {
            color: #fff;
            text-decoration: none;
        }
        .accordion .card-body a {
            margin: 5px;
        }
    </style>
</head>
<body>

<div class="container dashboard-container">
    <div class="title-section">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="accordion" id="adminDashboard">
        <!-- Courses Management -->
        <div class="card">
            <div class="card-header" id="headingCourses">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseCourses" aria-expanded="true" aria-controls="collapseCourses">
                        <i class="fas fa-book"></i> Courses Management
                    </button>
                </h2>
            </div>
            <div id="collapseCourses" class="collapse show" aria-labelledby="headingCourses" data-parent="#adminDashboard">
                <div class="card-body">
                    <a href="add_courses.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add Course</a>
                    <a href="update_course.php" class="btn btn-warning"><i class="fas fa-edit"></i> Update Course</a>
                    <a href="remove_course.php" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Remove Course</a>
                    <a href="courses.php" class="btn btn-info"><i class="fas fa-eye"></i> View Courses</a>
                </div>
            </div>
        </div>

        <!-- Users Management -->
        <div class="card">
            <div class="card-header" id="headingUsers">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseUsers" aria-expanded="false" aria-controls="collapseUsers">
                        <i class="fas fa-user"></i> Users Management
                    </button>
                </h2>
            </div>
            <div id="collapseUsers" class="collapse" aria-labelledby="headingUsers" data-parent="#adminDashboard">
                <div class="card-body">
                    <a href="add_user.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add User</a>
                    <a href="edit_user.php" class="btn btn-warning"><i class="fas fa-user-edit"></i> Update User</a>
                    <a href="delete_user.php" class="btn btn-danger"><i class="fas fa-user-times"></i> Remove User</a>
                    <a href="list_users.php" class="btn btn-info"><i class="fas fa-users"></i> View Users</a>
                </div>
            </div>
        </div>

        <!-- Staff Management -->
        <div class="card">
            <div class="card-header" id="headingStaff">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseStaff" aria-expanded="false" aria-controls="collapseStaff">
                        <i class="fas fa-user-tie"></i> Staff Management
                    </button>
                </h2>
            </div>
            <div id="collapseStaff" class="collapse" aria-labelledby="headingStaff" data-parent="#adminDashboard">
                <div class="card-body">
                    <a href="add_staff.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add Staff</a>
                    <a href="update_staff.php" class="btn btn-warning"><i class="fas fa-edit"></i> Update Staff</a>
                    <a href="remove_staff.php" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Remove Staff</a>
                    <a href="staff_list.php" class="btn btn-info"><i class="fas fa-eye"></i> View Staff</a>
                </div>
            </div>
        </div>
<!-- tutor Management -->
<div class="card">
            <div class="card-header" id="headingTutor">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseTutor" aria-expanded="false" aria-controls="collapseStaff">
                        <i class="fas fa-user-tie"></i> Tutor Management
                    </button>
                </h2>
            </div>
            <div id="collapseTutor" class="collapse" aria-labelledby="headingTutor" data-parent="#adminDashboard">
                <div class="card-body">
                    <a href="add_tutor.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add Tutor</a>
                    <a href="update_tutor.php" class="btn btn-warning"><i class="fas fa-edit"></i> Update Tutor</a>
                    <a href="remove_tutor.php" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Remove Tutor</a>
                    <a href="tutors_list.php" class="btn btn-info"><i class="fas fa-eye"></i> View Tutor</a>
                </div>
            </div>
        </div>
        <!-- Students Management -->
        <div class="card">
            <div class="card-header" id="headingStudents">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseStudents" aria-expanded="false" aria-controls="collapseStudents">
                        <i class="fas fa-user-graduate"></i> Students Management
                    </button>
                </h2>
            </div>
            <div id="collapseStudents" class="collapse" aria-labelledby="headingStudents" data-parent="#adminDashboard">
                <div class="card-body">
                    <a href="add_student.php" class="btn btn-primary"><i class="fas fa-user-plus"></i> Add Student</a>
                    <a href="edit_student.php" class="btn btn-warning"><i class="fas fa-user-edit"></i> Update Student</a>
                    <a href="delete_student.php" class="btn btn-danger"><i class="fas fa-user-times"></i> Remove Student</a>
                    <a href="list_students.php" class="btn btn-info"><i class="fas fa-users"></i> View Students</a>
                </div>
            </div>
        </div>

        <!-- Exams Management -->
        <div class="card">
            <div class="card-header" id="headingExams">
                <h2 class="mb-0">
                    <button class="btn btn-link btn-block text-left" type="button" data-toggle="collapse" data-target="#collapseExams" aria-expanded="false" aria-controls="collapseExams">
                        <i class="fas fa-file-alt"></i> Exams Management
                    </button>
                </h2>
            </div>
            <div id="collapseExams" class="collapse" aria-labelledby="headingExams" data-parent="#adminDashboard">
                <div class="card-body">
                    <a href="add_exam.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> Add Exam</a>
                    <a href="exam_selection.php" class="btn btn-primary"><i class="fas fa-plus-circle"></i> exam_selection</a>
                    
                    <a href="update_exam.php" class="btn btn-warning"><i class="fas fa-edit"></i> Update Exam</a>
                    <a href="remove_exam.php" class="btn btn-danger"><i class="fas fa-trash-alt"></i> Remove Exam</a>
                    <a href="exam_questions.php" class="btn btn-info"><i class="fas fa-eye"></i> View Exams</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Footer -->
<?php include('footer.php'); ?>

<!-- JS Scripts -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    kkkkkkkkk
<div class="container dashboard-container">
    <div class="title-section">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="button-container">
        <!-- Courses Management -->
        <a href="add_courses.php" class="btn btn-primary">Add Course</a>
        <a href="update_course.php" class="btn btn-warning">Update Course</a>
        <a href="remove_course.php" class="btn btn-danger">Remove Course</a>
        <a href="courses.php" class="btn btn-info">View Courses</a>

        <!-- Users Management -->
        <a href="add_user.php" class="btn btn-primary">Add User</a>
        <a href="edit_user.php" class="btn btn-warning">Update User</a>
        <a href="delete_user.php" class="btn btn-danger">Remove User</a>
        <a href="list_users.php" class="btn btn-info">View Users</a>

        <!-- Staff Management -->
        <a href="add_staff.php" class="btn btn-primary">Add Staff</a>
        <a href="update_staff.php" class="btn btn-warning">Update Staff</a>
        <a href="remove_staff.php" class="btn btn-danger">Remove Staff</a>
        <a href="list_staff.php" class="btn btn-info">View Staff</a>

        <!-- Students Management -->
        <a href="register.php" class="btn btn-primary">Add Student</a>
        <a href="update_student.php" class="btn btn-warning">Update Student</a>
        <a href="remove_student.php" class="btn btn-danger">Remove Student</a>
        <a href="list_students.php" class="btn btn-info">View Students</a>

        <!-- Exams Management -->
        <a href="add_exam.php" class="btn btn-primary">Add Exam</a>
        <a href="update_exam.php" class="btn btn-warning">Update Exam</a>
        <a href="remove_exam.php" class="btn btn-danger">Remove Exam</a>
        <a href="exam_selection.php" class="btn btn-info">View Exams</a>

        <!-- Payment Status -->
        <a href="update_payment.php" class="btn btn-success">Update Payment Status</a>
  
    <a href="course_access_request.php" class="btn btn-success">course_access_request</a>
    <a href="access_user_account.php" class="btn btn-success">Access User Account</a>

   
    <a href="exam_report.php" class="btn btn-success">exam_report</a>
    <div class="container dashboard-container">
    <div class="title-section">
       

<!-- Include FontAwesome for Icons -->
<script src="https://kit.fontawesome.com/a076d05399.js"></script>

    </div>
    </div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('footer.php'); ?>

</body>
</html>



kkkkk
<?php

include('header.php');
include('db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Welcome to Admin Dashboard</h1>
    <a href="add_course.php">Add Course</a>
    <!-- Other admin links and content -->
    <?php
include('db.php');

// Check if 'id' is set in the URL
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Prepare and execute delete query
    $sql = "DELETE FROM courses WHERE video_id = :video_id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':video_id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: admin_dashboard.php'); // Redirect back to admin dashboard
        exit();
    } else {
        print_r($stmt->errorInfo());
    }
} else {
    echo 'No ID specified for deletion.';
}
?>
<?php
// Include database connection and admin header
include('db.php');
include('admin-header.php'); // Ensure this file exists in the same directory

// Get the video ID from the URL, fallback to 0 if not present
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Prepare and execute query to fetch course details
$sql = "SELECT * FROM courses WHERE video_id = :video_id"; 
$stmt = $conn->prepare($sql);
$stmt->bindParam(':video_id', $id, PDO::PARAM_INT);
if ($stmt->execute()) {
    $course = $stmt->fetch();
} else {
    print_r($stmt->errorInfo());
}

// Check if course exists
if (!$course) {
    die('Course not found');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Your head content here -->
</head>
<body>

<!-- Admin Dashboard Content -->

<!-- Example Delete Link -->
<a href="delete.php?id=<?php echo htmlspecialchars($course['video_id']); ?>" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>

<!-- Your other content -->

</body>
</html>

<?php

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

?>

<a href="add_course.php">Add Course</a>
    <div class="adminpanel">
       
        <p>You can manage User and Online Exam from here.......</p>

        
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="courses.php">Courses</a></li>
            <li class="nav-item">
            <a class="nav-link" href="students_list.php">Students</a>
            <a class="nav-link" href="students.php">Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="exams.php">Exams</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="exams_result.php">Exams Result</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_dashboard.php">Users(Students)</a>
        </li>
       
            <li><a href="students.php">Users(Students)</a></li>
            <li><a href="user_dashboard.php">Users(Students)</a></li>
            <li><a href="exams.php">Exam</a></li>
            <li><a href="grades.php">Grade Reports</a></li>
            <li><a href="exam_result.php">Exam Result </a></li>
            <li><a href="exam_report.php">Exam Reports</a></li>
            <li><a href="messages.php">Messages</a></li>
            <li><a href="settings.php">Settings</a></li>
            <li><a href="logout.php">Logout</a></li>
        </ul>
    </div>
 
</head>
<body>
   
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<?php include('footer.php'); ?>
kkkk

<?php
include('header.php');
include('db.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

?>

<!-- Admin Dashboard HTML -->
<div class="container mt-5">
    <h1>Admin Dashboard</h1>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" href="#home">Home</a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link" href="courses.php">Courses</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="students_list.php">Students</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="exams.php">Exams</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="messages.php">Messages</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="settings.php">Settings</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="#user_dashboard.php">Users</a>
        </li>
       
        <li class="nav-item">
            <a class="nav-link" href="#exam_reports.php">Exam Reports</a>
        </li>
        
        <li class="nav-item">
            <a class="nav-link" href="#exam_results.php">Exam Result Reports</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">Logout</a>
        </li>
    </ul>

    <!-- Tab content -->
    <div class="tab-content mt-3">
        <div class="tab-pane fade show active" id="home">
            <h2>Home</h2>
            <!-- Add home content here -->
        </div>
        <div class="tab-pane fade" id="courses">
            <h2>Courses</h2>
            <!-- Add courses management interface here -->
        </div>
        <div class="tab-pane fade" id="users">
            <h2>Users</h2>
            <!-- Add user management interface here -->
        </div>
        <div class="tab-pane fade" id="reports">
            <h2>Reports</h2>
            <!-- Add reports interface here -->
        </div>
        <div class="tab-pane fade" id="messages">
            <h2>Messages</h2>
            <!-- Add messaging interface here -->
        </div>
        <div class="tab-pane fade" id="settings">
            <h2>Settings</h2>
            <!-- Add settings interface here -->
        </div>
    </div>
</div>

<?php include('footer.php'); ?>

