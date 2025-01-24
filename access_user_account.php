
<?php
// Include the database connection file
require_once 'db.php';
require_once 'header_loggedin.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch users for the dropdown menu
$users_sql = "SELECT * FROM students"; // Assuming 'students' is the correct table
$users_stmt = $pdo->prepare($users_sql);
$users_stmt->execute();
$users = $users_stmt->fetchAll();

// Initialize $courses and $payment_status to avoid undefined variable errors
$courses = [];
$payment_details = [];

// Check if the user has selected a user to view details
if (isset($_POST['access_user_id'])) {
    $user_id = $_POST['access_user_id'];

    // Fetch the selected user's details from students table
    $user_sql = "SELECT * FROM students WHERE id = :id";
    $user_stmt = $pdo->prepare($user_sql);
    $user_stmt->bindValue(':id', $user_id, PDO::PARAM_INT);
    $user_stmt->execute();
    $user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch the courses the user has enrolled in
    $courses_sql = "SELECT c.course_title
                    FROM course_access ca
                    JOIN courses c ON id = id
                    WHERE id = :user_id";
    $courses_stmt = $pdo->prepare($courses_sql);
    $courses_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $courses_stmt->execute();
    $courses = $courses_stmt->fetchAll();

    // Fetch payment details for the user (linked to payment_status table)
    $payment_sql = "SELECT ps.payment_status, ps.amount_paid, ps.outstanding_balance, ps.payment_date
                    FROM payment_status ps
                    WHERE ps.student_id = :user_id";
    $payment_stmt = $pdo->prepare($payment_sql);
    $payment_stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $payment_stmt->execute();
    $payment_details = $payment_stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - User Financial Information</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2>Admin Dashboard - User Financial Information</h2>

    <!-- Access User Account Form -->
    <div class="card mt-4">
        <div class="card-body">
            <h4>Access User Account</h4>
            <form method="post" action="access_user_account.php">
                <div class="form-group">
                    <label for="access_user_id">Select User to Access</label>
                    <select class="form-control" id="access_user_id" name="access_user_id" required>
                        <option value="">-- Select User --</option>
                        <?php foreach ($users as $user): ?>
                            <option value="<?= htmlspecialchars($user['id']) ?>">
                                <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?> (ID: <?= htmlspecialchars($user['id']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Access User Account</button>
            </form>
        </div>
    </div>

    <?php if (isset($user)): ?>
    <div class="card mt-4">
        <div class="card-body">
            <h4>User Information</h4>
            <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
            <p><strong>Phone:</strong> <?= htmlspecialchars($user['telephone']) ?></p>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h4>Courses Taken</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Course Title</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <tr>
                                <td><?= htmlspecialchars($course['course_title']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="1">No courses found for this user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h4>Payment Information</h4>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Payment Status</th>
                        <th>Amount Paid</th>
                        <th>Outstanding Balance</th>
                        <th>Payment Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($payment_details)): ?>
                        <?php foreach ($payment_details as $payment): ?>
                            <tr>
                                <td><?= htmlspecialchars($payment['payment_status']) ?></td>
                                <td><?= htmlspecialchars($payment['amount_paid']) ?></td>
                                <td><?= htmlspecialchars($payment['outstanding_balance']) ?></td>
                                <td><?= htmlspecialchars($payment['payment_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No payment information found for this user.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php endif; ?>

</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('footer.php'); ?>
</body>
</html>
