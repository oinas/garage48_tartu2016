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
<!--div class="alert alert-warning" role="alert" style="width: 50%; font-size: 1em;">
Demo users: man, woman, user, traveler, requester<br>
Password is test
EOF;

formHeader();
formField("User", "user", "text");
formField("Password", "password", "password");
formFooter();

$HTML[] = <<<EOF
</div-->
EOF;

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

$permissions = ['email']; // Optional permissions
if(file_exists("../index2.html")){
	$loginUrl = $helper->getLoginUrl('http://cico.com/garage48_tartu2016/fb_callback.php', $permissions);
} else {
	$loginUrl = $helper->getLoginUrl('http://cico.northeurope.cloudapp.azure.com/fb_callback.php', $permissions);
}
$tmp = htmlspecialchars($loginUrl);
$HTML[] = <<<EOF
<div class="front-middle">
<h1>CICO brings you what you desire</h1>
Travelers can bring you products from all over the world!<br><br><br>
<a href="{$tmp}" class="btn btn-primary btn-lg"><img src="css/fb_white_29.png" style="margin-right: 10px; margin-top: -2px;"> Log in with Facebook!</a>
<br><br><h1>Quick &amp; easy login</h1>
</div>
EOF;
