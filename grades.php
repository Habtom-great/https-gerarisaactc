<?php
session_start();
if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}
include('db.php');

// Fetch grades
$sql = "SELECT users.full_name, courses.title, grades.grade 
        FROM grades 
        JOIN users ON grades.student_id = users.id 
        JOIN courses ON grades.course_id = courses.id";
$result = $conn->query($sql);

include('header.php');
?>
<h2>Grades Report</h2>
<table class="table">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Course Title</th>
            <th>Grade</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                <td><?php echo htmlspecialchars($row['title']); ?></td>
                <td><?php echo htmlspecialchars($row['grade']); ?></td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php include('footer.php'); ?>
