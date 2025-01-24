
<?php

include('db.php');

    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        // Redirect to login page if not logged in
        header("Location: login.php");
        exit();
    }
    
    // Fetch user details if logged in
    $user_id = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    $full_name = $user['first_name'] . ' ' . $user['last_name'];
    
    // Set the image path
    $user_image = 'path/to/default/image.jpg'; // Set your default image path here
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
    
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM students WHERE email = ?");
        $stmt->execute([$email]);
        $email_exists = $stmt->fetchColumn();
    
        if ($email_exists) {
            echo "Error: This email is already registered.";
        } else {
            // Prepare the SQL statement
            $stmt = $pdo->prepare("INSERT INTO students (first_name, last_name, email) VALUES (?, ?, ?)");
            
            // Execute the statement with the provided user data
            if ($stmt->execute([$first_name, $last_name, $email])) {
                echo "Student added successfully!";
                // Redirect to another page if necessary
            } else {
                echo "Error: Could not add student.";
            }
        }
    }
    ?>
    
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add Student - Gerar Isaac College</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="style.css"> <!-- Link to your custom CSS -->
    </head>
    
    <body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
      <div class="container">
        <a class="navbar-brand" href="index.php">Gerar Isaac College</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item">
              <a class="nav-link" href="home.php">Home</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="about.php">About Us</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="courses.php">Courses</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="contact.php">Contact Us</a>
            </li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li class="nav-item">
                    <a class="nav-link" href="profile.php">Profile</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="dashboard.php">Dashboard</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="register.php">Register</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
            <?php endif; ?>
          </ul>
          <span class="navbar-text user-info">
            <img src="<?= htmlspecialchars($user_image) ?>" alt="User Image" class="user-image">
            <?= htmlspecialchars($full_name) ?> (ID: <?= htmlspecialchars($user_id) ?>)
          </span>
        </div>
      </div>
    </nav>
    
    <div class="container mt-5">
        <h2>Add Student</h2>
        <form action="add_student.php" method="POST">
            <div class="form-group">
                <label for="first_name">First Name</label>
                <input type="text" class="form-control" id="first_name" name="first_name" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name</label>
                <input type="text" class="form-control" id="last_name" name="last_name" required>
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </form>
    </div>
    </body>
    </html>
    