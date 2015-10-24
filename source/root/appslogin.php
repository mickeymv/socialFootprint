<?php
@ob_start();
session_start();
// Check, if username session is NOT set then this page will jump to login page
if (!isset($_SESSION['username'])) {
header('Location: index.php');
}

?>
<html>
 <head>
  <title>Apps login</title>
 </head>
 <body>
 <div>
 <p><?php echo "Current Loggedin User:".$_SESSION['username']?></p><p><a href="logout.php">Logout</a></p>
 </div>
    <div> 
	<h1>Facebook</h1>
  	<?php 
  	require_once __DIR__ . '/facebook-sdk-v5/autoload.php';
  	$fb = new Facebook\Facebook([
  'app_id' => '1646244812311215',
  'app_secret' => 'b798f23b27dd787c88b69e4972fe8869',
  'default_graph_version' => 'v2.5',
  ]);

$helper = $fb->getRedirectLoginHelper();
$permissions = ['user_birthday', 'email', 'user_likes', 'user_friends', 'public_profile', 'user_hometown', 'user_location', 'user_about_me']; // optional
$loginUrl = $helper->getLoginUrl('http://localhost/fblogin-callback.php', $permissions);

echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';

?>
</div>
</body>
</html>