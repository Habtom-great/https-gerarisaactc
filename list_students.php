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

// Fetch student data for the current page with sorting, searching, and pagination
try {
    $search_sql = $search_query ? "WHERE (id LIKE :search OR first_name LIKE :search OR last_name LIKE :search OR telephone LIKE :search)" : "";

    // Fetch total number of students matching the search query
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users $search_sql");
    if ($search_query) {
        $stmt->bindValue(':search', "%$search_query%");
    }
    $stmt->execute();
    $total_items = $stmt->fetchColumn();
    $total_pages = ceil($total_items / $items_per_page);

    // Fetch student data with pagination, sorting, and search query
    $stmt = $pdo->prepare("SELECT id, first_name, last_name, middle_name, telephone, email, profile_image, course_took, grade_result FROM student_details $search_sql ORDER BY $sort_by $order LIMIT :offset, :limit");
    if ($search_query) {
        $stmt->bindValue(':search', "%$search_query%");
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $items_per_page, PDO::PARAM_INT);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
    $students = []; // Ensure $students is an empty array in case of an error
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student List</title>
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
    <h2 class="text-center mb-4">Student List</h2>
    
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
                <th>Course Took</th>
                <th>Grade Result</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $order_no = $offset + 1;
            foreach ($students as $student): ?>
                <tr>
                    <td><?= $order_no++ ?></td>
                    <td><?= htmlspecialchars($student['id']) ?></td>
                    <td><?= htmlspecialchars($student['last_name']) ?></td>
                    <td><?= htmlspecialchars($student['middle_name']) ?></td>
                    <td><?= htmlspecialchars($student['first_name']) ?></td>
                    <td><?= htmlspecialchars($student['telephone']) ?></td>
                    <td><?= htmlspecialchars($student['email']) ?></td>
                    <td><?= htmlspecialchars($student['course_took']) ?></td>
                    <td><?= htmlspecialchars($student['grade_result']) ?></td>
                    <td><img src="<?= htmlspecialchars($student['profile_image']) ?>" alt="Profile"></td>
                    <td>
                        <a href="edit_student.php?id=<?= $student['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                        <a href="delete_student.php?id=<?= $student['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
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
</body>
</html>
