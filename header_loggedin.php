<?php
// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Include database connection file
include('db.php');

// Check if the user is logged in
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Fetch user details (name and profile image)
    $stmt = $pdo->prepare("SELECT first_name, last_name, profile_image FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Concatenate first and last name
        $full_name = htmlspecialchars($user['first_name']) . ' ' . htmlspecialchars($user['last_name']);
        
        // Set user profile image, or use a default image if not available
        $user_image = !empty($user['profile_image']) ? htmlspecialchars($user['profile_image']) : 'path/to/default/image.jpg';
    } else {
        // Redirect to login if user is not found
        header("Location: login.php");
        exit();
    }
} else {
    // Redirect to login if session is not set
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac College - Exam Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS -->
</head>
<body>
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container">
    <a class="navbar-brand" href="index.php">Gerar Isaac College</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ml-auto">
        <li class="nav-item">
          <a class="nav-link" href="user_dashboard.php">Courses</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="exams.php">Exams</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="profile.php">Profile</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Logout</a>
        </li>
      </ul>
      
      <!-- User Info Display -->
      <span class="navbar-text user-info">
        <img src="<?= $user_image ?>" alt="User Image" class="user-image">
        <?= $full_name ?> (ID: <?= htmlspecialchars($user_id) ?>)
      </span>
    </div>
  </div>
</nav>

<!-- Custom Styles -->
<style>

.user-info .user-image {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-right: 10px;
    object-fit: cover;
}

textarea {
    margin-bottom: 20px;
}
</style>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
