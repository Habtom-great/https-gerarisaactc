

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
    <!-- Font Awesome CDN link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">
    <!-- Custom CSS file link -->
    <link rel="stylesheet" href="assets/css/stylish.css">

</head>
<body>
    <header class="header">
        <section class="flex">
            <a href="home.php" class="logo">View Electrical Engineering Courses</a>
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
                <img src="assets/tutor/Habtom.jpg" class="image" alt="Tutor Image">
                <h3 class="name">Habtom Araya-ACCA</h3>
                <p class="role">Tutor</p>
                <a href="profile.html" class="btn">View Profile</a>
                <div class="flex-btn">
                    <a href="login.html" class="option-btn">Login</a>
                    <a href="register.html" class="option-btn">Register</a>
                </div>
            </div>
        </section>
    </header>
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
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" >
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="fundamental_electric-course.php" class="inline-btn">view courses</a>
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
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" >
                  <span>10 videos</span>
               </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>
            
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

           
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

            
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

           
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

            
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
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
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>
            
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

          
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

            
            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

            <div class="box">
               <div class="tutor">
                  <img src="assets/images_courses/hab.jpg" alt="" />
                  <div class="info">
                     <h3>Habtom Araya-ACCA-----</h3>
                     <span>01-2-2023-2024</span>
                  </div>
               </div>
               <div class="thumb">
               <img src="assets/Electricity training/Elecricity Training/electrical-engineering-logo.avif" alt="" />
                  <span>10 videos</span>
                  </div>
               <h3 class="title">Basic Electrical Engineering</h3>
               <a href="electrical engineering course.php" class="inline-btn">view courses</a>
            </div>

           
      </section>

      <footer class="footer">&copy; copyright @ 2023 by <span>web designer Habtom Araya -ACCA</span> | all rights reserved!</footer>



<body class="courses-page">
    <header class="header">
        <section class="flex">
            
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


    kkkkkkkkkkkkkkkk
    <div class="module-nav">
    <div class="module-nav__module module-nav__module--open">
        <a href="#" class="module-nav__module-title">
            <h3>Module 1: Introduction to Basic Electricity 
                <span class="icon-thick-chevron-up"></span>
            </h3>
        </a>
        <div class="module-nav__topics">
            <a data-id="124675" title="Introduction to Basic Electricity - Learning Outcomes" 
                href="https://alison.com/topic/learn/124675/introduction-to-basic-electricity-learning-outcomes" 
                class="module-nav__topic active completed_topic">
                <span></span>
                <h4>Introduction to Basic Electricity - Learning Outcomes</h4>
            </a>
            <a data-id="124676" title="Fundamentals of Electricity" 
                href="https://alison.com/topic/learn/124676/fundamentals-of-electricity" 
                
                class="module-nav__topic active completed_topic">
                <span></span>
                <h4>Fundamentals of Electricity</h4>
            </a>
            <a data-id="124677" title="Electrical Units and Ohm’s Law" 
                href="https://alison.com/topic/learn/124677/electrical-units-and-ohms-law" 
                 href="https://player.vimeo.com/video/583618506"
                class="module-nav__topic current">
                <span></span>
                <h4>Electrical Units and Ohm’s Law</h4>
            </a>
            <a data-id="124678" title="Basics of Direct Current (DC) Circuits" 
                href="https://alison.com/topic/learn/124678/basics-of-direct-current-dc-circuits" 
                href="https://player.vimeo.com/video/583618019"
                class="module-nav__topic">
                <span></span>
                <h4>Basics of Direct Current (DC) Circuits</h4>
            </a>
            <a data-id="124679" title="Basics of Alternating Current" 
                href="https://alison.com/topic/learn/124679/basics-of-alternating-current" 
                href="https://player.vimeo.com/video/583617288"
                class="module-nav__topic">
                <span></span>
                <h4>Basics of Alternating Current</h4>
            </a>
            <a data-id="124680" title="Introduction to Basic Electricity - Lesson Summary" 
                href="https://alison.com/topic/learn/124680/introduction-to-basic-electricity-lesson-summary" 
                class="module-nav__topic">
                <span></span>
                <h4>Introduction to Basic Electricity - Lesson Summary</h4>
            </a>
        </div>
    </div>
    <div class="module-nav__module module-nav__module--closed">
        <a href="#" class="module-nav__module-title">
            <h3>Module 2: Basic Electricity Components and Precautions 
                <span class="icon-thick-chevron-up"></span>
            </h3>
        </a>
        <div class="module-nav__topics">
            <a data-id="124681" title="Basic Electricity Components and Precautions - Learning Outcomes" 
                href="https://alison.com/topic/learn/124681/basic-electricity-components-and-precautions-learning-outcomes" 
                class="module-nav__topic">
                <span></span>
                <h4>Basic Electricity Components and Precautions - Learning Outcomes</h4>
            </a>
            <a data-id="124682" title="Voltage Sources and Resistors" 
                href="https://alison.com/topic/learn/124682/voltage-sources-and-resistors" 
                 href="https://player.vimeo.com/video/583740013?quality=720p"
                class="module-nav__topic">
                <span></span>
                <h4>Voltage Sources and Resistors</h4>
            </a>
            <a data-id="124683" title="Capacitance and Capacitors" 
                href="https://alison.com/topic/learn/124683/capacitance-and-capacitors" 
                href="https://player.vimeo.com/video/583725560?quality=720"
                class="module-nav__topic">
                <span></span>
                <h4>Capacitance and Capacitors</h4>
            </a>
            <a data-id="124684" title="Magnets and Magnetism" 
                href="https://alison.com/topic/learn/124684/magnets-and-magnetism" 
                href="https://player.vimeo.com/video/583732127?quality=720"
                class="module-nav__topic">
                <span></span>
                <h4>Magnets and Magnetism</h4>
            </a>
            <a data-id="124685" title="Electrical Safety Measures" 
                href="https://alison.com/topic/learn/124685/electrical-safety-measures" 
                src="https://player.vimeo.com/video/583727241?quality=720p"https://player.vimeo.com/video/583727241?quality=720p
                class="module-nav__topic">
                <span></span>
                <h4>Electrical Safety Measures</h4>
            </a>
            <a data-id="124686" title="Basic Electricity Components and Precautions - Lesson Summary" 
                href="https://alison.com/topic/learn/124686/basic-electricity-components-and-precautions-lesson-summary" 
                src="https://player.vimeo.com/video/583738738?quality=720p"
                class="module-nav__topic">
                <span></span>
                <h4>Basic Electricity Components and Precautions - Lesson Summary</h4>
            </a>
        </div>
    </div>
