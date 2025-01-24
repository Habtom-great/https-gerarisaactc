<?php
include('db.php');
include('header.php');

if (!isset($_SESSION['user_name'])) {
    $_SESSION['user_name'] = "Default Name"; // Temporary fix; ensure this is set correctly during login
}

// Debugging: Check if PDO connection is set
if (!$pdo) {
    die('Database connection failed.');
}

// Fetch video details from the database
$video_id = 103; // video ID is 101 for this example, adjust as needed
try {
    $stmt = $pdo->prepare("SELECT * FROM videos WHERE id = ?");
    $stmt->execute([$video_id]);
    $video = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$video) {
        die('Video not found for ID: ' . htmlspecialchars($video_id));
    }
    
    // Fetch comments
    $comments = []; // Initialize as an empty array
    $stmt = $pdo->prepare("SELECT * FROM comments WHERE video_id = ?");
    $stmt->execute([$video_id]);
    $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die('Error: ' . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <!-- Your existing head content -->
</head>
<body>
<!-- Your existing body content -->

<section class="comments">
   <h1 class="heading"><?php echo count($comments); ?> comments</h1>

   <div class="show-comments">
      <?php if (isset($comments) && is_array($comments) && !empty($comments)): ?>
        <?php foreach ($comments as $comment): ?>
            <div class="box">
                <div class="user">
                    <img src="assets/images_courses/Accounting Image.png" alt="">
                    <div>
                        <h3><?php echo htmlspecialchars($comment['user_name']); ?></h3>
                        <span><?php echo htmlspecialchars($comment['date']); ?></span>
                    </div>
                </div>
                <p><?php echo htmlspecialchars($comment['content']); ?></p>
            </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No comments available.</p>
      <?php endif; ?>
   </div>
</section>

<!-- Your existing scripts and footer -->
</body>
</html>



