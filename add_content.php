<?php
session_start();
if(!isset($_SESSION['user_id']) || !$_SESSION['is_admin']){
    header("Location: login.php");
    exit;
}
include('db.php');
$course_id = $_GET['course_id'];
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $title = $_POST['title'];
    $content_type = $_POST['content_type'];
    $content_path = 'uploads/' . $_FILES['content_path']['name'];
    move_uploaded_file($_FILES['content_path']['tmp_name'], $content_path);

    $sql = "INSERT INTO content (course_id, title, content_type, content_path) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isss", $course_id, $title, $content_type, $content_path);
    if($stmt->execute()){
        header("Location: course.php?id=$course_id");
    } else {
        $error = "Error adding content";
    }
    $stmt->close();
}
include('header.php');
?>
<h2>Add Content to Course</h2>
<form method="post" action="add_content.php?course_id=<?php echo $course_id; ?>" enctype="multipart/form-data">
    <div class="form-group">
        <label for="title">Content Title</label>
        <input type="text" class="form-control" id="title" name="title" required>
    </div>
    <div class="form-group">
        <label for="content_type">Content Type</label>
        <select class="form-control" id="content_type" name="content_type" required>
            <option value="video">Video</option>
            <option value="picture">Picture</option>
            <option value="document">Document</option>
        </select>
    </div>
    <div class="form-group">
        <label for="content_path">Content File</label>
        <input type="file" class="form-control" id="content_path" name="content_path" required>
    </div>
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    <button type="submit" class="btn btn-primary">Add Content</button>
</form>
<?php include('footer.php'); ?>
