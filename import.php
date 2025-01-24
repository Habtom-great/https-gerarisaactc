<?php
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

// Check if form data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        header("Location: data_form.php?status=success&message=" . urlencode("Data successfully submitted."));
        exit();
    } catch (PDOException $e) {
        // Redirect back with an error message
        header("Location: data_form.php?status=error&message=" . urlencode("Error submitting data: " . $e->getMessage()));
        exit();
    }
} else {
    // Redirect back with an error message
    header("Location: data_form.php?status=error&message=" . urlencode("Invalid request method."));
    exit();
}
