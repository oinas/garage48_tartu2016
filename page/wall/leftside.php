<?php

$menu = array(
	"wall" => "Home",
	"travel_plan/add" => "Create travel plan",
	"product/add" => "Make product request",
	"travel_plan_view" => "My travel plans",
	"product_view" => "View product requests",
);

// hack otherwise it will not cache the profile picture and will load it every page
if(!isset($_SESSION['fb_picture']) && isset($_SESSION['fb_id'])){
	$json = file_get_contents("https://graph.facebook.com/" . $_SESSION['fb_id'] . "/picture?type=large&redirect=0");
	$tmp = json_decode($json);
	$_SESSION['fb_picture'] = $tmp->data->url;
}

$facebook_picture = isset($_SESSION['fb_picture']) ? $_SESSION['fb_picture'] : "css/noimage.jpg";
$facebook_picture = "css/noimage.jpg";

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
