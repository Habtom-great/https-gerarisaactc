<?php
include('db.php');

// Check if video title is provided
if (isset($_GET['video_title'])) {
    $video_title = urldecode($_GET['video_title']);

    // Query to get video details by title
    $sql = "SELECT video_url, video_notes FROM course_videos WHERE video_title = :video_title";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':video_title', $video_title, PDO::PARAM_STR);

    if ($stmt->execute()) {
        $video = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($video) {
            echo json_encode($video);
        } else {
            echo json_encode(['error' => 'Video not found.']);
        }
    } else {
        echo json_encode(['error' => 'Failed to fetch video data.']);
    }
} else {
    echo json_encode(['error' => 'No video title provided.']);
}

