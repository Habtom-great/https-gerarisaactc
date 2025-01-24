
<?php
// Database connection setup
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "accounting_course";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch aggregated data for summary
$summary_sql = "SELECT q1, q2, q3, q4, q5, COUNT(*) as count FROM survey_responses GROUP BY q1, q2, q3, q4, q5";
$summary_result = $conn->query($summary_sql);

// Fetch detailed feedback using the correct column names (question1, question2, etc.)
$detailed_feedback_sql = "SELECT q1, q2, q3, q4, q5, email, submitted_at FROM survey_responses";
$detailed_feedback_result = $conn->query($detailed_feedback_sql);

// Check if the detailed feedback query executed successfully
if (!$detailed_feedback_result) {
    die("Error executing detailed feedback query: " . $conn->error);
}

// Calculate total respondents and average ratings (Example placeholders for now)
$total_respondents_sql = "SELECT COUNT(*) as total FROM survey_responses";
$total_respondents = $conn->query($total_respondents_sql)->fetch_assoc()['total'];

$average_rating = 4.5; // Example placeholder
$positive_feedback_percentage = 90; // Example placeholder

// Report generation date
$date_generated = date("F j, Y");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f7fa;
        }
        .container {
            max-width: 1200px;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        header {
            background-color: #4caf50;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        table {
            margin-top: 20px;
        }
        footer {
            background-color: #4caf50;
            color: white;
            text-align: center;
            padding: 10px;
            margin-top: 20px;
            border-radius: 0 0 8px 8px;
        }
        .feedback-details {
            display: none; /* Hide by default */
        }
    </style>
    <script>
        function toggleFeedbackDetails(id) {
            var feedbackDetails = document.getElementById(id);
            if (feedbackDetails.style.display === "none") {
                feedbackDetails.style.display = "block";
            } else {
                feedbackDetails.style.display = "none";
            }
        }
    </script>
</head>
<body>
    <header>
        <h1>Feedback Report</h1>
    </header>
    <div class="container">
        <!-- Overview Section -->
        <div class="row text-center mb-4">
            <div class="col-md-4">
                <h4>Total Respondents</h4>
                <p><strong><?php echo $total_respondents; ?></strong></p>
            </div>
            <div class="col-md-4">
                <h4>Average Rating</h4>
                <p><strong><?php echo number_format($average_rating, 1); ?> / 5</strong></p>
            </div>
            <div class="col-md-4">
                <h4>Positive Feedback</h4>
                <p><strong><?php echo $positive_feedback_percentage; ?>%</strong></p>
            </div>
        </div>

        <!-- Aggregated Data Section -->
        <h3>Aggregated Feedback Summary</h3>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Question 1</th>
                    <th>Question 2</th>
                    <th>Question 3</th>
                    <th>Question 4</th>
                    <th>Question 5</th>
                    <th>Count</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($summary_result->num_rows > 0) {
                    while ($row = $summary_result->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['q1']}</td>
                                <td>{$row['q2']}</td>
                                <td>{$row['q3']}</td>
                                <td>{$row['q4']}</td>
                                <td>{$row['q5']}</td>
                                <td>{$row['count']}</td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No data available</td></tr>";
                }
                ?>
            </tbody>
        </table>

        <!-- Detailed Feedback Section -->
        <h3>Detailed Feedback</h3>
        <?php
        if ($detailed_feedback_result->num_rows > 0) {
            while ($row = $detailed_feedback_result->fetch_assoc()) {
                $feedback_id = "feedback" . $row['submitted_at']; // Unique ID for each feedback section
                echo "<div>
                        <p><strong>Email:</strong> {$row['email']}</p>
                        <button onclick='toggleFeedbackDetails(\"$feedback_id\")' class='btn btn-primary'>View/Hide Feedback</button>
                        <div id='$feedback_id' class='feedback-details'>
                            <p><strong>Feedback for Question 1:</strong> {$row['q1']}</p>
                            <p><strong>Feedback for Question 2:</strong> {$row['q2']}</p>
                            <p><strong>Feedback for Question 3:</strong> {$row['q3']}</p>
                            <p><strong>Feedback for Question 4:</strong> {$row['q4']}</p>
                            <p><strong>Feedback for Question 5:</strong> {$row['q5']}</p>

                            <p><small>Submitted on: {$row['submitted_at']}</small></p>
                        </div>
                      </div><hr>";
            }
        } else {
            echo "<p>No detailed feedback available.</p>";
        }
        ?>
    </div>
    <footer>
        <p>Report generated on: <strong><?php echo $date_generated; ?></strong></p>
    </footer>
</body>
</html>

<?php
$conn->close();
?>
