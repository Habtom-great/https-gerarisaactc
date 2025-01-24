
<?php
session_start();
include('header.php');
include('db.php'); // Include your database connection script

// Redirect users who are not logged in
if (!isset($_SESSION['user_id'])) {
   header('Location: login.php');
   exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Fetch courses based on user role
if ($user_role === 'admin') {
   // Admin can access all courses
   $sql = "SELECT * FROM courses";
   $stmt = $pdo->prepare($sql);
   $stmt->execute();
   $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
   // Regular user: Check if they have made a payment for any course
   $sql = "SELECT c.* FROM courses c
            INNER JOIN payments p ON c.course_id = p.course_id
            WHERE p.user_id = :user_id AND p.status = 'completed'";
   $stmt = $pdo->prepare($sql);
   $stmt->execute(['user_id' => $user_id]);
   $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Courses</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- Custom CSS File Link -->
   <link rel="stylesheet" href="03-styles.css">
</head>
<body>
   <header class="header">
      <section class="flex">
         <a href="courses.php" class="logo">Courses</a>
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
         <div class="profile">
            <img src="assets/images_courses/thumb-1.png" class="image" alt="">
            <h3 class="name">Accounting Courses</h3>
            <p class="role">Acc.101</p>
            <a href="profile.php" class="btn">View Profile</a>
            <div class="flex-btn">
               <a href="login.php" class="option-btn">Login</a>
               <a href="register.php" class="option-btn">Register</a>
            </div>
         </div>
      </section>
   </header>

   <div class="side-bar">
      <div id="close-btn">
         <i class="fas fa-times"></i>
      </div>
      <div class="profile">
         <img src="assets/images_courses/Gerar Isaac .png" class="image" alt="">
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

   <section class="courses">
      <h1 class="heading">Our Courses</h1>
      <div class="box-container">
         <?php foreach ($courses as $course) : ?>
            <div class="box">
               <div class="tutor">
                  <img src="<?php echo htmlspecialchars($course['tutor_image'] ?? 'default_tutor.png'); ?>" alt="">
                  <div class="info">
                     <h3><?php echo htmlspecialchars($course['tutor_name'] ?? 'Unknown Tutor'); ?></h3>
                     <span><?php echo htmlspecialchars($course['date'] ?? 'Unknown Date'); ?></span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="<?php echo htmlspecialchars($course['thumb_image'] ?? 'default_thumb.png'); ?>" alt="">
                  <span><?php echo htmlspecialchars($course['video_count'] ?? '0 videos'); ?></span>
               </div>
               <h3 class="title"><?php echo htmlspecialchars($course['course_name'] ?? 'Untitled Course'); ?></h3>
               <a href="playlist.php?id=<?php echo htmlspecialchars($course['course_id']); ?>" class="inline-btn">View Course</a>
            </div>
         <?php endforeach; ?>
      </div>

      
   </section>

 

   <!DOCTYPE html>
   <html lang="en">

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css" />

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/03-styles.css" />
   </head>

   <body>

     
      <section class="courses">
         <div class="box-container">
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="assets/images_courses/Accounting Image.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">Accounting</h3>
               <a href="playlist.php" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/Gerar Isaac_files/20211003_130728.jpg" alt="">
                  <div class="info">
                     <h3>Habtom Araya-ACCA</h3>
                     <span>21-10-2023</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="assets/images_courses/Peach Tree/logo.jpeg" alt="">
                  <span>10 videos</span>
               </div>
               <h3 class="title">Peach tree tutorial</h3>
               <a href="peach tree.php" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/Gerar Isaac_files/20211003_130728.jpg" alt="">
                  <div class="info">
                     <h3>Habtom Araya-ACCA</h3>
                     <span>21-10-2023</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="assets/Electricity training/logo.jpeg" alt="">
                  <span>10 videos</span>
               </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="playlist.php" class="inline-btn">view courses</a>
            </div>


            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/post-3-1.png" alt="">
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2023</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="assets/images_courses/thumb-1.png" alt="">
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete HTML tutorial</h3>
               <a href="playlist.php" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/pic-3.jpg" alt="">
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2023</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="assets/images_courses/thumb-2.png" alt="">
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete CSS tutorial</h3>
               <a href="playlist.php" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-4.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-3.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete JS tutorial</h3>
               <a href="playlist.html" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-5.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-4.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete Boostrap tutorial</h3>
               <a href="playlist.html" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-6.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-5.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete JQuery tutorial</h3>
               <a href="playlist.html" class="inline-btn">vview courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-7.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-6.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete SASS tutorial</h3>
               <a href="playlist.html" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-8.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-7.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete PHP tutorial</h3>
               <a href="playlist.html" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-9.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-8.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete MySQL tutorial</h3>
               <a href="playlist.html" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="images/pic-1.jpg" alt="" />
                  <div class="info">
                     <h3>john deo</h3>
                     <span>21-10-2022</span>
                  </div>
               </div>
               <div class="thumb">
                  <img src="images/thumb-9.png" alt="" />
                  <span>10 videos</span>
               </div>
               <h3 class="title">complete react tutorial</h3>
               <a href="playlist.html" class="inline-btn">view courses</a>
            </div>
         </div>
      </section>

      <footer class="footer">&copy; copyright @ 2022 by <span>mr. web designer</span> | all rights reserved!</footer>

      <!-- custom js file link  -->


      <?php
      // Array of course data
      $courses = [
         [
            'tutor_image' => 'images/pic-2.jpg',
            'tutor_name' => 'Habtom Araya-ACCA',
            'date' => '01-2-2023-2024',
            'thumb_image' => 'images/Accounting Image.png',
            'video_count' => '10 videos',
            'title' => 'Accounting',
            'link' => 'playlist.html'
         ],
         [
            'tutor_image' => 'images/post-3-1.png',
            'tutor_name' => 'Acc.courses',
            'date' => '21-10-2022',
            'thumb_image' => 'images/thumb-1.png',
            'video_count' => '10 videos',
            'title' => 'complete HTML tutorial',
            'link' => 'playlist.html'
         ],
         // Add more courses as needed
      ];
      ?>

      <section class="courses">
         <h1 class="heading">our courses</h1>

         <div class="box-container">
            <?php foreach ($courses as $course) : ?>
               <div class="box">
                  <div class="tutor">
                     <img src="<?= $course['tutor_image'] ?>" alt="" />
                     <div class="info">
                        <h3><?= $course['tutor_name'] ?></h3>
                        <span><?= $course['date'] ?></span>
                     </div>
                  </div>
                  <div class="thumb">
                     <img src="<?= $course['thumb_image'] ?>" alt="" />
                     <span><?= $course['video_count'] ?></span>
                  </div>
                  <h3 class="title"><?= $course['title'] ?></h3>
                  <a href="<?= $course['link'] ?>" class="inline-btn">view courses</a>
               </div>
            <?php endforeach; ?>
         </div>
      </section>

      <?php include 'footer.php'; ?>

kkkk
<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include('header.php');
include('db.php'); // Include your database connection script

// Redirect users who are not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

// Prepare SQL query based on user role
if ($user_role === 'admin') {
    // Admin can access all courses
    $sql = "SELECT * FROM courses";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Regular user: Fetch courses based on completed payments
    $sql = "SELECT DISTINCT c.* FROM courses c
            INNER JOIN payments p ON c.course_id = p.course_id
            WHERE p.user_id = :user_id AND p.status = 'completed'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="assets/03-styles.css">
    <style>
        /* CSS to ensure header font size is visible on courses.php */
        .courses-page .navbar-brand {
            font-size: 2.95rem; /* Adjust as needed */
        }

        .courses-page .nav-link {
            font-size: 1.45rem; /* Adjust as needed */
        }
        
        .courses-page .navbar-nav .nav-item {
            padding: 0.5rem 1rem; /* Adjust padding for better spacing */
        }
    </style>
</head> 


<body class="courses-page">
    <header class="header">
        <section class="flex">
            <a href="courses.php" class="logo">Courses</a>
            <form action="search.html" method="post" class="search-form">
                <input type="text" name="search_box" required placeholder="search courses..." maxlength="100">
                <button type="submit" class="fas fa-search"></button>
            </form>
            <div class="icons">
                <div id="menu-btn" class="fas fa-bars"></div>
                <div id="search-btn" class="fas fa-search"></div>
                <div id="user-btn" class="fas fa-user"></div>
                <div id="toggle-btn" class="fas fa-sun"></div>
            </div>
            <div class="profile">
                <img src="assets/images_courses/thumb-1.png" class="image" alt="">
                <h3 class="name">Accounting courses</h3>
                <p class="role">Acc.101</p>
                <a href="profile.php" class="btn">view profile</a>
                <div class="flex-btn">
                    <a href="login.php" class="option-btn">login</a>
                    <a href="register.php" class="option-btn">register</a>
                </div>
            </div>
        </section>
    </header>

    <div class="side-bar">
        <div id="close-btn">
            <i class="fas fa-times"></i>
        </div>
        <div class="profile">
            <img src="assets/images_courses/Gerar Isaac .png" class="image" alt="">
            <h3 class="name">Courses</h3>
            <a href="courses.php" class="btn">view courses</a>
        </div>
        <nav class="navbar">
            <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
            <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
            <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
            <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
            <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
        </nav>
    </div>

    <section class="courses">
        <h1 class="heading">Our Courses</h1>
        <div class="box-container">
            <?php if (!empty($courses)) : ?>
                <?php foreach ($courses as $course) : ?>
                    <div class="box">
                        <div class="tutor">
                            <img src="<?php echo htmlspecialchars($course['tutor_image']); ?>" alt="">
                            <div class="info">
                                <h3><?php echo htmlspecialchars($course['tutor_name']); ?></h3>
                                <span><?php echo htmlspecialchars($course['date']); ?></span>
                            </div>
                        </div>
                        <div class="thumb">
                            <img src="<?php echo htmlspecialchars($course['thumb_image']); ?>" alt="">
                            <span>10 videos</span>
                        </div>
                        <h3 class="title"><?php echo htmlspecialchars($course['course_name']); ?></h3>
                        <a href="playlist.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" class="inline-btn">View Course</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No courses available.</p>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
