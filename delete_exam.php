<?php
include('header_loggedin.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['exam_id'])) {
    $exam_id = $_POST['exam_id'];

    try {
        // Delete exam and associated questions
        $stmt = $pdo->prepare("DELETE FROM exams WHERE exam_id = :exam_id");
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();

        header("Location: view_exams.php");
        exit();
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "No exam ID provided.";
}
?>

kkkkk
<?php
include('header_loggedin.php');

if (isset($_GET['exam_id'])) {
    $exam_id = $_GET['exam_id'];

    try {
        // Delete exam and associated questions
        $stmt = $pdo->prepare("DELETE FROM exams WHERE exam_id = :exam_id");
        $stmt->bindParam(':exam_id', $exam_id);
        $stmt->execute();

        header("Location: view_exams.php");
        exit();
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    echo "No exam ID provided.";
}
?>
