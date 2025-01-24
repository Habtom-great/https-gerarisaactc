<?php
require_once 'db.php';
require_once 'header_loggedin.php';
// Database connection
$conn = new mysqli('localhost', 'root', '', 'accounting_course');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all courses for the sidebar with a limit
$limit = 5;  // Set the number of courses to display
$courses = [];
$result = $conn->query("SELECT course_id, course_title, course_subtitle FROM courses LIMIT $limit");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Fetch the current course details if `course_id` is provided in the URL
$current_course = null;
if (isset($_GET['course_id'])) {
    $course_id = (int)$_GET['course_id'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $current_course = $result->fetch_assoc();
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
    background-color: #0056b3; /* Professional blue tone */
    color: white;
    padding: 10px 0; /* Reduced height */
    text-align: center;
    font-size: 1.2rem; /* Professional font size */
    font-weight: bold;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Add subtle shadow for elegance */
              }

       footer {
    background-color: #0056b3; /* Match header color */
    color: white;
    padding: 8px 0; /* Reduced height */
    text-align: center;
    font-size: 0.9rem; /* Adjust font size */
    box-shadow: 0 -4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow on top */
    position: relative; /* Keeps footer at the bottom */
       }


      

        .sidebar {
            width: 20%; /* Adjusted to make the sidebar narrower */
            background-color: #ddd;
            padding: 20px;
            box-sizing: border-box;
            margin-left: 0; /* Aligned more to the left */
        }

        .content {
            width: 75%; /* Adjusted content width accordingly */
            padding: 20px;
            box-sizing: border-box;
        }

        .tutor-info {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 20px;
        }

        .tutor-info img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
        }

        .video-container {
            position: relative;
            padding-top: 56.25%;
            margin-bottom: 20px;
            background-color: #000;
        }

        .video-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
        }

        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        button {
            margin: 5px;
            padding: 10px 20px;
            cursor: pointer;
        }

        #course-notes {
            display: none;
            margin-top: 20px;
            text-justify: 100px;
        }

        .course-list {
            list-style-type: none;
            padding: 0;
        }

        .course-list li {
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }

        .course-list li a {
            text-decoration: none;
            color: #333;
        }

        .pagination {
            text-align: center;
            margin-top: 20px;
        }

        .pagination a {
            padding: 5px 10px;
            margin: 0 5px;
            background-color: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
    <script>
        // Toggle course notes visibility
        function toggleNotes() {
            const notes = document.getElementById('course-notes');
            notes.style.display = notes.style.display === 'block' ? 'none' : 'block';
        }

        // Handle Like button click
        function handleLike() {
            alert('You liked the course!');
        }

        // Handle Dislike button click
        function handleDislike() {
            alert('You disliked the course!');
        }

        // Navigate through courses (Next/Previous)
        function navigateCourse(direction) {
            const courseList = <?php echo json_encode($courses); ?>;
            let currentIndex = courseList.findIndex(course => course.course_id == <?php echo $course_id; ?>);
            
            if (direction === 'next' && currentIndex < courseList.length - 1) {
                window.location.href = '?course_id=' + courseList[currentIndex + 1].course_id;
            } else if (direction === 'prev' && currentIndex > 0) {
                window.location.href = '?course_id=' + courseList[currentIndex - 1].course_id;
            }
        }

        // Submit a comment
        function submitComment() {
            const comment = document.getElementById('comment-box').value;
            if (comment) {
                alert('Your comment: ' + comment);
                document.getElementById('comment-box').value = ''; // Clear the comment box
            } else {
                alert('Please enter a comment.');
            }
        }
    </script>
</head>
<body>



<div class="container">
    <div class="sidebar">
        <h3>Course List</h3>
        <ul class="course-list">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $index => $course): ?>
                    <li>
                        <a href="?course_id=<?php echo $course['course_id']; ?>">
                            <?php echo htmlspecialchars($course['course_title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses available.</p>
            <?php endif; ?>
        </ul>

        <!-- Pagination: Show more courses if available -->
        <div class="pagination">
            <a href="#">Previous</a>
            <a href="#">Next</a>
        </div>
    </div>

    <div class="content">
        <?php if ($current_course): ?>
            <div class="tutor-info">
                <img src="<?php echo htmlspecialchars($current_course['tutor_image']); ?>" alt="Tutor Image">
                <div>
                    <h4><?php echo htmlspecialchars($current_course['tutor_name']); ?></h4>
                    <p>Instructor</p>
                </div>
            </div>
            <h2><?php echo htmlspecialchars($current_course['course_title']); ?></h2>
            <p><?php echo htmlspecialchars($current_course['description']); ?></p>

            <?php if (!empty($current_course['course_videos'])): ?>
                <div class="video-container">
                    <iframe src="<?php echo htmlspecialchars($current_course['course_videos']); ?>" frameborder="0" allowfullscreen></iframe>
                </div>
            <?php else: ?>
                <p>No video available for this course.</p>
            <?php endif; ?>

            <div class="controls">
                <button onclick="toggleNotes()">View Course Notes</button>
                <button onclick="handleLike()">Like</button>
                <button onclick="handleDislike()">Dislike</button>
                <button onclick="navigateCourse('prev')">Previous</button>
                <button onclick="navigateCourse('next')">Next</button>
            </div>

            <div id="course-notes">
                <h3>Course Notes</h3>
                <p><?php echo htmlspecialchars($current_course['course_note']); ?></p>
            </div>

            <div>
                <h3>Comments</h3>
                <textarea id="comment-box" placeholder="Enter your comment"></textarea>
                <button onclick="submitComment()">Submit Comment</button>
            </div>

        <?php else: ?>
            <p>Please select a course to view details.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; // Include the footer ?>

</body>
</html>
kkkkkkkkkk

<?php
require_once 'db.php';
require_once 'header_loggedin.php';

// Database connection
$conn = new mysqli('localhost', 'root', '', 'accounting_course');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Fetch all courses for the sidebar with a limit
$courses = [];
$result = $conn->query("SELECT course_id, course_title FROM courses");
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $courses[] = $row;
    }
}

