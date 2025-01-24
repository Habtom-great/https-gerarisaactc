<?php
// Include database connection
include('db.php');

// Function to generate a unique ID
function unique_id($prefix = '') {
    return $prefix . uniqid() . bin2hex(random_bytes(4));
}

// Database connection details
$host = 'localhost';
$dbname = 'accounting_course'; 
$username = 'root'; 
$password = ''; 
$charset = 'utf8mb4';

// Create a new PDO instance
$dsn = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Handle form submission for course notes import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['import_course_notes'])) {
    // Retrieve form data
    $id = $_POST['id'];
    $module_title = $_POST['module_title'];
    $course_notes = $_POST['course_notes'];
    $module_name = $_POST['module_name'];
    $course_video = $_POST['course_video'];
    $week_number = $_POST['week_number'];
    $chapter_number = $_POST['chapter_number'];

    // Prepare SQL statement
    $stmt = $pdo->prepare("INSERT INTO course_notes (id, module_title, course_notes, module_name, course_video, week_number, chapter_number) VALUES (?, ?, ?, ?, ?, ?, ?)");

    try {
        // Execute the SQL statement
        $stmt->execute([$id, $module_title, $course_notes, $module_name, $course_video, $week_number, $chapter_number]);

        // Redirect back with a success message
        header("Location: data_form.php?status=success&message=" . urlencode("Course data imported successfully."));
        exit();
    } catch (PDOException $e) {
        // Redirect back with an error message
        header("Location: data_form.php?status=error&message=" . urlencode("Error importing data: " . $e->getMessage()));
        exit();
    }
}

include('header.php');

// Display messages
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $message = $_GET['message'];

    $message_class = $status === 'success' ? 'success' : 'error';
    echo '
    <div class="message ' . $message_class . '">
        <span>' . htmlspecialchars($message) . '</span>
        <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
    </div>
    ';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Data Form</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: #007bff;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        input, textarea {
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 0.5rem;
            margin-bottom: 1rem;
            background-color: #f9f9f9;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            padding: 0.75rem;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            margin-top: 1rem;
            padding: 1rem;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Enter Course Data</h1>
    <form action="process_form.php" method="post">
        <label for="id">ID:</label>
        <input type="number" id="id" name="id" required>

        <label for="module_title">Module Title:</label>
        <input type="text" id="module_title" name="module_title" required>

        <label for="course_notes">Course Notes:</label>
        <textarea id="course_notes" name="course_notes" rows="4" required></textarea>

        <label for="module_name">Module Name:</label>
        <input type="text" id="module_name" name="module_name" required>

        <label for="course_video">Course Video :</label>
        <input type="url" id="course_video" name="course_video" required>

        <label for="week_number">Week Number:</label>
        <input type="number" id="week_number" name="week_number" required>

        <label for="chapter_number">Chapter Number:</label>
        <input type="number" id="chapter_number" name="chapter_number" required>

        <input type="submit" name="import_course_notes" value="Submit">
    </form>
</div>

</body>
</html>
