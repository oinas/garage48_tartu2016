<?php

$notifications = $db->notifications;
$n = $notifications->findOne(array("user" => $_SESSION['user']));
if(isset($n['count']) && $n['count'] > 0){
	$badge = '<div class="badge">' . $n['count'] . '</div>';
} else {
	$badge = "";
}

$menu = array(
	"users/view/" . $_SESSION['user'] => '<span class="glyphicon glyphicon-user"></span> ' . $_SESSION['fb_name'],
	"profile/edit" => '<span class="glyphicon glyphicon-pencil"></span> ' . "Edit profile",
	"wall" => '<span class="glyphicon glyphicon-bell"></span> ' . "Notifications {$badge}",
	"travel_plan/add" => '<span class="glyphicon glyphicon-plane"></span> ' . "Create travel plan",
	"product_request/add" => '<span class="glyphicon glyphicon-briefcase"></span> ' . "Make product request",
/*	"travel_plan" => "My travel plans",
	"product_request" => "My product requests",
	"pending/view/pending" => "Show pending requests",
	"pending/view/accepted" => "Show accepted requests",
	"messages" => "Show messages",
	"q=search&showall" => "Show all travel plans",
	"users" => "DEBUG User list"*/
);

// hack otherwise it will not cache the profile picture and will load it every page
$facebook_picture = getUserPicture($_SESSION['user']);

$HTML[] = <<<EOF
<div class="container-fluid">
	<div class="row">
		<div class="left-side col-sm-3">
			<center>
				<img src="{$facebook_picture}" width="90%" class="circle">

			</center>
			<ul class="side-bar-menu">
EOF;

foreach($menu as $k => $v){
	$HTML[] = <<<EOF
		<li class="left-menu-color"><a href="?{$k}">{$v}</a>
EOF;
}

$HTML[] = <<<EOF
			</ul>

		</div>

		<div class="center-side col-sm-9">
EOF;