// Fetch the current course details if `course_id` is provided in the URL
$current_course = null;
$videos = [];
if (isset($_GET['course_id'])) {
    $course_id = (int)$_GET['course_id'];
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id = ?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $current_course = $result->fetch_assoc();

        // Fetch videos related to the current course
        $video_stmt = $conn->prepare("SELECT * FROM course_videos WHERE course_id = ?");
        $video_stmt->bind_param("i", $course_id);
        $video_stmt->execute();
        $video_result = $video_stmt->get_result();
        while ($video = $video_result->fetch_assoc()) {
            $videos[] = $video;
        }
        $video_stmt->close();
    }
    $stmt->close();
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            display: flex;
            flex-wrap: wrap;
         
        }
        .sidebar {
            width: 25%;
            background-color: #f9f9f9;
            padding: 20px;
        }
        .content {
            flex: 1;
            min-width: 50%;
        }
        iframe {
            width: 100%;
            height: 500px;
            border: none;
        }
    </style>
</head>
<body>
<header>
    <h1>Welcome to the Course</h1>
</header>

<div class="container">
    <div class="sidebar">
        <h3>Courses</h3>
        <ul>
            <?php foreach ($courses as $course): ?>
                <li>
                    <a href="?course_id=<?php echo $course['course_id']; ?>">
                        <?php echo htmlspecialchars($course['course_title']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="content">
        <?php if ($current_course): ?>
            <h2><?php echo htmlspecialchars($current_course['course_title']); ?></h2>
            <p><?php echo htmlspecialchars($current_course['description']); ?></p>
            <iframe src="<?php echo htmlspecialchars($videos[0]['video_url'] ?? ''); ?>" allowfullscreen></iframe>
            <ul>
                <?php foreach ($videos as $video): ?>
                    <li>
                        <a href="?course_id=<?php echo $current_course['course_id']; ?>&video_id=<?php echo $video['video_id']; ?>">
                            <?php echo htmlspecialchars($video['title']); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Please select a course to view its details.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Your Website Name</p>
</footer>
</body>
</html>
