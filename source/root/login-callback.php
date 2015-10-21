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
  
  echo '<br>Login success! <br>';
  
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

//echo "<br>'/id''s Your email is " . $fb->get('/me/user_email')->getGraphEdge() ."<br>"; //what is the GET for email? not email/user_email

//echo "<br>'/id''s Your hometown is " . $fb->get('/me/hometown')->getGraphObject() ."<br>"; //get for hometown?

//echo "<br>'/id''s Your location is " . $fb->get('/me/location')->getGraphEdge() ."<br>"; //get for location?

//echo "<br>Your dob is <br>" . $fb->get('/me/birthday')->getGraphObject() ."<br>"; //get for dob? birthday? user_birthday?

//echo "<br>'/id''s Your about is " . $fb->get('/me/user_about_me')->getGraphEdge() ."<br>"; //about? about_me? user_about_me?

//echo "<br>Your locale is <br>" . $fb->get('/me/locale')->getGraphEdge() ."<br>"; //??

echo "<br>Your pic is <br>" . $fb->get('/me/picture')->getGraphObject() ."<br>"; 

echo "<br>Your likes are <br>" . $fb->get('/me/likes')->getGraphEdge() ."<br>"; 

/*
This list is available through the likes edge on the User object.
is edges different from fields?
*/

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