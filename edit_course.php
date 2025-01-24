<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login_register.php");
    exit;
}

include('db.php');

if (isset($_GET['id'])) {
    $course_id = $_GET['id'];

    // Fetch course details
    $sql = "SELECT * FROM courses WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
} else {
    header("Location: admin_dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_name = $_POST['course_name'];
    $description = $_POST['description'];
    $tutor_id = $_POST['tutor_id'];

    $sql = "UPDATE courses SET course_name = ?, description = ?, tutor_id = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssii", $course_name, $description, $tutor_id, $course_id);
    if ($stmt->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    } else {
        die('Execute failed: ' . htmlspecialchars($stmt->error));
    }
}

include('header.php');
?>
<h2>Edit Course</h2>
<form method="POST" action="">
    <div class="form-group">
        <label for="course_name">Course Name</label>
        <input type="text" class="form-control" id="course_name" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Description</label>
        <textarea class="form-control" id="description" name="description" required><?php echo htmlspecialchars($course['description']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="tutor_id">Tutor</label>
        <select class="form-control" id="tutor_id" name="tutor_id" required>
            <?php
            $tutors_sql = "SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM users WHERE role = 'tutor'";
            $tutors_result = $conn->query($tutors_sql);
            while ($tutor = $tutors_result->fetch_assoc()) {
                $selected = $tutor['id'] == $course['tutor_id'] ? 'selected' : '';
                echo "<option value=\"{$tutor['id']}\" $selected>{$tutor['name']}</option>";
            }
            ?>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">Update Course</button>
</form>
<?php
include('footer.php');
?>
