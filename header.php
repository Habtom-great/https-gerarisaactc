
<?php

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerar Isaac College</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS -->
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">Gerar Isaac Training Center</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="courses.php">Courses</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="exam_questions.php">Exams</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            </ul>

            <!-- Check if user is logged in and display user info -->
            <?php if (isset($_SESSION['user_id']) && isset($_SESSION['full_name']) && isset($_SESSION['user_image'])): ?>
                <span class="navbar-text user-info">
                    <img src="<?= htmlspecialchars($_SESSION['user_image']); ?>" alt="User Image" class="user-image">
                    <?= htmlspecialchars($_SESSION['full_name']); ?> (ID: <?= htmlspecialchars($_SESSION['user_id']); ?>)
                </span>
            <?php endif; ?>
        </div>
    </div>
</nav>

<style>
.user-info {
    display: flex;
    align-items: center;
    color: #00d1b2;
    margin-left: auto;
}

.user-info .user-image {
    border-radius: 50%;
    width: 40px;
    height: 40px;
    margin-right: 10px;
}
</style>
</body>
</html>
