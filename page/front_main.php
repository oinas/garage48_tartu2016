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
<h1>CICO brings what you desire</h1>
<span>Travelers can bring you products from all over the world!</span><br><br><br>
<a href="{$tmp}" class="btn btn-primary btn-lg btn-darkblue"><img src="css/fb_white_29.png" style="margin-right: 10px; margin-top: -2px;"> Log in with Facebook</a>
<br><br><h3>Try it yourself</h3>
</div>

<div class="front-powered">
<strong>POWERED BY</strong><br>
<a href="https://azure.microsoft.com/en-us/" target="_blank"><img src="css/cloudazure.jpg" width="200"></a><br>
<a href="https://php.net" target="_blank"><img src="css/php.png" width="200"></a><br>
<a href="http://garage48.org" target="_blank"><img src="css/garage48.png" width="200" class="makewhiteborder"></a><br>
<a href="https://www.mongodb.org/" target="_blank"><img src="css/mongodb.png" width="93"></a>
<a href="https://developers.facebook.com/" target="_blank"><img src="css/facebookapi.jpg" width="93"></a><br>

</div>
EOF;
