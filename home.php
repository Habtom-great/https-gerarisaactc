
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac Training Center</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

    <style>
        /* Global Settings */
        body {
            padding-top: 60px; /* Reduced padding between header and content */
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }

        /* Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Rolling Time and Weather Section */
        .rolling-info {
            background: linear-gradient(90deg, #f39c12, #8e44ad, #3498db);
            padding: 10px 0;
            color: white;
            font-size: 1.1rem;
            text-align: center;
            display: flex;
            justify-content: space-around;
            overflow: hidden;
            white-space: nowrap;
        }

        .rolling-info .rolling-item {
            display: inline-block;
            margin: 0 20px;
            animation: roll 15s linear infinite;
        }

        @keyframes roll {
            0% {
                transform: translateX(100%);
            }
            100% {
                transform: translateX(-100%);
            }
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 120px;
            left: 0;
            width: 200px;
            height: calc(100% - 160px);
            background-color: #343a40;
            color: white;
            padding: 20px;
            border-right: 3px solid #007bff;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .sidebar a:hover {
            background-color: #007bff;
        }

        /* Main Section */
        .section {
            margin-left: 230px;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-top: 10px;
        }

        /* Colorful Sections */
        #home {
            background-color: #ecf0f1;
        }

        #about {
            background-color: #f8f9fa;
        }

        #courses {
            background-color: #e1f5fe;
        }

        #contact {
            background-color: #ffecb3;
        }

        /* Footer */
        footer {
            background-color: #343a40;
            color: white;
            padding: 20px 0;
            text-align: center;
        }

        footer a {
            color: white;
            margin: 0 10px;
        }

        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .section {
                margin-left: 0;
            }

            .sidebar {
                width: 100%;
                top: 120px;
                height: auto;
            }
        }
    </style>
</head>

<body>

    <!-- Rolling Time and Weather Section -->
    <div class="rolling-info">
        <div class="rolling-item">
            <h5>Washington DC</h5>
            <span>Time: <span id="dcClock">--:--:--</span></span> | 
            <span>Weather: <span id="dcWeather">Loading...</span></span>
        </div>
        <div class="rolling-item">
            <h5>Canada</h5>
            <span>Time: <span id="canadaClock">--:--:--</span></span> | 
            <span>Weather: <span id="canadaWeather">Loading...</span></span>
        </div>
        <div class="rolling-item">
            <h5>Ethiopia</h5>
            <span>Time: <span id="ethiopiaClock">--:--:--</span></span> | 
            <span>Weather: <span id="ethiopiaWeather">Loading...</span></span>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#home">
                <img src="assets/logos/1.png-2.png" alt="GITC Logo" style="max-height: 40px;"> Gerar Isaac Training Center (GITC)
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item"><a class="nav-link" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#About Us">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact Us</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="languageDropdown" data-toggle="dropdown">Language</a>
                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="?lang=en">English</a>
                            <a class="dropdown-item" href="?lang=am">Amharic</a>
                            <a class="dropdown-item" href="?lang=ti">Tigrinya</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="section">
        <div id="home">
            <h2>Welcome to Gerar Isaac Training Center</h2>
            <p>Discover our various courses and programs tailored for your needs.</p>
            <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
            <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="assets/images_courses/first round-final exam.jpg" class="d-block w-100" alt="First slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images_courses/2nd round graduating  students peach tree exam ..jpg" class="d-block w-100" alt="Second slide">
            </div>
            <div class="carousel-item">
                <img src="assets/images_courses/3rd round   students training session .jpg" class="d-block w-100" alt="Third slide">
            </div>
        </div>
        <a class="carousel-control-prev" href="#carouselExample" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExample" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

<!-- About Us Section -->
<div id="About Us">
    <div class="container">
        <h2>About Us</h2>
        <p>At Gerar Isaac Training Center (GITC), we are committed to providing high-quality<br>
         education in various fields, helping students achieve their academic and professional goals.
        
We are in class/Online Courses, your one-stop solution for learning. Our platform offers a wide range of comprehensive courses designed to help you master the principles and practices of accounting.

we understand the importance of quality education, which is why our courses are taught by experienced professionals in the field. Whether you are a beginner or an experienced accountant looking to enhance your skills, we have courses tailored to meet your needs.

Our mission is to make accounting,basic electrical engineering and web development education accessible to everyone, regardless of their location or background. With our user-friendly platform and flexible learning options, you can learn at your own pace and from anywhere in the world.

Join us today and take the first step towards advancing your career!
        </p>
    </div>



