<html>
 <head>
  <title>Database Creation</title>
 </head>
 <body>
  	<?php 
 DEFINE('DB_USERNAME', 'root');
 DEFINE('DB_PASSWORD', 'root');
 DEFINE('DB_HOST', 'localhost');
 DEFINE('DB_DATABASE', 'socialfootprint');

$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
if ($conn->connect_error) {
    die("couldn't connect to database server \n" . $conn->connect_error);
} 
echo "'Connected successfully to the MySQL db!’";

$sql = "CREATE TABLE user (   
fbId INT( 20 ) UNSIGNED NOT NULL AUTO_INCREMENT,   
name VARCHAR( 255 ) NOT NULL,
CONSTRAINT pk_idName PRIMARY KEY (fbId,name)
)";

if ($conn->query($sql) === TRUE) {
    echo "<br>Table User created successfully";
} else {
    echo "<br>Error creating table User: " . $conn->error;
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
    echo "<br>Error creating table facebookUser: " . $conn->error;
}

$conn->close();
	?> 
 </body>
</html>