<?php
session_start();
include('db.php');

// Check if the admin is logged in and has the role of 'admin'
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

// Approve payment when the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['approve_payment'])) {
    $payment_id = $_POST['payment_id'];
    $user_id = $_POST['user_id'];

    // Update the payment status to approved in the database
    $sql = "UPDATE payments SET admin_approval = 'approved' WHERE payment_id = :payment_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['payment_id' => $payment_id]);

    // Grant course access to the user by updating their course access status
    $update_user_access_sql = "UPDATE user_course_access SET access_granted = 1 WHERE user_id = :user_id AND payment_id = :payment_id";
    $stmt = $pdo->prepare($update_user_access_sql);
    $stmt->execute(['user_id' => $user_id, 'payment_id' => $payment_id]);

    echo "<p>Payment and course access approved for user ID: {$user_id}</p>";
}

// Fetch pending payments that need admin approval
$sql = "SELECT p.payment_id, p.user_id, p.amount, u.full_name, c.course_title 
        FROM payments p
        JOIN users u ON p.user_id = u.id
        JOIN courses c ON p.course_id = c.course_id
        WHERE p.admin_approval = 'pending'";
$stmt = $pdo->prepare($sql);
$stmt->execute();
$pending_payments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Approve Payments</title>
</head>
<body>
    <h1>Approve Pending Payments</h1>
    <?php if ($pending_payments): ?>
        <table border="1">
            <thead>
                <tr>
                    <th>Payment ID</th>
                    <th>User Name</th>
                    <th>Course Title</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pending_payments as $payment): ?>
                    <tr>
                        <td><?= htmlspecialchars($payment['payment_id']) ?></td>
                        <td><?= htmlspecialchars($payment['full_name']) ?></td>
                        <td><?= htmlspecialchars($payment['course_title']) ?></td>
                        <td><?= htmlspecialchars($payment['amount']) ?></td>
                        <td>
                            <form method="POST" action="admin_approval.php">
                                <input type="hidden" name="payment_id" value="<?= htmlspecialchars($payment['payment_id']) ?>">
                                <input type="hidden" name="user_id" value="<?= htmlspecialchars($payment['user_id']) ?>">
                                <button type="submit" name="approve_payment">Approve Payment</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending payments for approval.</p>
    <?php endif; ?>
</body>
</html>
