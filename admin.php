<?php

include('db.php');

// Check if the user is an admin
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    header("Location: courses.php");
    exit();
}

// Handle form submission for adding/editing courses
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $title = $_POST['title'];
    $description = $_POST['description'];
    $video_url = $_POST['video_url'];
    $course_id = $_POST['course_id'] ?? null;

    if ($course_id) {
        // Update existing course
        $sql = "UPDATE courses SET title = ?, description = ?, video_url = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $title, $description, $video_url, $course_id);
    } else {
        // Insert new course
        $sql = "INSERT INTO courses (title, description, video_url) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $title, $description, $video_url);
    }
    if ($_SESSION['role'] !== 'admin') {
        header("Location: courses.php");
        exit();
    }
    if ($stmt->execute()) {
        echo "Course saved successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

include('header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Courses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Manage Courses</h2>
        <form method="POST" action="">
            <input type="hidden" name="course_id" value="<?php echo $course_id ?? ''; ?>">
            <div class="form-group">
                <label for="title">Course Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $title ?? ''; ?>" required>
            </div>
            <div class="form-group">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" required><?php echo $description ?? ''; ?></textarea>
            </div>
            <div class="form-group">
                <label for="video_url">Video URL</label>
                <input type="url" class="form-control" id="video_url" name="video_url" value="<?php echo $video_url ?? ''; ?>">
            </div>
            <button type="submit" class="btn btn-primary">Save Course</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php
include('footer.php');

?>
