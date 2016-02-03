<?php

/* LOGIN FUNCTIONALLITY HERE */
if(isset($_POST['submit'])){
	$entry = $db->users->findOne(array("user" => $_POST['user']));
	if(!empty($entry)){
		if($entry['password'] == md5($_POST['password'])){
			// login correct
			$SUCCESS[] = "Login successful";
			header("Location: ?wall");
			$_SESSION['user'] = $entry['_id'];
		} else {
			$ERROR[] = "User and/or password wrong";
			header("Location: ?");
		}
	} else {
		$ERROR[] = "User and/or password wrong";
		header("Location: ?");
	}		
}

$HTML[] = <<<EOF
Demo users: man, woman, user, traveler, requester<br>
Password is test
EOF;

formHeader();
formField("User", "user", "text");
formField("Password", "password", "password");
formFooter();

require_once "Facebook/autoload.php";

$fb = new Facebook\Facebook([
  'app_id' => '{app-id}',
  'app_secret' => '{app-secret}',
  'default_graph_version' => 'v2.2',
  ]);

$helper = $fb->getRedirectLoginHelper();

$permissions = ['email']; // Optional permissions
$loginUrl = $helper->getLoginUrl('https://localhost/fb-callback.php', $permissions);

$HTML[] = '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
