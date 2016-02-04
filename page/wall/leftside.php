<?php

$menu = array(
	"wall" => "Home",
	"travel_plan/add" => "Create travel plan",
	"product/add" => "Make product request",
	"travel_plan" => "My travel plans",
	"product_view" => "View product requests",
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
				<br>{$_SESSION['fb_name']}

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
