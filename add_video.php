<?php
include('db.php');
include('header.php');


// Check if the user is logged in as an admin
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $video_url = $_POST['course_video'];
    $notes = $_POST['course_notes'];
    $date = date('Y-m-d');

    try {
        $stmt = $pdo->prepare("INSERT INTO videos (title, course_video, course_notes, date) VALUES (?, ?, ?, ?)");
        $stmt->execute([$title, $video_url, $notes, $date]);
        $success_message = "Video added successfully!";
    } catch (PDOException $e) {
        $error_message = "Error: " . htmlspecialchars($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Video</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">
    <h2>Add New Video</h2>

    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form action="add_video.php" method="POST">
        <label for="title">Title:</label>
        <input type="text" id="title" name="title" required><br>

        <label for="video_url">Video URL:</label>
        <input type="text" id="video_url" name="video_url" required><br>

        <label for="notes">Notes:</label>
        <textarea id="notes" name="notes"></textarea><br>

        <button type="submit">Add Video</button>
    </form>
</div>

</body>
</html>
