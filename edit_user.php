<?php
include('db.php');
include('header_loggedin.php'); // Include header

// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// CSRF token generation and validation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$success_message = '';
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
    $target_dir = "uploads/";
    $uploadOk = 1;

    if (!empty($_FILES["profile_image"]["name"])) {
        $target_file = $target_dir . basename($_FILES["profile_image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $check = getimagesize($_FILES["profile_image"]["tmp_name"]);
        if ($check === false) {
            $error_message = "File is not a valid image.";
            $uploadOk = 0;
        }
        if ($_FILES["profile_image"]["size"] > 500000) {
            $error_message = "File size is too large.";
            $uploadOk = 0;
        }
        if (!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            $error_message = "Only JPG, JPEG, PNG & GIF formats are allowed.";
            $uploadOk = 0;
        }
        if ($uploadOk && !move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
            $error_message = "Failed to upload the image.";
        }
    }

    if ($uploadOk) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, email = ?, telephone = ?, role = ?, profile_image = ?, address = ? WHERE id = ?");
            $stmt->execute([
                $_POST['first_name'],
                $_POST['middle_name'],
                $_POST['last_name'],
                $_POST['email'],
                $_POST['telephone'],
                $_POST['role'],
                $target_file ?? $_POST['current_image'],
                $_POST['address'],
                $_GET['id']
            ]);
            $success_message = "User details updated successfully!";
        } catch (PDOException $e) {
            $error_message = "Database error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    header("Location: list_users.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .form-container {
            max-width: 600px;
            margin: auto;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        .form-group img {
            max-width: 80px;
            height: auto;
            border-radius: 5px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <div class="form-container">
        <h2 class="text-center mb-4">Edit User</h2>

        <?php if ($success_message): ?>
            <div class="alert alert-success"> <?= htmlspecialchars($success_message) ?> </div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger"> <?= htmlspecialchars($error_message) ?> </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" class="form-control" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" id="middle_name" name="middle_name" class="form-control" value="<?= htmlspecialchars($user['middle_name']) ?>">
                </div>
                <div class="form-group col-md-4">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" class="form-control" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                <div class="form-group col-md-6">
                    <label for="telephone">Phone</label>
                    <input type="text" id="telephone" name="telephone" class="form-control" value="<?= htmlspecialchars($user['telephone']) ?>" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="role">Role</label>
                    <select id="role" name="role" class="form-control">
                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                        <option value="staff" <?= $user['role'] === 'staff' ? 'selected' : '' ?>>Staff</option>
                        <option value="tutor" <?= $user['role'] === 'tutor' ? 'selected' : '' ?>>Tutor</option>
                        <option value="students" <?= $user['role'] === 'students' ? 'selected' : '' ?>>Students</option>
                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>User</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image" class="form-control">
                    <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF. Max size: 500KB.</small>
                    <?php if (!empty($user['profile_image'])): ?>
                        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile Image" class="mt-2">
                        <input type="hidden" name="current_image" value="<?= htmlspecialchars($user['profile_image']) ?>">
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="address">Address</label>
                <textarea id="address" name="address" class="form-control" rows="2"> <?= htmlspecialchars($user['address']) ?> </textarea>
            </div>
            </div>
            <div class="form-group text-center">
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </div>
          
           
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
