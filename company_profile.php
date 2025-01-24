<?php

include('db.php');
include('header.php');

if (!isset($_SESSION['user_id'])) {
   header('Location: login.php');
   exit;
}

// Fetch courses from the database
$sql = "SELECT * FROM courses";
$result = $conn->query($sql);

if ($result === false) {
   die('Error: ' . htmlspecialchars($conn->error));
}

$courses = [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title> Company profile</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.2/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="assets/03-styles.css">

</head>
<body>

<header class="header">
   
   <section class="flex">

      <a href="home.php" class="logo">Education</a>

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
         <img src="assets/images_courses/pic-2.jpg" class="image" alt="">
         <h3 class="name">Gerar Isaac <br>Training Center (GITC)</h3>
         <p class="role">Company Profile</p>
         <a href="company_profile.php" class="btn">view profile</a>
         
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
         
         </div>
   <div class="profile">
      <img src="assets/images_courses/Gerar Isaac .png" class="image" alt="">
      <h3 class="name">Company Profile</h3>
      <p class="role"></p>
      <a href="company_profile.php" class="btn">view profile</a>
   </div>

   <nav class="navbar">
      <a href="home.php"><i class="fas fa-home"></i><span>home</span></a>
      <a href="about.php"><i class="fas fa-question"></i><span>about</span></a>
      <a href="courses.php"><i class="fas fa-graduation-cap"></i><span>courses</span></a>
      <a href="teachers.php"><i class="fas fa-chalkboard-user"></i><span>teachers</span></a>
      <a href="contact.php"><i class="fas fa-headset"></i><span>contact us</span></a>
   </nav>

</div>

<section class="user-profile">

   <h1 class="heading">Gerar Isaac Training Center (GITC)</h1>

   <div class="info">

      <div class="user">
         <img src="assets/images_courses/Gerar Isaac .png" alt="">
         <h4>
            Mission Statement:<br>
            Our mission is to make quality education accessible to everyone, anywhere, by leveraging technology to provide innovative and personalized learning experiences.
            
            Vision:<br>
            To be a global leader in online education, transforming the way people learn and empowering them to achieve their full potential.
            
            About Us:<br>
            Founded in January, 2023, [Gerar Isaac Training Center (GITC)] is a pioneering online education platform dedicated to providing high-quality, flexible learning opportunities to students and professionals worldwide. Our platform offers a wide range of courses across various disciplines, designed and taught by industry experts and renowned educators.
            
            History:<br>
            Gerar Isaac Training Center (GITC) was established by Habtom Araya-ACCA with the goal of bridging the gap between traditional education and the evolving demands of the modern world. Since our inception, we have grown from a small startup to a leading online education provider, serving millions of learners globally.
            
            Services:<br>
            
            Online Courses: Comprehensive courses in subjects like technology, business, arts, and sciences.
            Professional Certifications: Accredited programs that enhance career opportunities.
            Corporate Training: Customized training solutions for businesses to upskill their workforce.
            Interactive Learning: Live classes, webinars, and interactive sessions with instructors.
            Support Services: 24/7 customer support, academic advising, and career counseling.<br>
            Target Market:<br>
            
            Students: High school, college, and university students seeking supplementary education.
            Professionals: Individuals looking to advance their careers with additional skills and certifications.
            Corporations: Businesses requiring tailored training programs for their employees.
            Lifelong Learners: Anyone interested in personal development and continuous learning.<br>
            Achievements:<br>
            
            Over [Number] courses offered.<br>
            Serving [Number] students in [Number] countries.<br>
            Partnerships with [Number] top universities and organizations.<br>
            Awarded [Awards and Recognitions].<br>
            Testimonials:<br>
            "[Your Company Name] has transformed my career. The courses are comprehensive, and the instructors are top-notch."<br>
            
            [Student/Professional Name]<br>
            "The corporate training solutions provided by [Your Company Name] have significantly improved our team's productivity."
            
            [Client Company Name]<br>
            Our Team:<br>
            
            Founder & CEO: [Habtom Araya-ACCA]<br>
            Chief Operating Officer: [Samrawit Eshetie]<br>
            Chief Technology Officer: [Yared Asefa]<br>
            Chief Academic Officer: [Mesfin Tamru]<br>
            Key Instructors: <br>
               Habtom Araya-ACCA , Masters<br>
               Samrawit Eshetie , Masters<br>
               Yared Asefa , Masters<br>
            Future Goals:<br>
            
            Expand our course offerings to include more emerging fields.<br>
            Enhance our platform with AI-driven personalized learning experiences.<br>
            Establish more strategic partnerships with leading educational institutions and companies.<br>
            Continue to innovate and adapt to the evolving educational landscape.<br>

           </h4>
         <p> Contact Us:<br>
            
            Address: <br>
            Phone: <br>
            Email: <br>
            Website: <br>
            Social Media:
            [Facebook];
            [Twitter];
            [LinkedIn];
            [Instagram];
            
           </p>
         <a href="update.html" class="inline-btn">upda profile</a>
      </div>
   
      <div class="box-container">
   
         <div class="box">
            <div class="flex">
               <i class="fas fa-bookmark"></i>
               <div>
                  <span>74</span>
                  <p>Students Enrolled</p>
               </div>
            </div>
            <a href="#" class="inline-btn">view Enrolled</a>
         </div>
   
         <div class="box">
            <div class="flex">
               <i class="fas fa-heart"></i>
               <div>
                  <span>33</span>
                  <p>videos liked</p>
               </div>
            </div>
            <a href="#" class="inline-btn">view liked</a>
         </div>
   
         <div class="box">
            <div class="flex">
               <i class="fas fa-comment"></i>
               <div>
                  <span>12</span>
                  <p>videos comments</p>
               </div>
            </div>
            <a href="#" class="inline-btn">view comments</a>
         </div>
   
      </div>
   </div>

</section>

<footer class="footer">

   &copy; copyright @ 2022 by <span>mr. web designer</span> | all rights reserved!

</footer>

<!-- custom js file link  -->
<script src="js/script.js"></script>

   
</body>
</html>