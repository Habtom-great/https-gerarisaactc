<?php
// Include the database connection file
require_once 'db.php';
require_once 'header_loggedin.php';

// Redirect if not logged in or not an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$admin_id = $_SESSION['user_id'];

// Handle Approve action
if (isset($_GET['approve'])) {
    $access_id = $_GET['approve'];
    $stmt = $pdo->prepare("UPDATE course_access SET access_status = 'approved' WHERE id = :id");
    $stmt->execute(['id' => $access_id]);
    echo "<script>alert('Access approved.'); window.location.href='admin_dashboard.php';</script>";
}

// Handle Deny action
if (isset($_GET['deny'])) {
    $access_id = $_GET['deny'];
    $stmt = $pdo->prepare("UPDATE course_access SET access_status = 'denied' WHERE id = :id");
    $stmt->execute(['id' => $access_id]);
    echo "<script>alert('Access denied.'); window.location.href='admin_dashboard.php';</script>";
}

// Pagination variables
$limit = 10; // Number of records per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch Course Access Requests with pagination
try {
    $sql = "SELECT a.id, a.user_id, a.course_id, a.requested_at, a.access_status, 
                   c.course_title, u.first_name, u.last_name
            FROM course_access a
            JOIN courses c ON a.course_id = c.course_id
            JOIN users u ON a.user_id = u.id
            LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $requests = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Total records count
    $totalStmt = $pdo->query("SELECT COUNT(*) FROM course_access");
    $totalRecords = $totalStmt->fetchColumn();
    $totalPages = ceil($totalRecords / $limit);
} catch (PDOException $e) {
    die("Error fetching requests: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            margin-top: 20px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th:nth-child(8), .table td:nth-child(8) {
            width: 200px; /* Increase the width of the Request Date column */
        }
        .table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .table tbody tr:hover {
            background-color: #f1f1f1;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-deny {
            background-color: #dc3545;
            color: white;
        }
        .btn-approve:hover, .btn-deny:hover {
            opacity: 0.8;
        }
        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }
        .status-denied {
            background-color: #f8d7da;
            color: #721c24;
        }
        .btn {
            font-size: 10px;
            padding: 3px 8px;
        }
        .btn-approve {
            background-color: #28a745;
            color: white;
        }
        .btn-deny {
            background-color: #dc3545;
            color: white;
        }
        .btn-approve:hover, .btn-deny:hover {
            opacity: 0.8;
        }
        .actions {
            display: flex;
            gap: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center mb-4">Course Access Requests</h1>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Request ID</th>
                        <th>User ID</th>
                        <th>Last Name</th>
                        <th>First Name</th>
                        <th>Course ID</th>
                        <th>Course Title</th>
                        <th>Request Date</th>
                        <th>Access Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($requests) {
                        $orderNo = $offset + 1;
                        foreach ($requests as $request) {
                            echo '<tr>';
                            echo '<td>' . $orderNo++ . '</td>';
                            echo '<td>' . htmlspecialchars($request['id']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['user_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['last_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['first_name']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['course_id']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['course_title']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['requested_at']) . '</td>';
                            echo '<td>' . htmlspecialchars($request['access_status']) . '</td>';
                            echo '<td>';
                            if ($request['access_status'] === 'pending') {
                                echo '<div class="actions">';
                                echo '<a href="?approve=' . htmlspecialchars($request['id']) . '" class="btn btn-approve btn-sm">Approve</a>';
                                echo '<a href="?deny=' . htmlspecialchars($request['id']) . '" class="btn btn-deny btn-sm">Deny</a>';
                                echo '</div>';
                            } else {
                                echo 'N/A';
                            }
                            echo '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="10">No requests available.</td></tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>

        <!-- Back to Admin Dashboard Button -->
        <div class="text-right mt-4">
            <a href="admin_dashboard.php" class="btn btn-secondary">Back to Admin Dashboard</a>
        </div>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php if ($page > 1): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page - 1 ?>">Previous</a></li>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <li class="page-item"><a class="page-link" href="?page=<?= $page + 1 ?>">Next</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

<?php include('footer.php'); ?>
