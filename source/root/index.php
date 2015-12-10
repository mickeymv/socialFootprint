<?php
session_start();

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