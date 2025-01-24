<?php
// Include database connection file
include('db.php');
include('header.php');

// Define the students array
$students = [
    ['21', 'Yohana', 'Negasi', '095278888', 'yohana@gmail.com'],
    ['06', 'Smret', 'Tomas', '095278888', 'smret@gmail.com'],
    ['05', 'John', 'Doe', '0987654321', 'john.doe@example.com'],
    // Add more student records as needed
];

try {
    // Begin transaction
    $pdo->beginTransaction();

    // Prepare the SQL statement
    $sql = "INSERT INTO students (id, first_name, last_name, telephone, email) VALUES (:id, :first_name, :last_name, :telephone, :email)";
    
    $stmt = $pdo->prepare($sql);

    // Execute the statement for each student
    foreach ($students as $student) {
        $stmt->execute([
            ':id' => $student[0],
            ':first_name' => $student[1],
            ':last_name' => $student[2],
            ':telephone' => $student[3],
            ':email' => $student[4],
        ]);
    }

    // Commit the transaction
    $pdo->commit();
    echo "Students inserted successfully.";
} catch (PDOException $e) {
    // Rollback the transaction if something failed
    $pdo->rollBack();
    echo "Failed to insert students: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Students</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1>Insert Students</h1>
        <p><?php echo htmlspecialchars($message); ?></p>
    </div>
</body>
</html>
