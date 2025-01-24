<?php
include('db.php');
include('header_loggedin.php');

// Check if the user is logged in and has the 'user' role
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Prepare and execute the query to get courses
    $stmt = $pdo->prepare('SELECT course_id, course_title, course_subtitle, tutor_name, tutor_image, course_videos, thumb_image FROM courses');
    $stmt->execute();

    // Check if there are any courses available
    if ($stmt->rowCount() > 0) {
        echo '<div class="container my-3">';
        echo '<h1 class="text-center mb-4">Available Courses</h1>';
        echo '<div class="row g-4">'; // Row to hold cards with spacing

        // Loop through each course and display its data
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $course_id = $row['course_id'];
            $course_title = $row['course_title'];
            $course_subtitle = $row['course_subtitle'];
            $course_videos = $row['course_videos'];
            $thumb_image = $row['thumb_image'];
            $tutor_name = $row['tutor_name'];
            $tutor_image = $row['tutor_image'];

            // Display course data in Bootstrap card
            echo '
            <div class="col-md-3">
                <div class="card course-card shadow-lg h-100">
                    <img src="' . htmlspecialchars($thumb_image) . '" class="card-img-top" alt="' . htmlspecialchars($course_title) . '">
                    <div class="card-body">
                        <h5 class="card-title text-truncate">' . htmlspecialchars($course_title) . '</h5>
                        <p class="card-text small text-muted text-truncate">' . htmlspecialchars($course_subtitle) . '</p>
                        
                        <!-- Tutor Info -->
                        <div class="tutor-info d-flex align-items-center mb-3">
                            <img src="' . htmlspecialchars($tutor_image) . '" class="tutor-img rounded-circle" alt="' . htmlspecialchars($tutor_name) . '">
                            <span class="ms-2">' . htmlspecialchars($tutor_name) . '</span>
                        </div>

                        <a href="' . htmlspecialchars($course_videos) . '" class="btn btn-course w-100" target="_blank">Watch Video</a>
                    </div>
                </div>
            </div>';
        }

        echo '</div>'; // End row
        echo '</div>'; // End container

    } else {
        echo "<p class='text-center'>No courses available at the moment.</p>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>


<style>
/* General Card Styling */
.card-container .card {
    border: none;
    border-radius: 12px;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    background-color: #f9f9f9; /* Soft light gray for background */
}

.card-container .card:hover {
    transform: translateY(-10px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
}

/* Card Header Image */
.card-img-top {
    height: 180px;
    object-fit: cover;
    border-top-left-radius: 12px;
    border-top-right-radius: 12px;
}

/* Course Title Styling */
.card-title {
    font-weight: bold;
    font-size: 1.1rem;
    color: #333; /* Dark gray for title */
}

/* Course Description Styling */
.card-text {
    font-size: 0.875rem;
    line-height: 1.2;
    color: #666; /* Slightly lighter gray for description */
}

/* Tutor Image and Name */
.tutor-info {
    display: flex;
    align-items: center;
}

.tutor-img {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid #007bff; /* Subtle blue border around tutor image */
}

.tutor-info span {
    font-size: 0.875rem;
    font-weight: bold;
    color: #007bff; /* Blue for tutor name for a professional feel */
}

/* Button Styling */
.btn-course {
    background-color: #007bff; /* Calm, professional blue */
    color: white;
    border: none;
    padding: 8px 15px;
    font-size: 0.875rem;
    border-radius: 6px;
    transition: background-color 0.3s ease;
}

.btn-course:hover {
    background-color: #0056b3; /* Darker blue on hover */
}

/* Row and Card Spacing */
.row.g-4 {
    gap: 1.5rem; /* Add consistent spacing between cards */
}

/* Ensure Each Card Takes 1/4 Width (4 cards per row) */
.col-md-3 {
    flex: 1 0 21%;  /* Flexbox trick to make sure 4 cards fit in a row */
    max-width: 24%;  /* Ensure cards fit in one row */
    margin-bottom: 20px;  /* Add some spacing between rows */
}

/* Card Background Color Variation */
.card.course-card:nth-child(1) {
    background-color: #e3f2fd; /* Soft light blue for the first card */
}

.card.course-card:nth-child(2) {
    background-color: #c8e6c9; /* Soft light green for the second card */
}

.card.course-card:nth-child(3) {
    background-color: #f1f8e9; /* Soft light yellow-green for the third card */
}

.card.course-card:nth-child(4) {
    background-color: #f3f4f6; /* Light gray for the fourth card */
}

/* Ensure 4 cards in one row */
@media (max-width: 991px) {
    .col-md-3 {
        flex: 1 0 48%;  /* Two cards per row on medium screens */
        max-width: 48%; 
    }
}

@media (max-width: 767px) {
    .col-md-3 {
        flex: 1 0 100%;  /* One card per row on small screens */
        max-width: 100%;
    }
}

</style>