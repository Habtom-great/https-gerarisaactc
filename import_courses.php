<?php
include('header.php');
include('db.php');


if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $fileName = $_FILES["csv_file"]["tmp_name"];

    if ($_FILES["csv_file"]["size"] > 0) {
        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $courseId = $column[0];
            $courseName = $column[1];
            $courseDescription = $column[2];
            $courseNote = $column[3];
            $courseVideo = $column[4];

            $sqlInsert = "INSERT INTO courses (course_id, course_name, course_description, course_note, course_video) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bind_param("sssss", $courseId, $courseName, $courseDescription, $courseNote, $courseVideo);
            $stmt->execute();
        }
        fclose($file);

        echo "Courses imported successfully.";
    } else {
        echo "No file selected or file is empty.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Courses</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Import Courses</h2>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">Upload CSV file</label>
                <input type="file" class="form-control" id="csv_file" name="csv_file" required>
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>
    </div>
</body>
</html>

<?php include('footer.php'); ?>
