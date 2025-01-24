
<?php
// Start session if not already started

include('header.php');
include('db.php'); // Include your database connection script

// Include Composer's autoload file
require 'vendor/autoload.php';

// Use PhpSpreadsheet classes


if (isset($_POST['submit'])) {
    $file = $_FILES['file']['tmp_name'];

    // Load the spreadsheet

        // Now you can insert $data into your database
        // Example:
        // $sql = "INSERT INTO students (name, email) VALUES (:name, :email)";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute(['name' => $data[0], 'email' => $data[1]]);
    }

    echo "Students imported successfully!";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students</title>
</head>
<body>
   

kkkk
<?php
// Start session if not already started

include('header.php');
include('db.php'); // Include your database connection script

// Include Composer's autoload file
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;

// Your file upload and import logic here
if (isset($_POST['submit'])) 
    $file = $_FILES['file']['tmp_name'];

    // Load the spreadsheet
  

 {

        $data = [];
      

        // Now you can insert $data into your database
        // Example:
        // $sql = "INSERT INTO students (name, email) VALUES (:name, :email)";
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute(['name' => $data[0], 'email' => $data[1]]);
    

    echo "Students imported successfully!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students</title>
</head>
<body>
    <h1>Import Students</h1>
    <form action="import_students.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" required>
        <button type="submit" name="submit">Import</button>
    </form>
</body>
</html>

kkk
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students - Gerar Isaac College Online Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<?php

include('header.php');

// Include your PDO connection
include('db.php');

// Include PhpSpreadsheet autoload file
require 'vendor/autoload.php';

// Rest of your import_students.php code here
?>


    <div class="container mt-5">
        <h2 class="text-center mb-4">Import Students</h2>
        <form action="import_students.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload Excel File</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>
            <button type="submit" name="import" class="btn btn-primary">Import</button>
        </form>
    </div>
</body>
</html>

kkkk
<?php

include('header.php');

// Include your PDO connection
include('db.php');

// Include PhpSpreadsheet autoload file
require 'vendor/autoload.php';

if (isset($_POST['import'])) {
    $file_mimes = array('application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    
    if (isset($_FILES['file']['name']) && in_array($_FILES['file']['type'], $file_mimes)) {
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);

        if ('csv' == $extension) {
            $reader = IOFactory::createReader('Csv');
        } else {
            $reader = IOFactory::createReader('Xlsx');
        }

        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();

        foreach ($sheetData as $row) {
            // Assuming the order matches: first_name, middle_name, last_name, email, telephone, pic
            $first_name = isset($row[0]) ? $row[0] : '';
            $middle_name = isset($row[1]) ? $row[1] : '';
            $last_name = isset($row[2]) ? $row[2] : '';
            $email = isset($row[3]) ? $row[3] : '';
            $telephone = isset($row[4]) ? $row[4] : '';
            $pic = isset($row[5]) ? $row[5] : ''; // Assuming you handle the image path correctly

            // Insert student data into the database
            $sql = "INSERT INTO students (first_name, middle_name, last_name, email, telephone, pic) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$first_name, $middle_name, $last_name, $email, $telephone, $pic]);
        }
        echo "Data imported successfully!";
    } else {
        echo "Please upload a valid Excel file.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Students - Gerar Isaac College Online Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Import Students</h2>
        <form action="import_students.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="file">Upload Excel File</label>
                <input type="file" name="file" id="file" class="form-control" required>
            </div>
            <button type="submit" name="import" class="btn btn-primary">Import</button>
        </form>
    </div>
</body>
</html>

<?php include('footer.php'); ?>

kkkk
<?php
include('header.php');
include('db.php');


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["csv_file"])) {
    $fileName = $_FILES["csv_file"]["tmp_name"];

    if ($_FILES["csv_file"]["size"] > 0) {
        $file = fopen($fileName, "r");

        while (($column = fgetcsv($file, 10000, ",")) !== FALSE) {
            $studentId = $column[0];
            $studentFullName = $column[1];
            $studentEmail = $column[2];
            $studentTelephone = $column[3];
            $studentPhoto = $column[4];

            $sqlInsert = "INSERT INTO students (student_id, student_name, student_email, student_telephone, student_photo) 
                          VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sqlInsert);
            $stmt->bind_param("sssss", $studentId, $studentFullName, $studentEmail, $studentTelephone, $studentPhoto);
            $stmt->execute();
        }
        fclose($file);

        echo "Students imported successfully.";
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
    <title>Import Students</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h2>Import Students</h2>
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
