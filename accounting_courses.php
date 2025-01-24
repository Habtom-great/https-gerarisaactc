<?php include 'header.php'; ?> 
<?php include 'sidebar.php'; ?>

<section id="courses" class="container section py-5">
    <h2 class="text-center mb-5">Our Accounting Courses</h2>
    <div class="row">
        <?php
        // Database connection
        include 'db.php';  // Ensure this file sets up $pdo

        // Fetch Accounting courses from the database
        $sql = "SELECT * FROM courses"; // You can add WHERE clause if needed
        $stmt = $pdo->query($sql);
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($courses)) {
            // Loop through each course and display it
            foreach ($courses as $row) {
                echo '
                <div class="col-md-6 col-lg-3 mb-4">
                    <div class="card shadow-lg h-100 border-0">
                        <img src="assets/logos/' . htmlspecialchars($row["thumb_image"]) . '" class="card-img-top img-fluid" alt="' . htmlspecialchars($row["course_title"]) . '">
                        <div class="card-body text-center">
                            <h5 class="card-title font-weight-bold">' . htmlspecialchars($row["course_title"]) . '</h5>
                            <p class="card-text text-muted">' . htmlspecialchars($row["description"]) . '</p>
                            <a href="course_details.php?id=' . htmlspecialchars($row["course_id"]) . '" class="btn btn-primary btn-block">Learn More</a>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo '<p>No accounting courses available at the moment.</p>';
        }

        // Optionally, set $pdo to null to close the connection explicitly
        $pdo = null;
        ?>
    </div>
</section>

<?php include 'footer.php'; ?>
