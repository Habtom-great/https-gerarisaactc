<?php
include('db.php'); // Ensure this file sets up $pdo
include('header_loggedin.php');

// Fetch courses from the database
try {
    $sql = "SELECT * FROM courses";
    $stmt = $pdo->query($sql);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electrical Engineering Courses</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/stylish.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .content-wrapper {
            display: flex;
            justify-content: space-between;
            margin: 20px auto;
            width: 90%;
            max-width: 1200px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            padding: 20px;
        }
        .course-sidebar {
            width: 30%;
            padding-right: 20px;
            border-right: 1px solid #ddd;
        }
        .course-sidebar h3 {
            font-size: 1.8em; /* Increased font size */
            margin-bottom: 10px;
        }
        .module-nav__module {
            margin-bottom: 10px;
        }
        .module-nav__module-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #333;
            background: #e0e0e0;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            font-size: 1.4em; /* Increased font size */
        }
        .module-nav__topics {
            padding: 10px 20px;
            display: none;
            background: #f9f9f9;
            border-radius: 0 0 8px 8px;
        }
        .module-nav__topic {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            margin-bottom: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .module-nav__topic:hover {
            background-color: #e0f7fa;
            border-color: #b2ebf2;
        }
        .video-player {
            width: 65%;
            background: #ffffff;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .video-player iframe {
            width: 100%;
            height: 400px;
            border: none;
        }
        .video-controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }
        .video-controls button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px 20px;
            font-size: 1em;
            cursor: pointer;
            border-radius: 5px;
        }
        .video-controls button:hover {
            background-color: #0056b3;
        }
        .course-note {
            display: none;
            margin-top: 20px;
        }
        .course-note h4 {
            margin-bottom: 10px;
        }
        .progress-indicator {
            margin-top: 20px;
        }
        .progress-indicator h4 {
            margin-bottom: 10px;
        }
        .progress {
            background-color: #e0e0e0;
            border-radius: 5px;
            height: 20px;
            width: 100%;
            position: relative;
        }
        .progress-bar {
            background-color: #007bff;
            height: 100%;
            border-radius: 5px;
            transition: width 0.3s;
        }
        .comments {
            margin-top: 20px;
        }
        .comments h4 {
            margin-bottom: 10px;
        }
        .comments form {
            display: flex;
            flex-direction: column;
        }
        .comments form textarea {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
        }
        .comments form button {
            align-self: flex-start;
        }
        .comments .comment {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>
    
    <div class="content-wrapper">
        <!-- Left Sidebar: Course Modules -->
        <div class="course-sidebar">
            <h3>Course Modules</h3>
            <div class="module-nav">
                <?php foreach ($courses as $course): ?>
                    <!-- Check if course modules exist -->
                    <?php if (!empty($course['course_videos'])): ?>
                        <!-- Module Title -->
                        <div class="module-nav__module">
                            <a href="#" class="module-nav__module-title">
                                <h3><?php echo htmlspecialchars($course['course_title']); ?>
                                    <span class="icon-thick-chevron-up">&#9650;</span>
                                </h3>
                            </a>
                            <div class="module-nav__topics">
                                <?php foreach (explode(',', $course['course_videos']) as $video): ?>
                                    <a data-video="<?php echo htmlspecialchars($video); ?>" class="module-nav__topic">
                                        <h4><?php echo htmlspecialchars($course['course_title']); ?> - Topic</h4>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Video Player and Course Content -->
        <div class="video-player">
            <!-- Video Frame -->
            <iframe src="" id="video-frame" allowfullscreen></iframe>

            <!-- Video Controls -->
            <div class="video-controls">
                <button id="like-btn">Like</button>
                <button id="dislike-btn">Dislike</button>
                <button id="view-notes-btn">View Notes</button>
            </div>

            <!-- Course Notes -->
            <div class="course-note" id="course-note">
                <h4>Course Notes:</h4>
                <p>These are the notes for the selected video module. You can view detailed notes here.</p>
            </div>

            <!-- Progress Indicator -->
            <div class="progress-indicator">
                <h4>Progress:</h4>
                <div class="progress">
                    <div class="progress-bar" style="width: 75%;"></div>
                </div>
            </div>

            <!-- Comments Section -->
            <div class="comments">
                <h4>Comments:</h4>
                <form action="submit_comment.php" method="post">
                    <textarea name="comment" rows="4" placeholder="Add a comment..." required></textarea>
                    <button type="submit">Submit</button>
                </form>
                <div class="comment">
                    <p><strong>John Doe:</strong> This is a great course!</p>
                </div>
                <!-- Add more comments as needed -->
            </div>

            <!-- Navigation Buttons -->
            <div class="navigation-buttons">
                <button id="prev-btn">Previous</button>
                <button id="next-btn">Next</button>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for handling video changes and interactivity
        document.querySelectorAll('.module-nav__topic').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const videoUrl = item.getAttribute('data-video');
                document.getElementById('video-frame').src = videoUrl;
            });
        });

        document.getElementById('view-notes-btn').addEventListener('click', () => {
            const notes = document.getElementById('course-note');
            notes.style.display = notes.style.display === 'none' ? 'block' : 'none';
        });

        // Toggle visibility of module topics
        document.querySelectorAll('.module-nav__module-title').forEach(title => {
            title.addEventListener('click', () => {
                const topics = title.nextElementSibling;
                if (topics.style.display === 'block') {
                    topics.style.display = 'none';
                } else {
                    topics.style.display = 'block';
                }
            });
        });

        // Example handlers for Next and Previous buttons
        document.getElementById('next-btn').addEventListener('click', () => {
            // Logic to navigate to the next page
        });

        document.getElementById('prev-btn').addEventListener('click', () => {
            // Logic to navigate to the previous page
        });
    </script>
</body>
</html>
