<?php

// Inialize session
session_start();
 header('Location: appslogin.php');

DEFINE('DB_USERNAME', 'root');
 DEFINE('DB_PASSWORD', '');
 DEFINE('DB_HOST', 'localhost');
 DEFINE('DB_DATABASE', 'socialfootprint');

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("couldn't connect to database server \n" . $conn->connect_error);
} 
echo "'Connected successfully to the MySQL db!'";
$_SESSION['username'] = $_POST['username'];

try {
    $query = "INSERT INTO user (name) VALUES ('".$_POST['username']."')";
	$result = $conn->query($query) ;

} catch (Exception $e) {
    
}
?>