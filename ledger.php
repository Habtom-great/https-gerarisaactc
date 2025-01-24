<?php
require_once 'db.php';
require_once 'header_loggedin.php';

// Database connection
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$include_file = "";
$path ="";
if (isset($_GET['route'])) {
$path = $_GET['route'];
} else {
	if (isset($_POST['route'])) {
	$path = $_POST['route'];
	}
}
if($path <> "") { // Checks if file really exists before including it
	$include_file = "./".$path.".php";
	if(!file_exists($include_file)) {
		$include_file = "includes/page-parts/content-404.php";
	}
		
}


