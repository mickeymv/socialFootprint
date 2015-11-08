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
		<link rel="stylesheet" type="text/css" href="appslogin_css.css">
		<script type="text/javascript" src="//platform.linkedin.com/in.js">
			api_key:   772dtbul04ktif
			authorize: true
			onLoad: onLinkedInLoad
			scope: r_emailaddress
		</script>
		<title>Apps login</title>
	</head>
 <body>
	<div>
		<span><?php echo "<b>Current User: </b>".$_SESSION['username']?></span>
		&nbsp;&nbsp;&nbsp;&nbsp;
		<span>
			<button onclick="onLogoutClick()">Log Out</button>
		</span>
	</div>
    <div id="container"> 
		<div id="one">
			<h3>FACEBOOK</h3>
			<div>
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
							echo '<a href="' . htmlspecialchars($loginUrl) . '"><image src="fbLogin.png"></a>';
						}
						else
						{
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
						$response = $fb->get('/me');
						$graphObject = $response->getGraphObject();
						$uid = $graphObject['id'];
						$name = $graphObject['name'];			
						
						$userNode = $fb->get('/me?fields=name,email,hometown,location,birthday,locale,picture,gender,languages,link')->getGraphUser();
						$gender = (isset($userNode['gender']))?$userNode['gender']:'HIDDEN';
						$email = (isset($userNode['email']))?$userNode['email']:'HIDDEN';
						$hometown =  (isset($userNode['hometown']))?$userNode['hometown']['name']:'HIDDEN';
						$location =  (isset($userNode['location']))?$userNode['location']['name']:'HIDDEN';
						$birthdate =  (isset($userNode['birthday']))?$userNode['birthday']->format('Y/m/d'):'HIDDEN';
						$locale = (isset($userNode['locale']))?$userNode['locale']:'HIDDEN';
						$fbId = $userNode['id'];
						$link = $userNode['link'];
						$languageList = (isset($userNode['languages']))?$userNode['languages']:NULL;
						$languages = "";
						if($languageList!=NULL)
							foreach($languageList as $language)
								$languages =  $languages.$language['name'] .","; 
						else
							$languages = "HIDDEN";

						$profile_pic_url =  "http://graph.facebook.com/".$fbId."/picture?type=large";
						$likes = $fb->get('/me/likes')->getGraphEdge();

						echo "<img alt='Profile Pic' style='width:100px;height:100px;' src=\"" . $profile_pic_url . "\" >";
						echo " <a href=\"" .$link . "\">Profile URL</a>";
						
						$response = $fb->get('/me?fields=birthday,feed.with(location),events');
						$graphObject = $response->getGraphObject();
						$checkins = (isset($graphObject['feed']))?$graphObject['feed']:NULL;
						$events = (isset($graphObject['events']))?$graphObject['events']:NULL;

						echo "<br><b>Name: </b>".$name."<br>";
						echo "<b>Gender: </b>" . $gender ."<br>"; 
						echo "<b>Email Id: </b>" . $email ."<br>"; 
						echo "<b>Hometown: </b>" . $hometown."<br>"; 
						echo "<b>Current location: </b>" . $location."<br>";
						echo "<b>DOB: </b>" .$birthdate."<br>";
						echo "<b>Locale: </b>" . $locale."<br>"; 
						echo "<b>Languages: </b>".$languages."<br>";	
						echo "<b>Checkins: </b><br>"; 
						if($checkins!=NULL)
						{
							echo '<table border="1">';
							echo '<tr style="font-weight:bold"><td>PLACE</td><td>DATE</td><td>TIME</td></tr>';

							foreach($checkins as $checkin)
							{
								$datetime = $checkin['created_time'];
								
								$response = $fb->get('/'.$checkin['id'].'?fields=privacy,place');
								$graphObject = $response->getGraphObject();
								$privacy = $graphObject['privacy']['value'];
								if($privacy != "EVERYONE" && $privacy != "")
									continue;
								echo '<tr>';
								echo '<td>'.$graphObject['place']['name'].'</td>';
								echo '<td>'.$datetime->format('Y/m/d').'</td>';
								echo '<td>'.$datetime->format('H:m:s').'</td>';
								echo '</tr>';
							} 
							echo '</table>';
						}
						else
							echo "HIDDEN";
								
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
		</div>
		<div id="two"> 
			<h3>LINKEDIN</h3>
			<div>
				<script type="in/Login"></script>
				<div id='profile' style="visibility: hidden">
					<img src="" id="ProfilePic" alt="Profile Pic" style="width:100px;height:100px;">
					<span id="ProfileURL"></span>
					<div><b>First Name: </b><span id='FirstName'></span></div>
					<div><b>LastName: </b><span id='LastName'></span></div>
					<div><b>headline: </b><span id='headline'></span></div>
					<div><b>UserId: </b><span id='UserId'></span></div>
					<div><b>Location: </b><span id='Location'></span></div>
					<div><b>Industry: </b><span id='Industry'></span></div>
					<div><b>Summary: </b><span id='Summary'></span></div>
					<div><b>EmailAddress: </b><span id='EmailAddress'></span></div>
					<div id="Positionsdiv" style="visibility: hidden"><b>Work: </b><table border="1"><tbody id="Positions"></tbody></table></div>
				</div>
			</div>
		</div>
	</div>
	<!-- Faceook scripts -->
	<script type="text/javascript">
		    // Setup an event listener to make an API call once auth is complete
			function onLinkedInLoad() {
				IN.Event.on(IN, "auth", getProfileData);
			}

			function onLogoutClick()
			{
				console.log("logging out");
				IN.User.logout(afterLogout);
			}

			function afterLogout()
			{
				window.location = "logout.php";
			}

			// Handle the successful return from the API call
			function onSuccess(data) {
				console.log(data);
				document.getElementById("profile").style.visibility='visible';
				document.getElementById("FirstName").innerHTML = data.firstName;
				document.getElementById("LastName").innerHTML = data.lastName;
				document.getElementById("headline").innerHTML = data.headline;
				document.getElementById("UserId").innerHTML = data.id;			
				document.getElementById("ProfilePic").src = data.pictureUrl;	
				document.getElementById("Location").innerHTML = data.location.name;
				document.getElementById("Industry").innerHTML = data.industry;
				document.getElementById("Summary").innerHTML = data.summary;
				obj = data.positions;
				if(obj._total != 0)
					document.getElementById("Positionsdiv").style.visibility='visible'
				var tbody = document.getElementById('Positions');
				for (var i = 0; i < obj._total; i++) 
				{
					val = obj.values;
					var tr = "<tr>";
					tr += "<td>" + val[i].title + "</td><td>" + val[i].company.name + "</td><td>" + val[i].endDate.month + "/" + val[i].endDate.year + "</td><td>" + val[i].isCurrent + "</td></tr>";
					tbody.innerHTML += tr;
				}
				document.getElementById("ProfileURL").innerHTML = "<a href='" + data.publicProfileUrl + "'>Profile URL</a>";
				document.getElementById("EmailAddress").innerHTML = data.emailAddress;
			}

			// Handle an error response from the API call
			function onError(error) {
				console.log(error);
			}

			// Use the API call wrapper to request the member's basic profile data
			function getProfileData() {
				IN.API.Raw("/people/~:(id,firstName,lastName,headline,picture-url,location,industry,summary,positions,public-profile-url,email-address)?format=json").result(onSuccess).error(onError);
			}

		</script>
</body>
</html>
