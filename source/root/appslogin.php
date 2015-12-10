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
		<meta name="google-signin-scope" content="profile email https://www.googleapis.com/auth/plus.login https://www.googleapis.com/auth/plus.me">
		<meta name="google-signin-client_id" content="318733159872-p0249g76untk8tob74o888elp6u4r1m5.apps.googleusercontent.com">
		<script src="https://apis.google.com/js/platform.js" async defer></script>
	</head>
 <body>
	<div>
		<?php
		$attributes= array();
		$attributes['name'] = array();	$attributes['name']['visible'] = 0;		$attributes['name']['weight'] = 0.2; $attributes['name']['attr'] = 'Name';
		$attributes['profilePicture'] = array();	$attributes['profilePicture']['visible'] = 1;		$attributes['profilePicture']['weight'] = 0.1; $attributes['profilePicture']['attr'] = 'Profile Picture';
		$attributes['Phone number'] = array();	$attributes['Phone number']['visible'] = 0;		$attributes['Phone number']['weight'] = 0.1; $attributes['Phone number']['attr'] = 'Phone number';
		$attributes['gender'] = array();	$attributes['gender']['visible'] = 0;		$attributes['gender']['weight'] = 0.1;	 $attributes['gender']['attr'] = 'Gender';
		$attributes['hometown'] = array();	$attributes['hometown']['visible'] = 0;		$attributes['hometown']['weight'] = 0.1; $attributes['hometown']['attr'] = 'Hometown';
		$attributes['currentlocation'] = array();	$attributes['currentlocation']['visible'] = 0;		$attributes['currentlocation']['weight'] = 0.1; $attributes['currentlocation']['attr'] = 'Current Location';
		$attributes['dob'] = array();	$attributes['dob']['visible'] = 0;		$attributes['dob']['weight'] = 0.1; $attributes['dob']['attr'] = 'Date of Birth';
		$attributes['language'] = array();	$attributes['language']['visible'] = 0;		$attributes['language']['weight'] = 0.1; $attributes['language']['attr'] = 'Language';
		$attributes['checkins'] = array();	$attributes['checkins']['visible'] = 0;		$attributes['checkins']['weight'] = 0.1; $attributes['checkins']['attr'] = 'Checkins';
		$attributes['nickname'] = array();	$attributes['nickname']['visible'] = 0;		$attributes['nickname']['weight'] = 0.1; $attributes['nickname']['attr'] = 'Nickname';
		$attributes['relationshipstatus'] = array();	$attributes['relationshipstatus']['visible'] = 0;		$attributes['relationshipstatus']['weight'] = 0.1; $attributes['relationshipstatus']['attr'] = 'Relationship Status';
		$attributes['industry'] = array();	$attributes['industry']['visible'] = 0;		$attributes['industry']['weight'] = 0.1; $attributes['industry']['attr'] = 'Works at';
		$attributes['email'] = array();	$attributes['email']['visible'] = 0;		$attributes['email']['weight'] = 0.1; $attributes['email']['attr'] = 'Email';
		$attributes['status'] = array();	$attributes['status']['visible'] = 0;		$attributes['status']['weight'] = 0.1; $attributes['status']['attr'] = 'Status';
		$attributes['Projects'] = array();	$attributes['Projects']['visible'] = 0;		$attributes['Projects']['weight'] = 0.1; $attributes['Projects']['attr'] = 'Projects';
		$attributes['Skills'] = array();	$attributes['Skills']['visible'] = 0;		$attributes['Skills']['weight'] = 0.1; $attributes['Skills']['attr'] = 'Skills';
		$attributes['Occupation'] = array();	$attributes['Occupation']['visible'] = 0;		$attributes['Occupation']['weight'] = 0.1; $attributes['Occupation']['attr'] = 'Occupation';
		$attributes['Family'] = array();	$attributes['Family']['visible'] = 0;		$attributes['Family']['weight'] = 0.1; $attributes['Family']['attr'] = 'Family';
		$attributes['Studies at'] = array();	$attributes['Studies at']['visible'] = 0;		$attributes['Studies at']['weight'] = 0.1; $attributes['Studies at']['attr'] = 'Studies at';

		echo '<script type="text/javascript">var attributes = '.json_encode($attributes)."</script>"?>

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
							$permissions = ['public_profile','user_friends','email','user_relationships','user_relationship_details','user_about_me', 'user_religion_politics','user_tagged_places','user_events','user_posts','user_location','user_hometown','user_birthday']; // optional
							
							//$permissions = ['public_profile','user_friends','user_events','user_posts'];

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
						$attributes['name']['visible'] = 1;

						$text =" ";
						$userNode = $fb->get('/'.$uid.'?fields=name,email,hometown,location,birthday,locale,picture,gender,languages,link,relationship_status')->getGraphUser();
						//echo $userNode;
						$text.= (isset($userNode['gender']))? 'attributes.gender.visible=1;':'';
						$gender = (isset($userNode['gender']))? ($userNode['gender']):'HIDDEN';
						$text.= (isset($userNode['email']))? 'attributes.email.visible = 1;' :'';
						$email = (isset($userNode['email']))? ($userNode['email']):'HIDDEN';
						$text.=  (isset($userNode['hometown']))? 'attributes.hometown.visible = 1;':'';
						$hometown =  (isset($userNode['hometown']))? ($userNode['hometown']['name']):'HIDDEN';
						$text.=  (isset($userNode['location']))? 'attributes.currentlocation.visible = 1;':'';
						$location =  (isset($userNode['location']))? ($userNode['location']['name']):'HIDDEN';
						$text.=  (isset($userNode['birthday']))? 'attributes.dob.visible = 1;':'';
						$birthdate =  (isset($userNode['birthday']))? ($userNode['birthday']->format('Y/m/d')):'HIDDEN';
						$text.=  (isset($userNode['relationship_status']))? 'attributes.relationshipstatus.visible = 1;':'';
						$relationship_status =  (isset($userNode['relationship_status']))? ($userNode['relationship_status']):'HIDDEN';
						
						$fbId = $userNode['id'];
						$link = $userNode['link'];
						$text.= (isset($userNode['languages']))?'attributes.language.visible = 1;':'';
						$languageList = (isset($userNode['languages']))? ($userNode['languages']):NULL;
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
					
						$response = $fb->get('/me?fields=birthday,events');
						$graphObject = $response->getGraphObject();
						$text.= (isset($graphObject['feed']))? 'attributes.checkins.visible = 1;':'';
						$checkins = (isset($graphObject['feed']))? ($graphObject['feed']):NULL;
						
						echo '<script type="text/javascript">'.$text.'console.log(attributes)</script>';
						echo "<br><b>Name: </b>".$name."<br>";
						echo "<b>Gender: </b>" . $gender ."<br>"; 
						echo "<b>Email Id: </b>" . $email ."<br>"; 
						echo "<b>Hometown: </b>" . $hometown."<br>"; 
						echo "<b>Current location: </b>" . $location."<br>";
						echo "<b>DOB: </b>" .$birthdate."<br>";
						//echo "<b>Locale: </b>" . $locale."<br>"; 
						echo "<b>Languages: </b>".$languages."<br>";	
						echo "<b>Relationship Status: </b>".$relationship_status."<br>";	
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
		<div id="three"> 
			<h3>Google+</h3>
			<div>
				<div id='gprofileLogin' class="g-signin2" data-onsuccess="onSignIn" data-theme="dark"></div>
				<div id='gprofile' style="visibility: hidden">
					<img src="" id="gProfilePic" alt="Profile Pic" style="width:100px;height:100px;">
					<span id="gProfileURL"></span>
					<div><b>Name: </b><span id='gFirstName'></span></div>
					<div><b>NickName: </b><span id='gnickname'></span></div>
					<div><b>birthday: </b><span id='gbirthday'></span></div>
					<div><b>Gender: </b><span id='ggender'></span></div>
					<div><b>Email: </b><span id='gEmailAddress'></span></div>
					<div><b>Current Location: </b><span id='gcurrentLocation'></span></div>
					<div><b>Language: </b><span id='glanguage'></span></div>
					<div><b>Places Lived: </b><span id='gplacesLived'></span></div>
					<div><b>Relationship Status: </b><span id='grelationshipStatus'></span></div>
					<div id="Positionsdiv" style="visibility: hidden"><b>Work: </b><table border="1"><tbody id="Positions"></tbody></table></div>
				</div>
			</div>
		</div>
	</div>

	</br></br>
	<div>
	<h3><button onclick="onCalculateClick()">Calculate</button><b> <span id="tgggw" style="visibility: hidden"></span></b></h3>
	<span id="tw" style="visibility: hidden"><b> Total Weight = <span id='totalweight'></span></span>  </br>
	<span id="twh" style="visibility: hidden"><b> Threhold = <span id='totalweightff'></span></span></br>
	<table border="1" id='wtt' style="visibility: hidden"><tbody id="weightTable"></tbody></table>
	
	</div>
	<!-- Faceook scripts -->
	<script type="text/javascript">
			function onCalculateClick() {
				var threshold=1.1;
				document.getElementById("wtt").style.visibility='visible';
				document.getElementById("tw").style.visibility='visible';
				
				document.getElementById("tgggw").style.visibility='visible';
				document.getElementById("twh").style.visibility='visible';
				var totalWeight=0;
				var tbody = document.getElementById('weightTable');
				tbody.innerHTML = '';
				th = "<tr><th>Attribute</th><th>Weight</th</tr>"
				tbody.innerHTML += th;
				for  (var key in attributes)
				{
					var tr = "<tr>";
					tr += "<td>" + attributes[key].attr + "</td><td>" + attributes[key].weight + "</td></tr>";
					totalWeight += attributes[key].visible*attributes[key].weight;
					tbody.innerHTML += tr;
				}
				
				document.getElementById('totalweightff').innerHTML = threshold;
				document.getElementById('totalweight').innerHTML = totalWeight.toFixed(2);
				document.getElementById('tgggw').innerHTML = (totalWeight.toFixed(2) <=threshold) ?'safe':'vulnerable';
			}

			function onSignIn(googleUser) {
				// Useful data for your client-side scripts:

				var data = googleUser.getBasicProfile();
				var id_token = googleUser.getAuthResponse().id_token;
				console.log("ID Token: " + id_token);
				
				document.getElementById("gprofileLogin").setAttribute("style","height:0px");
				document.getElementById("gprofile").style.visibility='visible';
				document.getElementById("gprofileLogin").style.visibility='hidden';
				document.getElementById("gFirstName").innerHTML = data.getName();		attributes.name.visible = 1;
				document.getElementById("gEmailAddress").innerHTML = data.getEmail();	attributes.email.visible = 1;
				document.getElementById("gProfilePic").src = data.getImageUrl();

				var apikey="AIzaSyCKXsT-m5yTZo3Ki0GecHfUBpa-L0WOUbk";
				var url = "https://www.googleapis.com/plus/v1/people/"+data.getId()+"?fields=ageRange%2Cbirthday%2CcurrentLocation%2Cgender%2Clanguage%2Cnickname%2CplacesLived%2CrelationshipStatus%2Curl&key="+ apikey;
				var representationOfDesiredState = "The";
				var client = new XMLHttpRequest();
				client.open("GET", url, false);
				client.send(representationOfDesiredState);
				console.log(client.responseText);
				var userData = JSON.parse(client.responseText);

				var aggender = (userData.gender != undefined)?userData.gender:'HIDDEN';
				var agbirthday = (userData.birthday != undefined)?userData.birthday:'HIDDEN';
				var agcurrentLocation = (userData.currentLocation != undefined)?userData.currentLocation:'HIDDEN';
				var aglanguage = (userData.language != undefined)? userData.language:'HIDDEN';
				var agnickname = (userData.nickname != undefined)?userData.nickname:'HIDDEN';
				var agplacesLived = (userData.placesLived != undefined)?userData.placesLived[0].value:'HIDDEN';
				var agrelationshipStatus = (userData.relationshipStatus != undefined)?userData.relationshipStatus:'HIDDEN';

				if (userData.url != undefined)
					document.getElementById("gProfileURL").innerHTML = "<a href='" + userData.url + "'>Profile URL</a>";
				
				document.getElementById("ggender").innerHTML = aggender;	
				if(aggender !='HIDDEN')
					attributes.gender.visible = 1;
				document.getElementById("gbirthday").innerHTML = agbirthday;	
				if(agbirthday !='HIDDEN')
					attributes.dob.visible = 1;
				document.getElementById("gcurrentLocation").innerHTML = agcurrentLocation;	 
				if(agcurrentLocation !='HIDDEN')
					attributes.currentlocation.visible = 1;
				document.getElementById("glanguage").innerHTML = aglanguage;	
				console.log(aglanguage);
				if(aglanguage !='HIDDEN')
					attributes.language.visible = 1;
				document.getElementById("gnickname").innerHTML =agnickname;	
				if(agnickname !='HIDDEN')
					attributes.nickname.visible = 1;
				document.getElementById("gplacesLived").innerHTML = agplacesLived;	
				if(agplacesLived !='HIDDEN')
					attributes.checkins.visible = 1;
				document.getElementById("grelationshipStatus").innerHTML = agrelationshipStatus;	
				if(agrelationshipStatus !='HIDDEN')
					attributes.relationshipstatus.visible = 1;
			};

		    // Setup an event listener to make an API call once auth is complete
			function onLinkedInLoad() {
				IN.Event.on(IN, "auth", getProfileData);
			}

			function onLogoutClick()
			{
				var auth2 = gapi.auth2.getAuthInstance();
				auth2.signOut().then(function () {
					console.log('google+ User signed out.');
				});

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
				attributes.name.visible = 1;
				document.getElementById("headline").innerHTML = data.headline;
				if(data.headline != undefined || data.headline !='')
					attributes.status.visible = 1;

				document.getElementById("UserId").innerHTML = data.id;			
				document.getElementById("ProfilePic").src = data.pictureUrl;	
				document.getElementById("Location").innerHTML = data.location.name;
				if(data.location.name != undefined && data.location.name !='')
					attributes.currentlocation.visible = 1;
				document.getElementById("Industry").innerHTML = data.industry;
				if(data.industry != undefined && data.industry !='')
					attributes.industry.visible = 1;
				document.getElementById("Summary").innerHTML = data.summary;
				if(data.summary != undefined && data.summary!='')
					attributes.status.visible = 1;
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
				if(data.emailAddress != undefined && data.emailAddress !='')
					attributes.email.visible = 1;
			}

			// Handle an error response from the API call
			function onError(error) {
				console.log(error);
			}

			// Use the API call wrapper to request the member's basic profile data
			function getProfileData() {
				IN.API.Raw("/people/~:(id,firstName,lastName,headline,picture-url,location,industry,summary,positions,public-profile-url,email-address)?format=json").result(onSuccess).error(onError);
			}
console.log(attributes);
		</script>
	</body>
</html>
