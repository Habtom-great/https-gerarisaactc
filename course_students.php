<?php

if (!isset($_SESSION['user_id']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}
include('db.php');
$course_id = $_GET['course_id'];

// Fetch students enrolled in the course
$sql = "SELECT users.full_name 
        FROM course_enrollments 
        JOIN users ON course_enrollments.student_id = users.id 
        WHERE course_enrollments.course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

include('header.php');
?>
<h2>Students Enrolled in Course</h2>
<ul class="list-group">
    <?php while ($row = $result->fetch_assoc()): ?>
        <li class="list-group-item"><?php echo htmlspecialchars($row['full_name']); ?></li>
    <?php endwhile; ?>
</ul>
<?php include('templates/footer.php'); ?>
