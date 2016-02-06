<?php

require_once "config.php";
require_once "Facebook/autoload.php";

if(file_exists("../index2.html")){
  $fb = new Facebook\Facebook([
    'app_id' => '999172896799797',
    'app_secret' => '5a5e05106f4900298a7fbbb9f1ae9c1a',
    'default_graph_version' => 'v2.2',
    ]);
} else {
  $fb = new Facebook\Facebook([
  'app_id' => '999171513466602',
  'app_secret' => 'c09515e82d9ff150603a9eaf535b4bad',
    'default_graph_version' => 'v2.2',
    ]);
}

$helper = $fb->getRedirectLoginHelper();

try {
  $accessToken = $helper->getAccessToken();
} catch(Facebook\Exceptions\FacebookResponseException $e) {
  // When Graph returns an error
  header("Location: {$BASEHREF}?");
  echo 'Graph returned an error: ' . $e->getMessage();
  exit;
} catch(Facebook\Exceptions\FacebookSDKException $e) {
  // When validation fails or other local issues
  header("Location: {$BASEHREF}?");
  echo 'Facebook SDK returned an error: ' . $e->getMessage();
  exit;
}

if (! isset($accessToken)) {
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

// Logged in
/*echo '<h3>Access Token</h3>';
var_dump($accessToken->getValue());*/

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();

// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
/*echo '<h3>Metadata</h3>';
var_dump($tokenMetadata);*/

// Validation (these will throw FacebookSDKException's when they fail)
//$tokenMetadata->validateAppId(999172896799797);
// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();

if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
    exit;
  }

  echo '<h3>Long-lived</h3>';
  var_dump($accessToken->getValue());
  header("Location: {$BASEHREF}?");
  exit;
}

$_SESSION['fb_access_token'] = (string) $accessToken;

$response = $fb->get('/me?fields=id,name,email', $accessToken);

$user = $response->getGraphUser();
$page = $response->getGraphPage();

$_SESSION['fb_id'] = $user['id'];
$_SESSION['fb_name'] = $user['name'];
$_SESSION['fb_email'] = $user['email'];

/** make database entry */
$entries = $db->users;
$entry = $entries->findOne(array("facebookid" => $user['id']));

if(empty($entry)){
  $tmp = explode("@", $user['email']);
  $entries->insert(
    array(
      "user" => $tmp[0],
      "password" => md5("test"),
      "first" => $user['name'],
      "last" => "",   //deprecated, we do not use lastname, the name will come automatically from facebook
      "email" => $user['email'],
      "facebookid" => $user['id'],
      "facebookAccess" => (string) $accessToken,
      "googleid" => "",
      "date" => date("Y-m-d H:i:s"),
      "last" => microtime(true),
      "status" => "0",
      "description" => "",
      "banstatus" => 0
      )
    );
} else {
  $entry['facebookAccess'] = (string) $accessToken;
  $entry['last'] = microtime(true);
  /** update */
  $entries->update(
      array("facebookid" => $user['id']),
      $entry
    );
}

$_SESSION['user'] = $user['id'];

/*
print_r($user);
print_r($page);

print_r($response);
var_dump($response);
*/

// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
header("Location: {$BASEHREF}?front_search");