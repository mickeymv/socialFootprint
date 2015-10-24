<?php
@ob_start();
session_start();
?>
<html>
 <head>
  <title>Facebook login callback page</title>
 </head>
 <body>
  	<?php 
  	  	require_once __DIR__ . '/facebook-sdk-v5/autoload.php';
    $fb = new Facebook\Facebook([
  'app_id' => '1646244812311215',
  'app_secret' => 'b798f23b27dd787c88b69e4972fe8869',
  'default_graph_version' => 'v2.5',
  ]);

$helper = $fb->getRedirectLoginHelper();
try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (isset($accessToken)) {
  // Logged in!
  /*
  echo '<h3>Access Token</h3>';  
var_dump($accessToken->getValue()); 
 */
 
 // The OAuth 2.0 client handler helps us manage access tokens  
$oAuth2Client = $fb->getOAuth2Client();  

// Get the access token metadata from /debug_token  
$tokenMetadata = $oAuth2Client->debugToken($accessToken);  

/*
echo '<h3>Metadata</h3>';  
var_dump($tokenMetadata);  
  */
  
// Validation (these will throw FacebookSDKException's when they fail)  
//$tokenMetadata->validateAppId($config['app_id']);  //This is taken from the fb example for login. What is $config?
// If you know the user ID this access token belongs to, you can validate it here  
// $tokenMetadata->validateUserId('123');  
$tokenMetadata->validateExpiration();   
   
if (! $accessToken->isLongLived()) {  
  // Exchanges a short-lived access token for a long-lived one  
  try {  
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);  
  } catch (Facebook\Exceptions\FacebookSDKException $e) {  
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>";  
    exit;  
  } 
  echo '<h3>Long-lived</h3>';  
  var_dump($accessToken->getValue());  
}

$_SESSION['fb_access_token'] = (string) $accessToken;  
  
// User is logged in with a long-lived access token.  
// You can redirect them to a members-only page.  
// header('Location: https://example.com/members.php');

  $_SESSION['facebook_access_token'] = (string) $accessToken;

  // Now you can redirect to another page and use the
  // access token from $_SESSION['facebook_access_token']
  
  echo 'Login success! <br>';
  
  // Sets the default fallback access token so we don't have to pass it to each request
$fb->setDefaultAccessToken($accessToken);

try {
  $response = $fb->get('/me');
  $graphObject = $response->getGraphObject();
  
/*
  $userNode = $response->getGraphUser();
echo '<br>Logged in as ' . $userNode->getName()."<br>";

echo "<br>User's graphUser is " . $userNode."<br>";
*/
echo "<br>User's graphObject is " . $graphObject."<br>";
echo "<br>User Name: ".$graphObject['name']."<br>";
$uid = $graphObject['id'];
/*
GraphUser and GraphObject were returning same response when calling "/me"

User's graphUser is {"name":"Mickey Vellukunnel","id":"10154230519659881"}

User's graphObject is {"name":"Mickey Vellukunnel","id":"10154230519659881"}


*/
  
  /* PHP SDK v5.0.0 */
/* make the API call */
/*
$request = new Facebook\FacebookRequest(
  $session,
  'GET',
  '/me'
);
*/
 //FacebookRequest? 
$userNode = $fb->get('/me?fields=name,email,hometown,location,birthday,locale,picture,gender,languages')->getGraphUser();
echo "<br>Your gender: " . $userNode['gender'] ."<br>"; 
echo "<br>Your email Id: " . $userNode['email'] ."<br>"; //what is the GET for email? not email/user_email

echo "<br>Your hometown is " . $userNode['hometown']['name']."<br>"; //get for hometown?

echo "<br>Your current location is " . $userNode['location']['name']."<br>"; //get for location?

echo "<br>Your dob is " .$userNode['birthday']->format('d/m/Y')."<br>"; //get for dob? birthday? user_birthday?

//echo "<br>Your about is " . $userNode['locale']."<br>"; //about? about_me? user_about_me?

echo "<br>Your locale is " . $userNode['locale']."<br>"; //??

echo "<br>Languages u know: ";
$languages = $userNode['languages'];
foreach($languages as $language)
	echo $language['name'] .","; 

//echo "<br>Your pic is <br>" . $userNode['picture']."<br>"; 
$profile_pic_url =  "http://graph.facebook.com/".$uid."/picture";
 //echo the image out
 echo "<br>your profile picture: <img src=\"" . $profile_pic_url . "\" /><br>"; 

$likes = $fb->get('/me/likes')->getGraphEdge();
echo "<br>Your likes are: <br>"; 
echo '<table>';
foreach($likes as $like)
{
     echo '<tr>';
     foreach($like as $fields)
     {
		 if(is_a($fields,'datetime') )
			 echo '<td>'.$fields->format('d/m/Y').'</td>';
		 else
             echo '<td>'.$fields.'</td>';
     }
      echo '</tr>';
} 
echo '<table>';
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

} else {
  if ($helper->getError()) {  
    header('HTTP/1.0 401 Unauthorized');  
    echo "Error: " . $helper->getError() . "\n";
    echo "Error Code: " . $helper->getErrorCode() . "\n";
    echo "Error Reason: " . $helper->getErrorReason() . "\n";
    echo "Error Description: " . $helper->getErrorDescription() . "\n";
  } else {  
    header('HTTP/1.0 400 Bad Request');  
    echo 'Bad request';  
  }  
  exit;  
}
?>
 </body>
</html>