</div>

kkkkkkkkkkkk

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electricity Modules</title>
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="scripts.js"></script>
    <style>
        .module-nav {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .module-nav__module-title {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-decoration: none;
            color: #333;
        }
        .module-nav__module-title h3 {
            margin: 0;
            font-size: 1.5em;
        }
        .icon-thick-chevron-up {
            font-size: 1.2em;
            transition: transform 0.3s;
        }
        .module-nav__module--open .icon-thick-chevron-up {
            transform: rotate(180deg);
        }
        .module-nav__topics {
            padding: 10px;
            display: none;
        }
        .module-nav__topic {
            display: block;
            padding: 10px;
            text-decoration: none;
            color: #333;
            margin-bottom: 5px;
            border-radius: 5px;
        }
        .module-nav__topic.active {
            background-color: #e0f7fa;
        }
        .module-nav__topic.current {
            background-color: #b2dfdb;
        }
        .module-nav__topic.completed_topic {
            background-color: #b9fbc0;
        }
        .module-nav__topic span {
            display: none;
        }
        .module-nav__topic h4 {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="module-nav">
        <div class="module-nav__module module-nav__module--open">
            <a href="#" class="module-nav__module-title">
                <h3>Module 1: Introduction to Basic Electricity 
                    <span class="icon-thick-chevron-up"></span>
                </h3>
            </a>
            <div class="module-nav__topics">
                <a data-id="124675" title="Introduction to Basic Electricity - Learning Outcomes" 
                    href="https://alison.com/topic/learn/124675/introduction-to-basic-electricity-learning-outcomes" 
                    class="module-nav__topic active completed_topic">
                    <span></span>
                    <h4>Introduction to Basic Electricity - Learning Outcomes</h4>
                </a>
                <a data-id="124676" title="Fundamentals of Electricity" 
                    href="https://alison.com/topic/learn/124676/fundamentals-of-electricity" 
                    class="module-nav__topic active completed_topic">
                    <span></span>
                    <h4>Fundamentals of Electricity</h4>
                </a>
                <a data-id="124677" title="Electrical Units and Ohm’s Law" 
                    href="https://player.vimeo.com/video/583618506"
                    class="module-nav__topic current">
                    <span></span>
                    <h4>Electrical Units and Ohm’s Law</h4>
                </a>
                <a data-id="124678" title="Basics of Direct Current (DC) Circuits" 
                    href="https://player.vimeo.com/video/583618019"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basics of Direct Current (DC) Circuits</h4>
                </a>
                <a data-id="124679" title="Basics of Alternating Current" 
                    href="https://player.vimeo.com/video/583617288"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basics of Alternating Current</h4>
                </a>
                <a data-id="124680" title="Introduction to Basic Electricity - Lesson Summary" 
                    href="https://alison.com/topic/learn/124680/introduction-to-basic-electricity-lesson-summary" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Introduction to Basic Electricity - Lesson Summary</h4>
                </a>
            </div>
        </div>
        <div class="module-nav__module module-nav__module--closed">
            <a href="#" class="module-nav__module-title">
                <h3>Module 2: Basic Electricity Components and Precautions 
                    <span class="icon-thick-chevron-up"></span>
                </h3>
            </a>
            <div class="module-nav__topics">
                <a data-id="124681" title="Basic Electricity Components and Precautions - Learning Outcomes" 
                    href="https://alison.com/topic/learn/124681/basic-electricity-components-and-precautions-learning-outcomes" 
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basic Electricity Components and Precautions - Learning Outcomes</h4>
                </a>
                <a data-id="124682" title="Voltage Sources and Resistors" 
                    href="https://player.vimeo.com/video/583740013?quality=720p"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Voltage Sources and Resistors</h4>
                </a>
                <a data-id="124683" title="Capacitance and Capacitors" 
                    href="https://player.vimeo.com/video/583725560?quality=720p"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Capacitance and Capacitors</h4>
                </a>
                <a data-id="124684" title="Magnets and Magnetism" 
                    href="https://player.vimeo.com/video/583732127?quality=720p"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Magnets and Magnetism</h4>
                </a>
                <a data-id="124685" title="Electrical Safety Measures" 
                    href="https://player.vimeo.com/video/583727241?quality=720p"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Electrical Safety Measures</h4>
                </a>
                <a data-id="124686" title="Basic Electricity Components and Precautions - Lesson Summary" 
                    href="https://player.vimeo.com/video/583738738?quality=720p"
                    class="module-nav__topic">
                    <span></span>
                    <h4>Basic Electricity Components and Precautions - Lesson Summary</h4>
                </a>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function() {
            $('.module-nav__module-title').on('click', function() {
                $(this).closest('.module-nav__module').toggleClass('module-nav__module--open module-nav__module--closed');
                $(this).siblings('.module-nav__topics').slideToggle();
                $(this).find('.icon-thick-chevron-up').toggleClass('icon-thick-chevron-down');
            });
        });
    </script>
</body>
</html>

kkkkkkkkkkkk
    <h3>
                                        Module 1: Introduction to Basic Electricity <span class="icon-thick-chevron-up"></span>
                                    </h3>
                                </a>
                                <div class="module-nav__topics">
                                    <a data-id="124675" title="Introduction to Basic Electricity - Learning Outcomes" href="https://alison.com/topic/learn/124675/introduction-to-basic-electricity-learning-outcomes" class="module-nav__topic active completed_topic ">
                                        <span></span>
                                        <h4>Introduction to Basic Electricity - Learning Outcomes</h4>
                                    </a>
                                    <a data-id="124676" title="Fundamentals of Electricity" href="https://alison.com/topic/learn/124676/fundamentals-of-electricity" class="module-nav__topic active completed_topic ">
                                        <span></span>
                                        <h4>Fundamentals of Electricity</h4>
                                    </a>
                                    <a data-id="124677" title="Electrical Units and Ohm’s Law" href="https://alison.com/topic/learn/124677/electrical-units-and-ohms-law" class="module-nav__topic current ">
                                        <span></span>
                                        <h4>Electrical Units and Ohm’s Law</h4>
                                    </a>
                                    <a data-id="124678" title="Basics of Direct Current (DC) Circuits" href="https://alison.com/topic/learn/124678/basics-of-direct-current-dc-circuits" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basics of Direct Current (DC) Circuits</h4>
                                    </a>
                                    <a data-id="124679" title="Basics of Alternating Current" href="https://alison.com/topic/learn/124679/basics-of-alternating-current" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basics of Alternating Current</h4>
                                    </a>
                                    <a data-id="124680" title="Introduction to Basic Electricity - Lesson Summary" href="https://alison.com/topic/learn/124680/introduction-to-basic-electricity-lesson-summary" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Introduction to Basic Electricity - Lesson Summary</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="module-nav__module module-nav__module--closed ">
                                <a href="https://alison.com/topic/learn/124681/basic-electricity-components-and-precautions-learning-outcomes " class="module-nav__module-title ">
                                    <span></span>
                                    <h3>
                                        Module 2: Basic Electricity Components and Precautions <span class="icon-thick-chevron-up"></span>
                                    </h3>
                                </a>
                                <div class="module-nav__topics" style="display: none">
                                    <a data-id="124681" title="Basic Electricity Components and Precautions - Learning Outcomes" href="https://alison.com/topic/learn/124681/basic-electricity-components-and-precautions-learning-outcomes" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basic Electricity Components and Precautions - Learning Outcomes</h4>
                                    </a>
                                    <a data-id="124682" title="Voltage Sources and Resistors" href="https://alison.com/topic/learn/124682/voltage-sources-and-resistors" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Voltage Sources and Resistors</h4>
                                    </a>
                                    <a data-id="124683" title="Capacitance and Capacitors" href="https://alison.com/topic/learn/124683/capacitance-and-capacitors" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Capacitance and Capacitors</h4>
                                    </a>
                                    <a data-id="124684" title="Magnets and Magnetism" href="https://alison.com/topic/learn/124684/magnets-and-magnetism" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Magnets and Magnetism</h4>
                                    </a>
                                    <a data-id="124685" title="Electrical Safety Measures" href="https://alison.com/topic/learn/124685/electrical-safety-measures" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Electrical Safety Measures</h4>
                                    </a>
                                    <a data-id="124686" title="Basic Electricity Components and Precautions - Lesson Summary" href="https://alison.com/topic/learn/124686/basic-electricity-components-and-precautions-lesson-summary" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basic Electricity Components and Precautions - Lesson Summary</h4>
                                    </a>
                                </div>
                            </div>


    <script>
        $(document).ready( () => {
            console.log(`%cSurvey triggering conditions: [career_survey_status]: ${[null, 'dismissed'].includes(localStorage.getItem('career_survey_status'))}, [session_survey_status]: ${sessionStorage.getItem('session_survey_status') === null}`, `background: crimson;`);
            if ([null, 'dismissed'].includes(localStorage.getItem('career_survey_status')) && sessionStorage.getItem('session_survey_status') === null) {
                $('.pop__overlay, .survey-pop').fadeIn();
                $('body').addClass('no-scroll');
            }
        }
        );
        const headings = {
            q2a: "are you currently seeking a job in",
            q2b: "are you interested in working in",
            q2c: "are you currently working in",
        };
        let surveyData = {
            "type": "recruitment",
            "answers": []
        };
        let questionText = '';
        $(document).on('click', '.survey-pop-screen.active .answer-list li', e => {
            e.preventDefault();
            const currentItem = $(e.target);
            const currentScreen = parseInt(currentItem.parents('.survey-pop-screen').attr('data-screen'));
            const question = currentItem.parents('.survey-pop-screen').attr('data-question-code');
            const answer = currentItem.parents('.survey-pop-screen').attr('data-answer-code');
            switch (question) {
            case "q1":
                currentItem.parents('.answer-list').find('li').removeClass('selected');
                currentItem.addClass('selected');
                questionText = 'Would you like Alison to help you find a job?';
                surveyData['answers'].push({
                    question: questionText,
                    answer: currentItem.text()
                });
                let headingSubstitute = headings[currentItem.parents('.answer-list').find('li.selected').attr("data-answer-code")];
                $('.survey-pop-screen[data-screen="2"] .pop__bottom h3 span.subst').html(headingSubstitute);
                console.log(surveyData);
                setTimeout( () => {
                    nextScreen(currentScreen);
                }
                , 500);
                break;
            case "q2":
            case "q3":
                currentItem.parents('.answer-list').find('li').removeClass('selected');
                currentItem.addClass('selected');
                currentItem.parents('.pop__body').find('.dropdown-box').text(currentItem.text());
                if (currentItem.hasClass('other')) {
                    currentItem.parents('.pop__body').find('.input-other').show();
                    currentItem.parents('.answer-list').addClass('others');
                    $(document).on('keyup', currentItem.parents('.pop__body').find('.input-other'), e => {
                        if ($(e.target).val().length > 2) {
                            currentItem.parents('.pop__bottom').find('.dis').removeClass('dis');
                        }
                    }
                    );
                } else {
                    currentItem.parents('.pop__body').find('.input-other').hide().val('');
                    currentItem.parents('.answer-list').removeClass('others');
                    currentItem.parents('.pop__bottom').find('.dis').removeClass('dis');
                }
                currentItem.parents('.answer-list').hide();
            }
        }
        );
        $(document).on('click', '.survey-pop-screen.active .survey-cross', e => {
            e.preventDefault();
            let count = localStorage.getItem('career_survey_count') ? 2 : 1;
            localStorage.setItem('career_survey_count', count);
            if (count === 1) {
                localStorage.setItem('career_survey_status', 'dismissed');
                sessionStorage.setItem('session_survey_status', 'dismissed');
            } else {
                sessionStorage.removeItem('session_survey_status');
                localStorage.removeItem('career_survey_count');
                localStorage.setItem('career_survey_status', 'rejected');
            }
            $(e.target).parents('.survey-pop').remove();
            $('.pop__overlay').hide();
            $('body').removeClass('no-scroll');
        }
        );
        $(document).on('click', '.survey-pop-screen.active .popup__click.next', e => {
            e.preventDefault();
            const activeScreen = $(e.target).parents('.survey-pop-screen.active');
            if ($(e.target).hasClass('dis')) {
                return false;
            }
            questionText = activeScreen.find('.pop__bottom h3').text();
            if (!activeScreen.find('.answer-list li.selected').hasClass('other')) {
                if (activeScreen.find('.answer-list li.selected').text()) {
                    surveyData['answers'].push({
                        question: questionText,
                        answer: activeScreen.find('.answer-list li.selected').text()
                    });
                } else {
                    message('Warning', 'Please select an answer before proceeding.');
                    return false;
                }
            } else {
                if (activeScreen.find('.input-other').val()) {
                    surveyData['answers'].push({
                        question: questionText,
                        answer: `Other: ${activeScreen.find('.input-other').val()}`
                    });
                } else {
                    message('Warning', 'Please type in your answer before proceeding.');
                    return false;
                }
            }
            console.log(surveyData);
            const currentScreen = parseInt($(e.target).parents('.survey-pop-screen').attr('data-screen'));
            $('.s-dropdown').hide();
            if (currentScreen != 3) {
                nextScreen(currentScreen);
            }
            if ($(e.target).hasClass('submit') && !$(e.target).hasClass('dis')) {
                $.ajax({
                    type: 'POST',
                    url: '/api/v1/alison-surveys/recruitment',
                    data: surveyData,
                    success: function() {
                        nextScreen(currentScreen);
                        message('Success', 'Survey Submitted Successfully');
                        setTimeout( () => {
                            sessionStorage.removeItem('session_survey_status');
                            localStorage.removeItem('career_survey_count');
                            localStorage.setItem('career_survey_status', 'submitted');
                            $(e.target).parents('.survey-pop').remove();
                            $('.pop__overlay').hide();
                            $('body').removeClass('no-scroll');
                        }
                        , 5000);
                    },
                    error: function() {
                        message('Error', 'Survey Submission Failed');
                        setTimeout( () => {
                            sessionStorage.removeItem('session_survey_status');
                            localStorage.removeItem('career_survey_count');
                            localStorage.setItem('career_survey_status', 'failed_to_submit');
                            $(e.target).parents('.survey-pop').remove();
                            $('.pop__overlay').hide();
                            $('body').removeClass('no-scroll');
                        }
                        , 1000);
                    }
                });
            }
        }
        );
        $(document).on('click', '.survey-pop-screen.active .survey-back', e => {
            e.preventDefault();
            const currentScreen = parseInt($(e.target).parents('.survey-pop-screen').attr('data-screen'));
            if (surveyData.answers.length === 1) {
                surveyData.answers = [];
            } else {
                surveyData.answers = surveyData.answers.filter(item => item.question !== questionText);
            }
            console.log(surveyData);
            $('.s-dropdown').hide();
            prevScreen(currentScreen);
        }
        );
        $(document).on('click', '.survey-pop-screen.active .dropdown-box', e => {
            e.preventDefault();
            $(e.target).parents('.pop__body').find('.s-dropdown').show();
        }
        );
        function nextScreen(currentScreen) {
            const nextScreen = currentScreen + 1;
            $('.survey-pop-screen.active').removeClass('active');
            $(`.survey-pop-screen[data-screen=${nextScreen}]`).addClass('active');
        }
        function prevScreen(currentScreen) {
            const prevScreen = currentScreen - 1;
            $('.survey-pop-screen.active').removeClass('active');
            $(`.survey-pop-screen[data-screen=${prevScreen}]`).addClass('active');
        }
        $(document).ready(function() {
            if ($(window).width() <= 768) {
                if ($('#announcement').length && $('.survey-pop').length) {
                    let height = $('#announcement').height()
                      , top = $('.survey-pop').position().top
                      , offsetTop = height + 100;
                    $('.survey-pop').css({
                        'top': `${offsetTop}px`
                    });
                }
                if ($('#sale').length && $('.survey-pop').length) {
                    let height = $('#sale').height()
                      , top = $('.survey-pop').position().top
                      , offsetTop = height + 100;
                    $('.survey-pop').css({
                        'top': `${offsetTop}px`
                    });
                }
            }
        });
        $(document).on('click', '#sale a.close, #sale a.close span, #announcement a.close, #announcement a.close span', function(e) {
            e.stopPropagation;
            $('.survey-pop').css({
                'top': '80px'
            });
        });
    </script>
</section>
<div class="aff-over" style="display:none;"></div>
<div class="player-header header__outer--lms">
    <div class="player-header__row">
        <div class="player-header__left">
            <a href="/" id="alison_logo" class="logo">
                <img src="/html/site/img/header/alison-free-courses.svg" alt="Free Online Courses, Classes and Tutorials">
            </a>
        </div>
        <div class="player-header__center">
            <h1 class="page_title">
                <span class="module_bold">
                    Module <span class="current_module">1</span>
                    :
                </span>
                Introduction to Basic Electricity 
            </h1>
            <span class="icon-thick-chevron-down"></span>
            <div class="module-nav module-nav--hide" style="display: none">
                <div class="module-nav__inner">
                    <div class="module-nav__content">
                        <div class="module-nav__modules modules-scrollbar">
                            <div class="module-nav__module module-nav__module--open ">
                                <a href="https://alison.com/topic/learn/124675/introduction-to-basic-electricity-learning-outcomes " class="module-nav__module-title active ">
                                    <span></span>
                                    <h3>
                                        Module 1: Introduction to Basic Electricity <span class="icon-thick-chevron-up"></span>
                                    </h3>
                                </a>
                                <div class="module-nav__topics">
                                    <a data-id="124675" title="Introduction to Basic Electricity - Learning Outcomes" href="https://alison.com/topic/learn/124675/introduction-to-basic-electricity-learning-outcomes" class="module-nav__topic active completed_topic ">
                                        <span></span>
                                        <h4>Introduction to Basic Electricity - Learning Outcomes</h4>
                                    </a>
                                    <a data-id="124676" title="Fundamentals of Electricity" href="https://alison.com/topic/learn/124676/fundamentals-of-electricity" class="module-nav__topic current ">
                                        <span></span>
                                        <h4>Fundamentals of Electricity</h4>
                                    </a>
                                    <a data-id="124677" title="Electrical Units and Ohm’s Law" href="https://alison.com/topic/learn/124677/electrical-units-and-ohms-law" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Electrical Units and Ohm’s Law</h4>
                                    </a>
                                    <a data-id="124678" title="Basics of Direct Current (DC) Circuits" href="https://alison.com/topic/learn/124678/basics-of-direct-current-dc-circuits" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basics of Direct Current (DC) Circuits</h4>
                                    </a>
                                    <a data-id="124679" title="Basics of Alternating Current" href="https://alison.com/topic/learn/124679/basics-of-alternating-current" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basics of Alternating Current</h4>
                                    </a>
                                    <a data-id="124680" title="Introduction to Basic Electricity - Lesson Summary" href="https://alison.com/topic/learn/124680/introduction-to-basic-electricity-lesson-summary" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Introduction to Basic Electricity - Lesson Summary</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="module-nav__module module-nav__module--closed ">
                                <a href="https://alison.com/topic/learn/124681/basic-electricity-components-and-precautions-learning-outcomes " class="module-nav__module-title ">
                                    <span></span>
                                    <h3>
                                        Module 2: Basic Electricity Components and Precautions <span class="icon-thick-chevron-up"></span>
                                    </h3>
                                </a>
                                <div class="module-nav__topics" style="display: none">
                                    <a data-id="124681" title="Basic Electricity Components and Precautions - Learning Outcomes" href="https://alison.com/topic/learn/124681/basic-electricity-components-and-precautions-learning-outcomes" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basic Electricity Components and Precautions - Learning Outcomes</h4>
                                    </a>
                                    <a data-id="124682" title="Voltage Sources and Resistors" href="https://alison.com/topic/learn/124682/voltage-sources-and-resistors" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Voltage Sources and Resistors</h4>
                                    </a>
                                    <a data-id="124683" title="Capacitance and Capacitors" href="https://alison.com/topic/learn/124683/capacitance-and-capacitors" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Capacitance and Capacitors</h4>
                                    </a>
                                    <a data-id="124684" title="Magnets and Magnetism" href="https://alison.com/topic/learn/124684/magnets-and-magnetism" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Magnets and Magnetism</h4>
                                    </a>
                                    <a data-id="124685" title="Electrical Safety Measures" href="https://alison.com/topic/learn/124685/electrical-safety-measures" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Electrical Safety Measures</h4>
                                    </a>
                                    <a data-id="124686" title="Basic Electricity Components and Precautions - Lesson Summary" href="https://alison.com/topic/learn/124686/basic-electricity-components-and-precautions-lesson-summary" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Basic Electricity Components and Precautions - Lesson Summary</h4>
                                    </a>
                                </div>
                            </div>
                            <div class="module-nav__module module-nav__module--closed ">
                                <a href="https://alison.com/topic/learn/124687/understanding-basic-electricity-course-assessment " class="module-nav__module-title ">
                                    <span></span>
                                    <h3>
                                        Course assessment <span class="icon-thick-chevron-up"></span>
                                    </h3>
                                </a>
                                <div class="module-nav__topics" style="display: none">
                                    <a data-id="124687" title="Understanding Basic Electricity - Course Assessment" href="https://alison.com/topic/learn/124687/understanding-basic-electricity-course-assessment" class="module-nav__topic ">
                                        <span></span>
                                        <h4>Understanding Basic Electricity - Course Assessment</h4>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="user-progress">
                <div class="user-progress--inner">
                    <div class="user-progress--bar">
                        <div data-width="0" class="user-progress--fill"></div>
                    </div>
                </div>
                <div class="user-progress--percentage"></div>
                <div class="saved">
                    <div class="saved__card">
                        <span>
                        <img src="assets/Electricity training/Elecricity Training" alt="">Progress Saved
                        </span>
                        <span class="icon-cross2"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="player-header__right">
            <div class="player-header__progress">
                <h3>Module Progress</h3>
                <span class="module-progress">
                    <span>0%</span>
                    Complete
                </span>
                <div class="user-progress">
                    <div class="user-progress--inner">
                        <div class="user-progress--bar">
                            <div data-width="0" class="user-progress--fill"></div>
                        </div>
                    </div>
                    <div class="user-progress--percentage"></div>
                    <div class="saved">
                        <div class="saved__card">
                            <span>
                                <img src="assets/Electricity training/How ELECTRICITY works - working principle.mp4"/>Progress Saved
                            </span>
                            
                        </div>
                    </div>
                </div>
            </div>        
                        <div class="favorite-wrap">
                            <div id="toggle-favorite" data-is_favorite="initial">
                                <span class="icon-favourite-default icon-favorite--header icon-heart"></span>
                            </div>
                            <div id="favorite-tooltip"></div>
                        </div>
                    </div>
                </div>
            </span>
            <div class="logo-container">
                <span href class="user__avatar">
                    <img src="https://alison.com/images/users/default/23437062.jpg" alt="Habtom Araya Bahta" class="learning-environment-avatar learning-environment-avatar--sm" style>
                </span>
            </div>
        </div>
    </div>
</div>
kkkkkkkkkkkkkkkkkkkkkk