<?php
session_start();
include('db.php');
include('header_loggedin.php');

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $course_note = $_POST['course_note'] ?? '';
    $video_link = $_POST['course_videos'] ?? '';
    $tutor_name = $_POST['tutor_name'] ?? '';
    $tutor_image = $_POST['tutor_image'] ?? '';
    $thumb_image = $_POST['thumb_image'] ?? '';
    $instructor_id = $_SESSION['user_id'];

    try {
        // Insert the new course into the database
        $stmt = $pdo->prepare("INSERT INTO courses (course_title, description, course_note, course_videos, tutor_name, tutor_image, thumb_image, instructor_id) VALUES (:course_title, :description, :course_note, :video_link, :tutor_name, :tutor_image, :thumb_image, :instructor_id)");
        $stmt->bindParam(':course_title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':course_note', $course_note);
        $stmt->bindParam(':video_link', $video_link);
        $stmt->bindParam(':tutor_name', $tutor_name);
        $stmt->bindParam(':tutor_image', $tutor_image);
        $stmt->bindParam(':thumb_image', $thumb_image);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->execute();

        echo "<p class='alert alert-success'>Course added successfully!</p>";
    } catch (PDOException $e) {
        echo "<p class='alert alert-danger'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
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
    <h2>Add Course</h2>
    <form method="POST" action="add_course.php">
        <div class="form-group">
            <label for="title">Course Title:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="form-group">
            <label for="description">Course Description:</label>
            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="course_note">Course Note (Text):</label>
            <textarea class="form-control" id="course_note" name="course_note" rows="4" required></textarea>
        </div>
        <div class="form-group">
            <label for="video_link">Course Video Link:</label>
            <input type="url" class="form-control" id="video_link" name="video_link" required>
        </div>
        <div class="form-group">
            <label for="tutor_name">Tutor Name:</label>
            <input type="text" class="form-control" id="tutor_name" name="tutor_name" required>
        </div>
        <div class="form-group">
            <label for="tutor_image">Tutor Image :</label>
            <input type="url" class="form-control" id="tutor_image" name="tutor_image" required>
        </div>
        <div class="form-group">
            <label for="thumb_image">Thumbnail Image:</label>
            <input type="url" class="form-control" id="thumb_image" name="thumb_image" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>
