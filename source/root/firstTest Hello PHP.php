<html>
 <head>
  <title>PHP Hello Test page</title>
 </head>
 <body>
  	<?php 
	echo "Hello, I'm PHP! Get my info from phpinfo() below!<br><br>";
	
	echo "The user agent is ".$_SERVER['HTTP_USER_AGENT'];
	?> 
	<?php phpinfo(); ?>
 </body>
</html>