<?php

$menu = array(
	"wall" => "Home",
	"travel_plan/add" => "Create travel plan",
	"product/add" => "Make product request",
	"travel_plan_view" => "My travel plans",
	"product_view" => "View product requests",
);

$HTML[] = <<<EOF
<div class="container-fluid">
	<div class="row">
		<div class="left-side col-md-3">
			<h1>Profile</h1>
			<img src="upload/profile.jpg" width="200">

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
