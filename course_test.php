<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GITC</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Custom styles for header */
        .navbar-brand img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
        }

        .footer a {
            color: #f8f9fa;
            text-decoration: none;
        }

        .footer a:hover {
            color: #ffc107;
        }

        .social-icons a {
            font-size: 20px;
            margin: 0 10px;
        }
    </style>
</head>
<body>

<!-- Header -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="#">
            <img src="logo.png" alt="Logo"> GITC
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link active" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Courses</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Content Placeholder -->
<div class="container mt-4">
    <h1>Welcome to GITC</h1>
    <p>Content goes here...</p>
</div>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Video Course</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .course-list {
            height: 100vh;
            overflow-y: auto;
            border-right: 1px solid #ddd;
        }

        .video-container {
            padding: 20px;
        }

        .video-container video {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .notes {
            margin-top: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        .course-item {
            display: flex;
            align-items: center;
            padding: 10px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .course-item:hover {
            background-color: #f4f4f4;
        }

        .course-item img {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
        <!-- Course List -->
        <div class="col-md-3 course-list bg-light">
            <h4 class="p-3">Course List</h4>
            <div id="course-list">
                <!-- Course items loaded via PHP -->
                <?php
                // Example data (replace this with database calls)
                $courses = [
                    ["id" => 1, "title" => "Chapter 1: Basics of Accounting", "icon" => "video-icon.png"],
                    ["id" => 2, "title" => "Chapter 2: Accounting Principles", "icon" => "video-icon.png"],
                    ["id" => 3, "title" => "Chapter 3: Accounting Equation", "icon" => "video-icon.png"]
                ];

                foreach ($courses as $course) {
                    echo '<div class="course-item" onclick="loadVideo(' . $course["id"] . ')">
                            <img src="' . $course["icon"] . '" alt="Video Icon">
                            <span>' . $course["title"] . '</span>
                          </div>';
                }
                ?>
            </div>
        </div>

        <!-- Video Player -->
        <div class="col-md-9 video-container">
            <video id="course-video" controls>
                <source src="default-video.mp4" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            <div class="notes">
                <h5>Course Notes</h5>
                <p id="course-notes">Select a course to view notes.</p>
            </div>
        </div>
    </div>
</div>

<script>
    function loadVideo(courseId) {
        // Fetch video and notes dynamically using Ajax
        $.ajax({
            url: 'load_video.php',
            type: 'GET',
            data: { id: courseId },
            success: function(response) {
                const data = JSON.parse(response);
                $('#course-video source').attr('src', data.video);
                $('#course-video')[0].load(); // Reload the video player
                $('#course-notes').text(data.notes);
            },
            error: function() {
                alert('Error loading video.');
            }
        });
    }
</script>
</body>
</html>
<?php
// Simulate fetching video and notes from a database
$courseData = [
    1 => ["video" => "video1.mp4", "notes" => "Notes for Chapter 1: Basics of Accounting"],
    2 => ["video" => "video2.mp4", "notes" => "Notes for Chapter 2: Accounting Principles"],
    3 => ["video" => "video3.mp4", "notes" => "Notes for Chapter 3: Accounting Equation"],
];

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if (array_key_exists($id, $courseData)) {
        echo json_encode($courseData[$id]);
    } else {
        echo json_encode(["video" => "", "notes" => "No notes available."]);
    }
}
?>
<!-- Footer -->
<footer class="footer">
    <div class="container text-center">
        <div class="row">
            <div class="col-md-6">
                <p>&copy; 2024 GITC. All Rights Reserved.</p>
            </div>
            <div class="col-md-6 social-icons">
                <a href="#" aria-label="Facebook"><i class="fab fa-facebook"></i></a>
                <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
