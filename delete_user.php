
<?php
include('header_loggedin.php');

if (isset($_GET['users_id'])) {
    $user_id = $_GET['users_id'];


}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        header("Location: list_users.php");
        exit();
    } catch (PDOException $e) {
        echo 'Error: ' . $e->getMessage();
    }
} else {
    header("Location: list_users.php");
    exit();
}
?>
