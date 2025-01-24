<?php 
session_start();
include('db.php'); // Ensure this file sets up $pdo
include('header.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

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
    <title>Courses</title>
    <!-- Font Awesome and CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .courses {
            padding: 20px;
        }
        .courses .box-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .courses .box {
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .courses .box:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 12px rgba(0, 0, 0, 0.2);
        }
        .courses .box img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .courses .box .tutor img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-bottom: 10px;
        }
        .courses .box h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }
        .courses .box span {
            display: block;
            color: #666;
            font-size: 14px;
            margin-bottom: 10px;
        }
        .inline-btn {
            background: #007bff;
            color: #fff;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
        }
        .inline-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <header class="header">
        <section class="flex">
            <a href="home.php" class="logo">Back to Home</a>
            <form action="search.html" method="post" class="search-form">
                <input type="text" name="search_box" required placeholder="Search courses..." maxlength="100">
                <button type="submit" class="fas fa-search"></button>
            </form>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>
        </section>
    </header>

    <section class="courses">
        <div class="box-container">
            <?php if (!empty($courses)): ?>
                <?php foreach ($courses as $course): ?>
                    <div class="box">
                        <div class="tutor">
                            <img src="<?= htmlspecialchars($course['tutor_image'] ?? 'default_image.jpg'); ?>" alt="Tutor Image" />
                            <h3><?= htmlspecialchars($course['tutor_name']); ?></h3>
                            <span><?= htmlspecialchars($course['created_at'] ?? 'Date not available'); ?></span>
                        </div>
                        <div class="thumb">
                            <img src="<?= htmlspecialchars($course['thumb_image'] ?? 'default_course_image.jpg'); ?>" alt="Course Image" />
                            <span><?= htmlspecialchars($course['video_count'] ?? '0'); ?> videos</span>
                        </div>
                        <h3 class="title"><?= htmlspecialchars($course['course_title']); ?></h3>
                        <a href="course_details.php?course_id=<?= urlencode($course['course_id']); ?>" class="inline-btn">View Details</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No courses found.</p>
            <?php endif; ?>
        </div>
    </section>
    <div class="side-bar">
        <div id="close-btn">
            <i class="fas fa-times"></i>
        </div>
        <div class="profile">
            <img src="assets/logos/Gerar Isaac .jpg" class="image" alt="">
            <h3 class="name">Courses</h3>
            <a href="courses.php" class="btn">View Courses</a>
        </div>
        <nav class="navbar">
            <a href="home.php"><i class="fas fa-home"></i><span>Home</span></a>
            <a href="about.php"><i class="fas fa-question"></i><span>About</span></a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>Courses</span></a>
            <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>Teachers</span></a>
            <a href="contact.php"><i class="fas fa-headset"></i><span>Contact Us</span></a>
        </nav>
    </div>
    
    <footer class="footer">
        &copy; <?= date('Y'); ?> by <span>Web Designer*</span> | All rights reserved!
    </footer>
</body>
</html>
kkkkkkkkkk
<?php 

if (isset($_POST['submit']) && isset($_FILES['my_video'])) {
	include "db_conn.php";
    $video_name = $_FILES['my_video']['name'];
    $tmp_name = $_FILES['my_video']['tmp_name'];
    $error = $_FILES['my_video']['error'];

    if ($error === 0) {
    	$video_ex = pathinfo($video_name, PATHINFO_EXTENSION);

    	$video_ex_lc = strtolower($video_ex);

    	$allowed_exs = array("mp4", 'webm', 'avi', 'flv');

    	if (in_array($video_ex_lc, $allowed_exs)) {
    		
    		$new_video_name = uniqid("video-", true). '.'.$video_ex_lc;
    		$video_upload_path = 'uploads/'.$new_video_name;
    		move_uploaded_file($tmp_name, $video_upload_path);

    		// Now let's Insert the video path into database
            $sql = "INSERT INTO videos(video_url) 
                   VALUES('$new_video_name')";
            mysqli_query($conn, $sql);
            header("Location: view.php");
    	}else {
    		$em = "You can't upload files of this type";
    		header("Location: index.php?error=$em");
    	}
    }


}else{
	header("Location: index.php");
}
    

