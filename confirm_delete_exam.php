<?php
include('header_loggedin.php');

if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id'];

    // Fetch the exam details (optional, for displaying confirmation details)
    try {
        $stmt = $pdo->prepare("SELECT * FROM exams WHERE exam_id = :exam_id");
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();
        $exam = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$exam) {
            echo 'Exam not found.';
            exit();
        }
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "No exam ID provided.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Delete Exam</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>Confirm Deletion</h2>
    <p>Are you sure you want to permanently delete the exam "<strong><?= htmlspecialchars($exam['course_name']) ?></strong>"?</p>
    <form action="delete_exam.php" method="POST">
        <input type="hidden" name="exam_id" value="<?= htmlspecialchars($exam_id) ?>">
        <button type="submit" class="btn btn-danger">Yes, delete permanently</button>
        <a href="view_exams.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>
