<?php
include 'db.php'; // Ensure this path is correct

// Define your variables, for example:
$username = 'exampleUser'; // You might get this from a form input

// Example query using PDO
try {
    $sql = "SELECT * FROM users WHERE email = :email";
    $stmt = $conn->prepare($sql);

    // Bind the parameter
    $stmt->bindValue(':email', $username, PDO::PARAM_STR);
    $stmt->execute();

    // Fetch the result
    $result = $stmt->fetch();

    // Check if user exists
    if ($result) {
        echo "User found";
    } else {
        echo "User not found";
    }
} catch (PDOException $e) {
    // Handle any errors
    die('Query failed: ' . $e->getMessage());
}
?>
