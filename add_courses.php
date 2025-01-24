<?php
session_start();
include('db.php');
include('header.php');

// Ensure the user is logged in and has the role of 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $course_note = $_POST['course_note'] ?? '';
    $video_link = $_POST['video_link'] ?? '';
    $tutor_name = $_POST['tutor_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $instructor_id = $_SESSION['user_id'];

    // Determine the course ID prefix based on the selected category
    switch ($category) {
        case 'Accounting':
            $course_prefix = 'ACC-';
            break;
        case 'Electrical Engineering':
            $course_prefix = 'ELE-';
            break;
        case 'Programming':
            $course_prefix = 'PRO-';
            break;
        default:
            $course_prefix = 'GEN-'; // Fallback if no category is selected
    }

    // Generate a unique course ID
    $course_id = $course_prefix . strtoupper(uniqid());

    // Handle file uploads for tutor image, thumbnail, and additional files
    $tutor_image_path = null;
    $thumb_image_path = null;
    $uploaded_files = [];

    if (isset($_FILES['tutor_image']) && $_FILES['tutor_image']['error'] == 0) {
        $tutor_image_path = 'uploads/tutors/' . basename($_FILES['tutor_image']['name']);
        move_uploaded_file($_FILES['tutor_image']['tmp_name'], $tutor_image_path);
    }

    if (isset($_FILES['thumb_image']) && $_FILES['thumb_image']['error'] == 0) {
        $thumb_image_path = 'uploads/thumbnails/' . basename($_FILES['thumb_image']['name']);
        move_uploaded_file($_FILES['thumb_image']['tmp_name'], $thumb_image_path);
    }

    if (!empty($_FILES['course_files']['name'][0])) {
        foreach ($_FILES['course_files']['tmp_name'] as $index => $tmp_name) {
            $file_name = $_FILES['course_files']['name'][$index];
            $file_path = 'uploads/courses/' . basename($file_name);
            move_uploaded_file($tmp_name, $file_path);
            $uploaded_files[] = $file_path;
        }
    }

    try {
        // Insert the new course into the database
        $stmt = $pdo->prepare("
            INSERT INTO courses (course_id, course_title, description, course_note, video_link, tutor_name, tutor_image, thumb_image, instructor_id, category) 
            VALUES (:course_id, :course_title, :description, :course_note, :video_link, :tutor_name, :tutor_image, :thumb_image, :instructor_id, :category)
        ");
        
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':course_title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':course_note', $course_note);
        $stmt->bindParam(':video_link', $video_link);
        $stmt->bindParam(':tutor_name', $tutor_name);
        $stmt->bindParam(':tutor_image', $tutor_image_path);
        $stmt->bindParam(':thumb_image', $thumb_image_path);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':category', $category);
        $stmt->execute();

        echo "<p class='alert alert-success'>Course added successfully with ID: {$course_id}!</p>";
    } catch (PDOException $e) {
        echo "<p class='alert alert-danger'>Error: " . $e->getMessage() . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: 30px auto;
            padding: 15px;
            background-color: #f4f4f9;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            font-size: 24px;
            font-weight: 600;
            text-align: center;
            color: #333;
            margin-bottom: 15px;
          
        }
        .form-group label {
            font-weight: 500;
            color: #555;
        }
        .form-control {
            height: 35px;
            padding: 5px;
            font-size: 14px;
            
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
            padding: 10px;
            font-size: 16px;
            width: 100%;
            border-radius: 5px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-text {
            color: #6c757d;
            font-size: 13px;
        }
        .alert {
            font-size: 14px;
        }
        @media (max-width: 768px) {
            .form-container {
                padding: 10px;
            }
            .form-container h2 {
                font-size: 20px;
            }
        }
    </style>
</head>
<body>

<div class="container form-container">
    <h2>Add Course</h2>
    <form method="POST" action="add_course.php" enctype="multipart/form-data">
        <div class="form-group">
            <label for="category">Course Category:</label>
            <select class="form-control" id="category" name="category" required>
                <option value="">-- Select Category --</option>
                <option value="Accounting">Accounting</option>
                <option value="Electrical Engineering">Electrical Engineering</option>
                <option value="Programming">Programming</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="title">Course Title:</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>

        <div class="form-group">
            <label for="description">Course Description:</label>
            <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="course_note">Course Note:</label>
            <textarea class="form-control" id="course_note" name="course_note" rows="3" required></textarea>
        </div>

        <div class="form-group">
            <label for="video_link">Course Video URL:</label>
            <input type="url" class="form-control" id="video_link" name="video_link" required>
        </div>

        <div class="form-group">
            <label for="tutor_name">Tutor Name:</label>
            <input type="text" class="form-control" id="tutor_name" name="tutor_name" required>
        </div>

        <div class="form-group">
            <label for="tutor_image">Tutor Image (JPEG/PNG):</label>
            <input type="file" class="form-control-file" id="tutor_image" name="tutor_image" accept="image/jpeg, image/png">
        </div>

        <div class="form-group">
            <label for="thumb_image">Thumbnail Image (JPEG/PNG):</label>
            <input type="file" class="form-control-file" id="thumb_image" name="thumb_image" accept="image/jpeg, image/png">
        </div>

        <div class="form-group">
            <label for="course_files">Additional Files (PDF/Word/Excel):</label>
            <input type="file" class="form-control-file" id="course_files" name="course_files[]" multiple accept=".pdf, .doc, .docx, .xls, .xlsx">
        </div>

        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>
