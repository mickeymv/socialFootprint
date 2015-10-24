<?php

// Inialize session
session_start();
DEFINE('DB_USERNAME', 'root');
DEFINE('DB_PASSWORD', '');
DEFINE('DB_HOST', 'localhost');
DEFINE('DB_DATABASE', 'socialfootprint');

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("couldn't connect to database server \n" . $conn->connect_error);
} 
echo "'Connected successfully to the MySQL db!’";

$sql = "CREATE TABLE user (   
fbId INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT UNIQUE,   
name VARCHAR( 255 ) NOT NULL,
CONSTRAINT pk_idName PRIMARY KEY (name)
)";

if ($conn->query($sql) === TRUE) {
    echo "<br>Table User created successfully";
} else {
    echo "<br>". $conn->error;
}

$sql = "CREATE TABLE facebookUser (   
fbId BIGINT( 20 ) UNSIGNED NOT NULL PRIMARY KEY,   
name VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL ,   
email VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci ,   
gender VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci , 
hometown VARCHAR(50),
currentLocation VARCHAR(50),
locale VARCHAR(50),
languages VARCHAR(100),
birthdate DATE,
profilePic LONGBLOB NOT NULL 
)";

if ($conn->query($sql) === TRUE) {
    echo "<br>Table facebookUser created successfully";
} else {
    echo "<br>" . $conn->error;
}

$conn->close();

// Check, if user is already login, then jump to secured page
if (isset($_SESSION['username'])) {
header('Location: appslogin.php');
}

?>
<html>

<head>
<title>Social FootPrint</title>
</head>

<body>

 <h3>User Registration</h3>

<table border="0">
<form method="POST" action="loginproc.php">
<tr><td>Username</td><td>:</td><td><input type="text" name="username" size="20">        </td></tr>
<tr><td>&nbsp;</td><td>&nbsp;</td><td><input type="submit" value="Login"></td></tr>
</form>
</table>
</body>

</html>