<?php

// Inialize session
session_start();
 header('Location: appslogin.php');

$_SESSION['username'] = $_POST['username'];

?>