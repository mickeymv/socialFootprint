<?php
@ob_start();
session_start();
// Check, if username session is NOT set then this page will jump to login page
if (!isset($_SESSION['username'])) {
header('Location: index.php');
}
require_once __DIR__ . '/facebook-sdk-v5/autoload.php';
$fb = new Facebook\Facebook([
  'app_id' => '1646244812311215',
  'app_secret' => 'b798f23b27dd787c88b69e4972fe8869',
  'default_graph_version' => 'v2.5',
  ]);

$helper = $fb->getRedirectLoginHelper();
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
		<h3>Facebook</h3>
<?php 
	try{
		$accessToken = $helper->getAccessToken();
		
		if (isset($accessToken)) {
			$oAuth2Client = $fb->getOAuth2Client();  
			$tokenMetadata = $oAuth2Client->debugToken($accessToken);  
			$tokenMetadata->validateExpiration();   
			if (! $accessToken->isLongLived()) {  
				try {
					$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);  
				}
				catch (Facebook\Exceptions\FacebookSDKException $e) {  
					echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>";  
				} 
				var_dump($accessToken->getValue());  
			}	
			$_SESSION['fb_access_token'] = (string) $accessToken;  
			echo 'Login successful!   ';
			$fb->setDefaultAccessToken($accessToken);

			$response = $fb->get('/me');
			$graphObject = $response->getGraphObject();
			$uid = $graphObject['id'];
			$name = $graphObject['name'];			
			
			$userNode = $fb->get('/me?fields=name,email,hometown,location,birthday,locale,picture,gender,languages')->getGraphUser();
			$gender = $userNode['gender'];
			$email = $userNode['email'];
			$hometown = $userNode['hometown'];
			$location = $userNode['location'];
			$birthdate = $userNode['birthday']->format('Y/m/d');
			$locale = $userNode['locale'];
			$fbId = $userNode['id'];
			$languageList = $userNode['languages'];
			$languages = "";
			foreach($languageList as $language)
				$languages =  $languages.", ".$language['name'] .","; 
			$profile_pic_url =  "http://graph.facebook.com/".$fbId."/picture";
			$likes = $fb->get('/me/likes')->getGraphEdge();

			echo "<img src=\"" . $profile_pic_url . "\" /><br>"; 
			echo "Name: ".$name."<br>";
			echo "Gender: " . $gender ."<br>"; 
			echo "Email Id: " . $email ."<br>"; //what is the GET for email? not email/user_email
			echo "Hometown is " . $hometown['name']."<br>"; //get for hometown?
			echo "Current location is " . $location['name']."<br>"; //get for location?
			echo "DOB" .$birthdate."<br>"; //get for dob? birthday? user_birthday?	
			echo "Locale:" . $locale."<br>"; //??
			echo "Languages: ".$languages;			
			echo "<br>Likes: <br>"; 
			echo '<table>';
			foreach($likes as $like)
			{
				echo '<tr>';
				foreach($like as $fields)
				{
					if(is_a($fields,'datetime') )
						echo '<td>'.$fields->format('Y/m/d').'</td>';
					else
						echo '<td>'.$fields.'</td>';
				}
				echo '</tr>';
			} 
			echo '<table>';

			//Save data to database
			DEFINE('DB_USERNAME', 'root');
			DEFINE('DB_PASSWORD', '');
			DEFINE('DB_HOST', 'localhost');
			DEFINE('DB_DATABASE', 'socialfootprint');

			$conn = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_DATABASE);
			if ($conn->connect_error) {
				die("couldn't connect to database server \n" . $conn->connect_error);
			} 
			try {
				$query = "INSERT INTO facebookuser(fbId, name, email, gender, hometown, currentLocation, locale, languages, birthdate, profilePic) 
							  VALUES ('". $fbId ."','".$name ."','".$email ."','".$gender ."','".$hometown ."','".$location ."','".$locale ."','".$languages ."','".$birthdate .
							  "','".$profile_pic_url ."')";
				$result = $conn->query($query) ;
			} 
			catch (Exception $e) {
			}
		}
		else{
			$permissions = ['user_birthday', 'email', 'user_likes', 'user_friends', 'public_profile', 'user_hometown', 'user_location', 'user_about_me']; // optional
			$loginUrl = $helper->getLoginUrl('http://localhost/appslogin.php', $permissions);
			echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
		}
	} 
	catch(Facebook\Exceptions\FacebookResponseException $e) {
		echo 'Graph returned an error: ' . $e->getMessage();
		exit;
	} 
	catch(Facebook\Exceptions\FacebookSDKException $e) {
		echo 'Facebook SDK returned an error: ' . $e->getMessage();
		exit;
	}
?>
	</div>
</body>
</html>