<?php
// Include common header
include('header_common.php');

// Initialize variables
$success_message = $error_message = '';
$name = $telephone = $email = $message = ''; // Initialize all variables

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Database connection using PDO
    $servername = 'localhost';
    $username = 'root'; // Replace with your MySQL username
    $password = ""; // Replace with your MySQL password
    $dbname = 'accounting_course'; // Replace with your database name

    try {
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        // Set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Get form data and trim input
        $name = trim($_POST['name']);
        $telephone = trim($_POST['telephone'] ?? ''); // Use null coalescing to avoid undefined index
        $email = trim($_POST['email']);
        $message = trim($_POST['message']);

        // Form validation
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Please enter a valid email address.";
        } elseif (empty($name) || empty($telephone) || empty($message)) {
            $error_message = "Name, telephone, and message fields are required.";
        } else {
            // Prepare the SQL statement to insert the data into the 'comments' table
            $stmt = $pdo->prepare("INSERT INTO contact (name, telephone, email, message) VALUES (:name, :telephone, :email, :message)");
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':telephone', $telephone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':message', $message);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Your message has been sent successfully!";
                // Reset form fields
                $name = $telephone = $email = $message = '';
            } else {
                $error_message = "There was an error sending your message. Please try again.";
            }
        }
    } catch (PDOException $e) {
        $error_message = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            max-width: 580px;
            margin-top: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Contact Us</h2>
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo $success_message; ?></div>
        <?php elseif ($error_message): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" required value="<?php echo htmlspecialchars($name); ?>">
            </div>
            <div class="form-group">
                <label for="telephone">Telephone</label>
                <input type="text" class="form-control" id="telephone" name="telephone" required value="<?php echo htmlspecialchars($telephone); ?>">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="form-group">
                <label for="message">Message</label>
                <textarea class="form-control" id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary btn-block">Send Message</button>
        </form>
    </div>
</body>
</html>

<?php
// Include footer
include('footer.php');
?>
