<?php
include('db.php');
session_start();
// Check if the user is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

// Pagination settings
$items_per_page = 10;
$current_page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Handle sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'id'; // Default sort by ID
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC'; // Default order is ascending

$valid_columns = ['id', 'first_name', 'last_name'];
$sort_by = in_array($sort_by, $valid_columns) ? $sort_by : 'id';
$order = ($order === 'ASC' || $order === 'DESC') ? $order : 'ASC';

try {
    // Fetch total number of users
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users");
    $stmt->execute();
    $total_items = $stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch user data for the current page with sorting
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, email, telephone, role, profile_image FROM users ORDER BY $sort_by $order LIMIT :offset, :limit");
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$users) {
        $users = []; // Ensure $users is an empty array if no data is returned
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $users = []; // Ensure $users is an empty array in case of an error
}

include('header_loggedin.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        /* Your existing styles */
      
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .dashboard-container {
            margin-top: 30px;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .title-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .title-section h1 {
            color: #007bff;
            font-size: 2.5rem;
            border-bottom: 2px solid #007bff;
            padding-bottom: 10px;
            display: inline-block;
        }
        .subtitle-section {
            text-align: center;
            margin-bottom: 20px;
        }
        .subtitle-section h2 {
            color: #343a40;
            font-size: 2rem;
            margin: 0;
        }
        .table {
            margin-top: 20px;
            border-collapse: separate;
            border-spacing: 0;
        }
        .table th, .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #dee2e6;
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
            text-transform: uppercase;
        }
        .table tr:hover {
            background-color: #f1f1f1;
        }
        .user-image {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #ddd;
        }
        .pagination {
            justify-content: center;
            margin-top: 20px;
        }
        .pagination a {
            color: #007bff;
            padding: 8px 16px;
            text-decoration: none;
            border: 1px solid #ddd;
            margin: 0 2px;
            border-radius: 4px;
        }
        .pagination a:hover {
            background-color: #e9ecef;
        }
        .pagination .active a {
            background-color: #007bff;
            color: #ffffff;
            border-color: #007bff;
        }
  
    </style>
</head>
<body>

<div class="container dashboard-container">
    <div class="title-section">
        <h1>Admin Dashboard</h1>
    </div>

    <div class="subtitle-section">
        <h2>Staff List</h2>
        <a href="add_user.php" class="btn btn-success mb-3">Add New User</a>
    </div>

    <!-- Sorting Dropdown -->
    <form method="GET" class="form-inline mb-3">
        <label for="sort_by" class="mr-2">Sort By:</label>
        <select name="sort_by" id="sort_by" class="form-control mr-2">
            <option value="id" <?= $sort_by == 'id' ? 'selected' : '' ?>>ID</option>
            <option value="first_name" <?= $sort_by == 'first_name' ? 'selected' : '' ?>>First Name</option>
            <option value="last_name" <?= $sort_by == 'last_name' ? 'selected' : '' ?>>Last Name</option>
        </select>

        <select name="order" id="order" class="form-control mr-2">
            <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
            <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending</option>
        </select>

        <button type="submit" class="btn btn-primary">Sort</button>
    </form>

    <!-- Users/Students Section -->
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Image</th>
                <th>ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="User Image" class="user-image"></td>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['first_name']) ?></td>
                    <td><?= htmlspecialchars($user['last_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['telephone']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                        <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Pagination Controls -->
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php if ($current_page > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page - 1 ?>&sort_by=<?= $sort_by ?>&order=<?= $order ?>" aria-label="Previous">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $current_page ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&sort_by=<?= $sort_by ?>&order=<?= $order ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <?php if ($current_page < $total_pages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $current_page + 1 ?>&sort_by=<?= $sort_by ?>&order=<?= $order ?>" aria-label="Next">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

</body>
</html>

<?php include('footer.php'); ?>
kkkkkkkkkk
<?php
// Include the database connection and header files
include('db.php');
include('header_common.php');

// Pagination settings
$items_per_page = 10;
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($current_page - 1) * $items_per_page;

// Handle sorting
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'last_name';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Handle search
$search_query = isset($_GET['search']) ? trim($_GET['search']) : '';

// Fetch staff data for the current page with sorting, searching, and pagination
try {
    $search_sql = $search_query ? "WHERE (id LIKE :search OR first_name LIKE :search OR last_name LIKE :search OR telephone LIKE :search)" : "";

    // Fetch total number of staff matching the search query
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM staff $search_sql");
    if ($search_query) {
        $stmt->bindValue(':search', "%$search_query%");
    }
    $stmt->execute();
    $total_items = $stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch staff data with pagination, sorting, and search query
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, middle_name, telephone, email, profile_image, education_level FROM staff $search_sql ORDER BY $sort_by $order LIMIT :offset, :limit");
    if ($search_query) {
        $stmt->bindValue(':search', "%$search_query%");
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $staff = []; // Ensure $staff is an empty array in case of an error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff List</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .dashboard-container {
            margin: 20px auto;
            padding: 20px;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table th, .table td {
            font-size: 0.85rem;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #007bff;
            color: #ffffff;
        }
        .pagination a {
            font-size: 0.85rem;
            padding: 5px 10px;
        }
        .table img {
            max-width: 40px;
            height: 40px;
            border-radius: 50%;
        }
    </style>
</head>
<body>
<div class="container dashboard-container">
    <h2 class="text-center mb-4">Staff List</h2>
    
    <a href="admin_dashboard.php" class="btn btn-secondary mb-3">Back to Admin Dashboard</a>

    <form method="GET" class="form-inline mb-3">
        <input type="text" name="search" class="form-control mr-2" placeholder="Search..." value="<?= htmlspecialchars($search_query) ?>">
        <select name="sort_by" class="form-control mr-2">
            <option value="last_name" <?= $sort_by == 'last_name' ? 'selected' : '' ?>>Last Name</option>
            <option value="first_name" <?= $sort_by == 'first_name' ? 'selected' : '' ?>>First Name</option>
            <option value="telephone" <?= $sort_by == 'telephone' ? 'selected' : '' ?>>Telephone</option>
        </select>
        <select name="order" class="form-control mr-2">
            <option value="ASC" <?= $order == 'ASC' ? 'selected' : '' ?>>Ascending</option>
            <option value="DESC" <?= $order == 'DESC' ? 'selected' : '' ?>>Descending</option>
        </select>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Order No.</th>
                <th>ID</th>
                <th>Last Name</th>
                <th>Middle Name</th>
                <th>First Name</th>
                <th>Phone</th>
                <th>Email</th>
                <th>Educational Level</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $order_no = $offset + 1;
            foreach ($staff as $staffMember): ?>
                <tr>
                    <td><?= $order_no++ ?></td>
                    <td><?= htmlspecialchars($staffMember['id']) ?></td>
                    <td><?= htmlspecialchars($staffMember['last_name']) ?></td>
                    <td><?= htmlspecialchars($staffMember['middle_name']) ?></td>
                    <td><?= htmlspecialchars($staffMember['first_name']) ?></td>
                    <td><?= htmlspecialchars($staffMember['telephone']) ?></td>
                    <td><?= htmlspecialchars($staffMember['email']) ?></td>
                    <td><?= htmlspecialchars($staffMember['education_level']) ?></td>
                    <td><img src="<?= htmlspecialchars($staffMember['profile_image']) ?>" alt="Profile"></td>
                    <td>
                        <a href="edit_staff.php?id=<?= $staffMember['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_staff.php?id=<?= $staffMember['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="pagination">
        <?php if ($current_page > 1): ?>
            <a href="?page=<?= $current_page - 1 ?>&search=<?= htmlspecialchars($search_query) ?>&sort_by=<?= $sort_by ?>&order=<?= $order ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i ?>&search=<?= htmlspecialchars($search_query) ?>&sort_by=<?= $sort_by ?>&order=<?= $order ?>" <?= $i == $current_page ? 'class="active"' : '' ?>><?= $i ?></a>
        <?php endfor; ?>

        <?php if ($current_page < $total_pages): ?>
            <a href="?page=<?= $current_page + 1 ?>&search=<?= htmlspecialchars($search_query) ?>&sort_by=<?= $sort_by ?>&order=<?= $order ?>">Next</a>
        <?php endif; ?>
    </div>
</div>
<!-- Footer -->
<?php include('footer.php'); ?>

</body>
</html>
