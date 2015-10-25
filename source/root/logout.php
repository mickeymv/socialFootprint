<?php
session_start();
unset($_SESSION['username']);
unset($_SESSION['fb_access_token']);
header('Location: index.php');
?>