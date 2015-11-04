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
		<p><?php echo "Current User:".$_SESSION['username']?></p><p><a href="logout.php">Logout</a></p>
	</div>
    <div> 
		<h3>Facebook</h3>
<?php 
	if(!isset($_SESSION['fb_access_token']))
	{
		try{
			$accessToken = $helper->getAccessToken();
		}
		catch(Facebook\Exceptions\FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} 
		catch(Facebook\Exceptions\FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		if(!isset($accessToken))
		{
			$permissions = ['user_friends','user_events','user_posts']; // optional
			
			$loginUrl = $helper->getLoginUrl('http://localhost/appslogin.php',$permissions);
			echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
		}
		else
		{
			$oAuth2Client = $fb->getOAuth2Client();  
			$tokenMetadata = $oAuth2Client->debugToken($accessToken);  
			$tokenMetadata->validateExpiration();   
			if (! $accessToken->isLongLived()) {  
				echo 4;
				try {
					$accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);  
				}
				catch (Facebook\Exceptions\FacebookSDKException $e) {  
					echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>";  
				} 
				//var_dump($accessToken->getValue());  		
			}			
			$_SESSION['fb_access_token'] =  (string) $accessToken;  	
			$fb->setDefaultAccessToken($_SESSION['fb_access_token']);
		}	
	}
	else
	{
		$accessToken = $_SESSION['fb_access_token'];		
		$fb->setDefaultAccessToken($accessToken);
	}
	if(isset($accessToken))
	{
		echo 'Login successful!   ';
		$response = $fb->get('/me');
		$graphObject = $response->getGraphObject();
		$uid = $graphObject['id'];
		$name = $graphObject['name'];			
		
		$userNode = $fb->get('/me?fields=name,email,hometown,location,birthday,locale,picture,gender,languages')->getGraphUser();
		$gender = (isset($userNode['gender']))?$userNode['gender']:'HIDDEN';
		$email = (isset($userNode['email']))?$userNode['email']:'HIDDEN';
		$hometown =  (isset($userNode['hometown']))?$userNode['hometown']['name']:'HIDDEN';
		$location =  (isset($userNode['location']))?$userNode['location']['name']:'HIDDEN';
		$birthdate =  (isset($userNode['birthday']))?$userNode['birthday']->format('Y/m/d'):'HIDDEN';
		$locale = (isset($userNode['locale']))?$userNode['locale']:'HIDDEN';
		$fbId = $userNode['id'];
		$languageList = (isset($userNode['languages']))?$userNode['languages']:NULL;
		$languages = "";
		if($languageList!=NULL)
			foreach($languageList as $language)
				$languages =  $languages.$language['name'] .","; 
		else
			$languages = "HIDDEN";

		$profile_pic_url =  "http://graph.facebook.com/".$fbId."/picture";
		$likes = $fb->get('/me/likes')->getGraphEdge();
		echo "<img src=\"" . $profile_pic_url . "\" /><br>"; 
		
		$response = $fb->get('/me?fields=birthday,feed.with(location),events');
		$graphObject = $response->getGraphObject();
		$checkins = (isset($graphObject['feed']))?$graphObject['feed']:NULL;
		$events = (isset($graphObject['events']))?$graphObject['events']:NULL;

		echo "Name: ".$name."<br>";
		echo "Gender: " . $gender ."<br>"; 
		echo "Email Id: " . $email ."<br>"; 
		echo "Hometown: " . $hometown."<br>"; 
		echo "Current location: " . $location."<br>";
		echo "DOB: " .$birthdate."<br>";
		echo "Locale: " . $locale."<br>"; 
		echo "Languages: ".$languages."<br>";	

		echo "<br>Checkins: <br>"; 
		if($checkins!=NULL)
		{
			echo '<table>';
			echo '<tr style="font-weight:bold"><td>story</td><td>Date</td><td>Time</td><td>Privacy</td></tr>';

			foreach($checkins as $checkin)
			{
				$datetime = $checkin['created_time'];
				echo '<tr>';
				$response = $fb->get('/'.$checkin['id'].'?fields=privacy');
				$graphObject = $response->getGraphObject();

				//echo '<td>'.$checkin['id'].'</td>';
				echo '<td>'.$checkin['story'].'</td>';
				echo '<td>'.$datetime->format('Y/m/d').'</td>';
				echo '<td>'.$datetime->format('H:m:s').'</td>';
				echo '<td>'.$graphObject['privacy']['value'].'</td>';
				echo '</tr>';
			} 
			echo '<table>';
		}
		else
			echo "HIDDEN";

		echo "<br>Events RSVP (Only Friends): <br>"; 
		if($events!=NULL)
		{
			echo '<table>';
			echo '<tr style="font-weight:bold"><td>Place</td><td>Location</td><td>Date</td><td>Time</td><td>Status</td></tr>';

			foreach($events as $event)
			{
				$datetime = $event['start_time'];
				echo '<tr>';
				//echo '<td>'.$checkin['id'].'</td>';
				echo '<td>'.$event['place']['name'].'</td>';
				if(isset($event['place']['location'])){
					$loc = preg_replace("/[^a-zA-Z0-9]+/", "-", html_entity_decode($event['place']['location']));
					echo '<td><span title="'.$loc.'">Locator</span></td>'; 
				}
				else
					echo '<td><span title="TBA">Locator</span></td>'; 
				echo '<td>'.$datetime->format('Y/m/d').'</td>';
				echo '<td>'.$datetime->format('H:m:s').'</td>';
				echo '<td>'.$event['rsvp_status'].'</td>';
				echo '</tr>';
			} 
			echo '<table>';
		}
		else
			echo "HIDDEN";
// Printing the Likes--commented  out
//		echo "<br>Likes: <br>"; 
//		echo '<table>';
//		foreach($likes as $like)
//		{
//			echo '<tr>';
//			foreach($like as $fields)
//			{
//				if(is_a($fields,'datetime') )
//					echo '<td>'.$fields->format('Y/m/d').'</td>';
//				else
//					echo '<td>'.$fields.'</td>';
//			}
//			echo '</tr>';
//		} 
//		echo '<table>';

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
			exit;
		}	
	}
?>
	</div>
</body>
</html>