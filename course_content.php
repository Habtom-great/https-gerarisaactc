
<?php

include('db.php');

if (!isset($_GET['id'])) {
    die("Course ID not provided.");
}

$course_id = $_GET['id'];

// Fetch course details
$course_sql = "SELECT title, description FROM courses WHERE id = ?";
$course_stmt = $conn->prepare($course_sql);
$course_stmt->bind_param('i', $course_id);
$course_stmt->execute();
$course_result = $course_stmt->get_result();

if ($course_result->num_rows == 0) {
    die("Course not found.");
}

$course = $course_result->fetch_assoc();

// Fetch course content
$content_sql = "SELECT * FROM course_content WHERE course_id = ?";
$content_stmt = $conn->prepare($content_sql);
$content_stmt->bind_param('i', $course_id);
$content_stmt->execute();
$content_result = $content_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $course['title']; ?> - Course Content</title>
    <link rel="stylesheet" href="assets/css/styles.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4"><?php echo $course['title']; ?></h1>
        <p><?php echo $course['description']; ?></p>
        <div class="row">
            <?php if ($content_result->num_rows > 0): ?>
                <?php while ($content = $content_result->fetch_assoc()): ?>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo $content['title']; ?></h5>
                                <p class="card-text"><?php echo $content['description']; ?></p>
                                <?php if ($content['type'] == 'video'): ?>
                                    <video width="100%" controls>
                                        <source src="uploads/<?php echo $content['file_path']; ?>" type="video/mp4">
                                        Your browser does not support the video tag.
                                    </video>
                                <?php else: ?>
                                    <a href="uploads/<?php echo $content['file_path']; ?>" class="btn btn-primary">Download Content</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-md-12">
                    <p>No content available for this course.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