<style>
    /* Container and Section Styling */
    #courses {
        padding: 50px 0;
        background-color: #f8f9fa;
    }

    /* Card Styling */
    .card {
        transition: transform 0.3s ease-in-out;
        border-radius: 15px;
        overflow: hidden;
    }

    /* Hover Effects */
    .card:hover {
        transform: scale(1.05);
    }

    /* Logo Styling */
    .logo-container {
        position: relative;
        display: flex;
        justify-content: center;
    }

    .logo-container img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Add Animation */
    .card {
    animation: rolling 4s forwards; /* Use 'forwards' to maintain the final state after animation */
}

@keyframes rolling {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}


    /* Button Styling */
    .btn {
        font-weight: bold;
        letter-spacing: 1px;
        padding: 8px 20px;
    }

    /* Title and Text Styling */
    .card-title {
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .card-text {
        font-size: 0.9rem;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        .card {
            margin-bottom: 30px;
        }
    }
</style>

        <!-- Courses Section -->
        <div id="courses" class="mt-5">
            <h2>Courses</h2>
            <p>Explore our diverse range of courses available for enrollment.</p>
            
        <h2 class="text-center mb-5" style="font-size: 2rem; color: #007bff; font-weight: bold;">Our Course Offers</h2>
        <div class="row justify-content-center">

            <!-- Accounting Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Accounting Image.png" class="rounded-circle" alt="Accounting">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-primary">Accounting</h5>
                        <p class="card-text">Explore a range of accounting courses including:</p>
                        <ul class="list-unstyled">
                            <li>Principles of Accounting</li>
                            <li>Cost Accounting</li>
                            <li>Financial Accounting</li>
                        </ul>
                        <a href="login.php" class="btn btn-primary btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Accounting Software -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/Peachtree-logo.jpg" class="rounded-circle" alt="Accounting Software">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-success">Accounting Software</h5>
                        <p class="card-text">Get hands-on experience with:</p>
                        <ul class="list-unstyled">
                            <li>Peach Tree</li>
                            <li>Tally</li>
                            <li>QuickBooks</li>
                        </ul>
                        <a href="accounting_software.php" class="btn btn-success btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Electrical Engineering -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/electrical-engineering-logo.avif" class="rounded-circle" alt="Electrical Engineering">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-warning">Electrical Engineering</h5>
                        <p class="card-text">Courses include:</p>
                        <ul class="list-unstyled">
                            <li>Basic Electricity</li>
                            <li>Basic Electronics</li>
                        </ul>
                        <a href="electrical_engineering.php" class="btn btn-warning btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

            <!-- Programming Courses -->
            <div class="col-md-6 col-lg-3 mb-4">
                <div class="card shadow-lg text-center border-0 h-100">
                    <div class="logo-container mt-3">
                        <img src="assets/logos/html,css,js.jpeg" class="rounded-circle" alt="Programming">
                    </div>
                    <div class="card-body p-3">
                        <h5 class="card-title text-info">Programming</h5>
                        <p class="card-text">Learn the essentials of:</p>
                        <ul class="list-unstyled">
                            <li>Website Development (HTML, CSS, JS)</li>
                            <li>PHP Tutorial</li>
                            <li>Database Management</li>
                        </ul>
                        <a href="programming_courses.php" class="btn btn-info btn-sm">Learn More</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
      
<!-- Testimonials Section -->
        <div id="testimonials" class="mt-5">
            <h2>Testimonials</h2>
            <p>Read what our students say about their experience.</p>
       <!-- First Round Accounting Graduates -->
         <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">First Round Accounting Graduates</h3>
       <div class="row">
       
           <!-- Testimonial 1 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-1st Round/Esaias Amanuel Zeregabir Acc 1st R-3.jpeg" class="rounded-circle mb-3" alt="Eseas Amanuel" style="width: 100px; height: 100px;">
                   <h5 style="color: #007bff;">Eseas Amanuel</h5>
                   <p class="text-muted">"The course deepened my understanding of accounting principles. I’m confident in my skills now!"</p>
               </div>
           </div>

           <!-- Testimonial 2 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-1st Round/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Feven Gebremichael" style="width: 100px; height: 100px;">
                   <h5 style="color: #28a745;">Feven Gebremichael</h5>
                   <p class="text-muted">"The accounting course is very detailed and easy to understand. Highly recommended!"</p>
               </div>
           </div>

           <!-- Testimonial 3 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-1st Round/Fireselam  bokre Gebresilasia Acc 1st R-2.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                   <h5 style="color: #ffc107;">Fireselam Bokre</h5>
                   <p class="text-muted">"Clear, concise, and practical. This course gave me the confidence to pursue my accounting career."</p>
               </div>
           </div>

           <!-- Testimonial 4 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-1st Round/Lidia weldu Hayelom Acc 1st R-4.jpeg " class="rounded-circle mb-3" alt="Fireselam Bokre" style="width: 100px; height: 100px;">
                   <h5 style="color: #17a2b8;">Lidia weldu</h5>
                   <p class="text-muted">"The Online Accounting Course has significantly improved my understanding of accounting principles."</p>
               </div>
           </div>
       </div>

       <!-- Second Type Accounting Graduates -->
       <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Second Round Accounting Graduates</h3>
       <div class="row">
           <!-- Testimonial 5 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-2nd Round/Betelihem.jpg " class="rounded-circle mb-3" alt="Betelihem" style="width: 100px; height: 100px;">
                   <h5 style="color: #dc3545;">Tsegay</h5>
                   <p class="text-muted">"The course content is detailed and the instructors are very knowledgeable."</p>
               </div>
           </div>

           <!-- Testimonial 6 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-2nd Round/Tsegay.jpg" class="rounded-circle mb-3" alt="Tsegay" style="width: 100px; height: 100px;">
                   <h5 style="color: #6f42c1;">Amanuel</h5>
                   <p class="text-muted">"I highly recommend this course to anyone looking to enhance their accounting skills."</p>
               </div>
           </div>

           <!-- Testimonial 7 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg" class="rounded-circle mb-3" alt="Amanuel" style="width: 100px; height: 100px;">
                   <h5 style="color: #ff5733;">Helen Tesfaye</h5>
                   <p class="text-muted">"A fantastic course that greatly improved my accounting knowledge."</p>
               </div>
           </div>

           <!-- Testimonial 8 -->
           <div class="col-md-6 col-lg-3 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px; transition: 0.3s;">
                   <img src="assets/home_page/images/Students-2nd Round/Amanuel.jpg " class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                   <h5 style="color: #007bff;">Samuel Bekele</h5>
                   <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
               </div>
           </div>
       </div>
   </div>

           <!-- Testimonial 8 -->
           <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Third Round Accounting Graduates</h3>
       <div class="row">
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                   <img src="assets/students/66b2aac503c7e.jpg" class="rounded-circle mb-3" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                   <h5 style="color: #007bff;">Samuel Bekele</h5>
                   <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
               </div>
           </div>
           <!-- Testimonial 9 -->
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                   <img src="assets/students/66b2beac03e52.jpg" class="rounded-circle mb-3" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                   <h5 style="color: #28a745;">Mekdes Tesfay</h5>
                   <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
               </div>
           </div>
           <!-- Testimonial 10 -->
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow" style="background-color: #ffffff; border-radius: 10px;">
                   <img src="assets/students/FEVEN GEBREMICHAEL KIDANE Acc 1st R-1.jpeg" class="rounded-circle mb-3" alt="Marta Haile" style="width: 100px; height: 100px;">
                   <h5 style="color: #ffc107;">Marta Haile</h5>
                   <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
               </div>
           </div>
       </div>


       <!-- Fourth Round Accounting Graduates -->
       <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Fourth Round Accounting Graduates</h3>
       <div class="row">
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                   <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                   <h5 style="color: #007bff;">Samuel Bekele</h5>
                   <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
               </div>
           </div>
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                   <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                   <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                   <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
               </div>
           </div>
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                   <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                   <h5 style="color: #ffc107;">Marta Haile</h5>
                   <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
               </div>
           </div>
       </div>
   </div>
</section>

 <!-- Fifth Round Accounting Graduates -->
 <h3 class="text-center mb-4" style="color: #dc3545; font-size: 24px; font-weight: bold; text-shadow: 1px 1px #888;">Fifth Round  Accounting Graduates</h3>
       <div class="row">
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                   <img src="assets/students/pic-2.jpg" class="rounded-circle mb-3 border border-primary" alt="Samuel Bekele" style="width: 100px; height: 100px;">
                   <h5 style="color: #007bff;">Samuel Bekele</h5>
                   <p class="text-muted">"The GITC team has made learning fun and engaging. The online accounting course was top-notch!"</p>
               </div>
           </div>
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                   <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-success" alt="Mekdes Alemayehu" style="width: 100px; height: 100px;">
                   <h5 style="color: #28a745;">Mekdes Alemayehu</h5>
                   <p class="text-muted">"I gained so much confidence after completing the web development course. Highly recommend GITC!"</p>
               </div>
           </div>
           <div class="col-md-6 col-lg-4 mb-4">
               <div class="testimonial-box text-center p-4 shadow-sm" style="background-color: #fff; border-radius: 10px;">
                   <img src="assets/students/pic-2.jpg"  class="rounded-circle mb-3 border border-warning" alt="Marta Haile" style="width: 100px; height: 100px;">
                   <h5 style="color: #ffc107;">Marta Haile</h5>
                   <p class="text-muted">"The skills I learned at GITC have opened so many doors for my career. Thank you, GITC!"</p>
               </div>
           </div>
       </div>
   </div>
</section>

<?php
// Initialize variables
$success_message = $error_message = '';
$name = $telephone = $email = $message = ''; // Initialize all variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get form data and trim input
    $name = trim($_POST['name']);
    $telephone = trim($_POST['telephone'] ?? '');
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Form validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif (empty($name) || empty($telephone) || empty($message)) {
        $error_message = "Name, telephone, and message fields are required.";
    } else {
        // Database connection using PDO
        $servername = 'localhost';
        $username = 'root'; // Replace with your MySQL username
        $password = ""; // Replace with your MySQL password
        $dbname = 'accounting_course'; // Replace with your database name

        try {
            $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            // Set the PDO error mode to exception
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Prepare the SQL statement to insert the data
            $stmt = $pdo->prepare("INSERT INTO contact (name, telephone, email, message) VALUES (:name, :telephone, :email, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Your message has been sent successfully!";
                // Clear the form fields after successful submission
                $name = $telephone = $email = $message = '';
            } else {
                $error_message = "There was an error sending your message. Please try again.";
            }

        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}
?>


    <style>
        body {
            background-color: #f8f9fa;
            /* Add padding at the bottom to prevent footer overlap */
            padding-bottom: 70px; /* Adjust this value if needed */
        }
        .contact-section {
            padding: 50px 0;
        }
        .contact-header {
            text-align: left;
            margin-bottom: 50px;
        }
        .contact-container {
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
        }
        .form-section, .address-section {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
            flex: 1 1 45%;
        }
        .address-section h4 {
            color: #007bff;
        }
        .map-container {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            margin-top: 20px;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: fixed;
            bottom: 0;
            width: 100%;
            z-index: 100;
        }
        @media (max-width: 768px) {
            .contact-container {
                flex-direction: column;
            }
            .form-section, .address-section {
                flex: 1 1 100%;
                margin-right: 0;
            }
        }
        
  
        body {
            background-color: #f8f9fa;
            padding-bottom: 150px; /* Increased padding to avoid footer overlap */
        }

        .contact-section {
            padding: 50px 0;
        }

        .contact-header {
            text-align: left;
            margin-bottom: 50px;
        }

        .form-section, .address-section {
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border-radius: 8px;
        }

        .form-section {
            margin-right: 30px;
        }

        .address-section h4 {
            color: #007bff;
        }

        .map-container {
            position: relative;
            padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        /* Adjust padding and gap between sections */
        @media (max-width: 768px) {
            .form-section {
                margin-right: 0;
                margin-bottom: 30px;
            }
        }

        footer {
            background-color: #343a40;
            color: white;
            text-align: center;
            padding: 20px 0;
            position: relative;
            width: 100%;
            bottom: 0;
        }
    </style>


<!-- Contact Section -->
<section id="contact" class="contact-section">
    <div class="container">
        <h2 class="contact-header">Contact Us</h2>
        <div class="contact-container">
            <!-- Contact Form -->
            <div class="form-section">
                <?php if ($success_message): ?>
                    <div class="alert alert-success"><?php echo $success_message; ?></div>
                <?php elseif ($error_message): ?>
                    <div class="alert alert-danger"><?php echo $error_message; ?></div>
                <?php endif; ?>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="name">Name<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
                    </div>
                    <div class="form-group">
                        <label for="telephone">Telephone<span style="color:red;">*</span></label>
                        <input type="text" class="form-control" id="telephone" name="telephone" required value="<?php echo htmlspecialchars($telephone); ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">Email<span style="color:red;">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                    </div>
                    <div class="form-group">
                        <label for="message">Message<span style="color:red;">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
                    </div>
                    <button type="submit" name="submit" class="btn btn-primary btn-block">Send Message</button>
                </form>
            </div>

            <!-- Address and Map Section -->
            <div class="address-section">
                <h4>Our Address</h4>
                <p><strong>Gerar Isaac Training Center</strong></p>
                <p>Meskel Flower, Bahgmer Building</p>
                <p>Addis Ababa, Ethiopia</p>
                <p><strong>Phone:</strong> +251 911 123456</p>
                <p><strong>Email:</strong> info@gerarisaac.com</p>

                <div class="map-container">
                    <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3942.221978156433!2d38.76136887493415!3d9.005601393496469!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b857f7dbe2cf3%3A0xdfb4b7d08319f7c6!2sMeskel%20Flower!5e0!3m2!1sen!2set!4v1694106543321!5m2!1sen!2set"
                        width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
                        
            </div>
        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>


<?php
// Include footer
include('footer.php');
?>
</body>
</html>



    <style>
        /* Fixed Navbar */
        .navbar {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        /* Content Margin after fixed navbar */
        body {
            padding-top: 60px;
            background-color: #f4f4f4;
        }

        /* Top section for Clock, Weather */
        .top-info-container {
            background-color: #007bff;
            color: white;
            padding: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-info-container .info-section {
            display: flex;
            align-items: center;
            margin-right: 20px;
        }

        .top-info-container .info-section h5 {
            margin-bottom: 0;
            margin-right: 10px;
        }

        /* Sidebar Styling */
        .sidebar {
            position: fixed;
            top: 140px;
            left: 0;
            width: 200px;
            height: 100%;
            background-color: #343a40;
            padding: 20px;
            border-right: 3px solid #007bff;
            color: white;
        }

        .sidebar a {
            display: block;
            color: white;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            background-color: #495057;
            text-align: center;
            font-size: 1rem;
            transition: background-color 0.3s ease;
        }

        
    </style>
</head>


<!-- Sidebar -->
<div class="sidebar">
    <h4>Navigation</h4>
    <a href="#home">Home</a>
    <a href="#About Us">About Us</a>
    <a href="#courses">Courses</a>
    <a href="#testimonials">Testimonials</a>
    <a href="#contact">Contact Us</a>
</div>
<body>
<script>
    // Clock Functionality
    function updateClocks() {
        const nowDC = new Date().toLocaleString("en-US", {timeZone: "America/New_York"});
        const nowCA = new Date().toLocaleString("en-US", {timeZone: "America/Toronto"});
        const nowET = new Date().toLocaleString("en-US", {timeZone: "Africa/Addis_Ababa"});

        // Update digital clocks
        document.getElementById("dcClock").innerText = nowDC;
        document.getElementById("canadaClock").innerText = nowCA;
        document.getElementById("ethiopiaClock").innerText = nowET;

        // Update analog clocks
        const [hoursDC, minutesDC, secondsDC] = nowDC.split(/:| /).map(Number);
        const [hoursCA, minutesCA, secondsCA] = nowCA.split(/:| /).map(Number);
        const [hoursET, minutesET, secondsET] = nowET.split(/:| /).map(Number);

        document.getElementById("hourHandDC").style.transform = `translateX(-50%) rotate(${(hoursDC % 12) * 30 + minutesDC * 0.5}deg)`;
        document.getElementById("minuteHandDC").style.transform = `translateX(-50%) rotate(${minutesDC * 6}deg)`;
        document.getElementById("secondHandDC").style.transform = `translateX(-50%) rotate(${secondsDC * 6}deg)`;

        document.getElementById("hourHandCA").style.transform = `translateX(-50%) rotate(${(hoursCA % 12) * 30 + minutesCA * 0.5}deg)`;
        document.getElementById("minuteHandCA").style.transform = `translateX(-50%) rotate(${minutesCA * 6}deg)`;
        document.getElementById("secondHandCA").style.transform = `translateX(-50%) rotate(${secondsCA * 6}deg)`;

        document.getElementById("hourHandET").style.transform = `translateX(-50%) rotate(${(hoursET % 12) * 30 + minutesET * 0.5}deg)`;
        document.getElementById("minuteHandET").style.transform = `translateX(-50%) rotate(${minutesET * 6}deg)`;
        document.getElementById("secondHandET").style.transform = `translateX(-50%) rotate(${secondsET * 6}deg)`;
    }

    // Weather Fetching
    async function fetchWeather() {
        const apiKey = 'YOUR_API_KEY'; // Replace with your actual API key
        const locations = {
            dc: "Washington,DC",
            ca: "Toronto,CA",
            et: "Addis Ababa,ET"
        };

        for (const [key, location] of Object.entries(locations)) {
            const response = await fetch(`https://api.openweathermap.org/data/2.5/weather?q=${location}&appid=${apiKey}&units=metric`);
            const data = await response.json();
            document.getElementById(`weather${key.toUpperCase()}`).innerText = `${data.name}: ${data.weather[0].description}, ${data.main.temp}°C`;
        }
    }

    // Initialize Clocks and Weather
    setInterval(updateClocks, 1000);
    fetchWeather();
</script>




