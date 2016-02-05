<?php

$notifications = $db->notifications;
$n = $notifications->findOne(array("user" => $_SESSION['user']));
if(isset($n['count']) && $n['count'] > 0){
	$badge = '<div class="badge">' . $n['count'] . '</div>';
} else {
	$badge = "";
}

$menu = array(
	"wall" => "Notifications {$badge}",
	"travel_plan/add" => "Create travel plan",
/*	"product_request/add" => "Make product request",*/
	"travel_plan" => "My travel plans",
	"product_request" => "My product requests",
	"pending/view/pending" => "Show pending requests",
	"pending/view/accepted" => "Show accepted requests",
	"messages" => "Show messages",
	"q=search&showall" => "Show all travel plans",
	"users" => "DEBUG User list"
);

// hack otherwise it will not cache the profile picture and will load it every page
$facebook_picture = getUserPicture($_SESSION['user']);

$HTML[] = <<<EOF
<div class="container-fluid">
	<div class="row">
		<div class="left-side col-md-3">
			<h1>Profile</h1>
			<center>
				<img src="{$facebook_picture}" width="90%" class="circle">
				<div class="profile-name">
				<a href="?profile/edit">{$_SESSION['fb_name']}</a>
				<a href="?users/view/{$_SESSION['fb_id']}">
					<span class="glyphicon glyphicon-search"></span>
				</a>
				</div>

			</center>
			<ul class="side-bar-menu">
EOF;

foreach($menu as $k => $v){
	$HTML[] = <<<EOF
		<li class="btn btn-primary"><a href="?{$k}">{$v}</a>
EOF;
}

$HTML[] = <<<EOF
			</ul>

		</div>

		<div class="center-side col-md-9">
EOF;
