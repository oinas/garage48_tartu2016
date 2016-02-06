<?php

$to = "";
$from = "";
if(isset($_GET['to'])){
	$to = $_GET['to'];
}
if(isset($_GET['from'])){
	$from = $_GET['from'];
}

if(file_exists("../index2.html")){
	$css = "localmain.css";
} else {
	$css = "main.css";
}

$notifications = $db->notifications;
$n = $notifications->findOne(array("user" => $_SESSION['user']));

if(isset($n['count']) && $n['count'] > 0){
	$badge = '<div class="badge">' . $n['count'] . '</div>';
	$not = "You have new notifications";
} else {
	$badge = "";
	$not = "Look your notifications";
}

$HTML[] = <<<EOF
<!DOCTYPE html> 
<html lang=en>
	<head>
		<title>{$TITLE} &middot; {$PAGE}</title>
		<meta charset="utf-8" />
		<base href="{$BASEHREF}" />
		<link rel="shortcut icon" href="favicon.ico?v1" />
		<script src="js/jquery-2.2.0.min.js"></script>
		<script src="js/jquery-ui.js"></script>
		<script src="js/jquery.tablesorter.min.js"></script> 
		<link rel="stylesheet" href="css/jquery-ui.css">
		<link rel="stylesheet" href="css/bootstrap.min.css">
<!-- FACEBOOK START -->
		<meta property="og:title" content="CICO - cico brings what you want" />
		<meta property="og:site_name" content="CICO" />
		<meta property="og:type" content="website" />
		<meta property="og:description" content="Travelers can bring you products from all over the world!" />
		<meta property="og:url" content="http://cico.northeurope.cloudapp.azure.com" />
		<meta property="og:image" content="http://cico.northeurope.cloudapp.azure.com/css/biglogo.png" />
<!-- FACEBOOK END -->
		<meta name="description" content="Travelers can bring you products from all over the world!" />
		<link rel="stylesheet" type="text/css" href="css/{$css}" />
		<link rel="stylesheet" media="(max-width: 800px)" href="css/mobile.css" />
		<link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css' />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
	</head>
	<body>

	<div class="body">
		<div class="header">
			<a href="?front_search"><img src="css/logo.png" class="header-logo"></a>


			

			<div class="menu">
				<a href="?front_search"><span class="glyphicon glyphicon-search icon-large">
					<div class="icon-large-title">Make search</div>
				</span></a>
				
				<div class="bigspacer">&nbsp;</div>

				<a href="?travel_plan"><span class="glyphicon glyphicon-plane icon-large">
					<div class="icon-large-title">Look your travel plans</div>
				</span></a>
				
				<div class="bigspacer">&nbsp;</div>

				<a href="?product_request"><span class="glyphicon glyphicon-briefcase icon-large">
					<div class="icon-large-title">Look your product requests</div>
				</span></a>
				
				<div class="bigspacer">&nbsp;</div>

				<a href="?messages"><span class="glyphicon glyphicon-comment icon-large">
					<div class="icon-large-title">Look your messages</div>
				</span></a>
				
				<div class="bigspacer">&nbsp;</div>

				<a href="?wall"><span class="glyphicon glyphicon-bell icon-large">{$badge}
					<div class="icon-large-title">{$not}</div>
				</span>
				
				<div class="bigspacer">&nbsp;</div></a>

				<div class="glyphicon glyphicon-menu-hamburger icon-large" tabindex="1">
					<div class="icon-large-menu">
						<ul>
EOF;



$menu = array(
	"1" => "Menu",
	"wall" => "Notifications {$badge}",
	"2" => "Travel plan",
	"travel_plan/add" => "<span class='glyphicon glyphicon-plus'></span> Create travel plan",
	"travel_plan" => "My travel plans",
	"travel_plan" => "Search travel plan",
	"q=search&showall" => "Show all travel plans",
	"3" => "Product requests",
	"product_request/add" => "<span class='glyphicon glyphicon-plus'></span> Make product request",
	"product_request" => "My product requests",
	"travel_plan" => "Search product request",
	"q=search_requesters&showall" => "Show all product requests",
	"pending/view/pending" => "Show pending requests",
	"pending/view/accepted" => "Show accepted requests",
	"4" => "&nbsp;",
	"messages" => "Show messages",
	"5" => "&nbsp;",
	"logout" => "Log out"
);

foreach($menu as $k => $v){
	if(strlen($k) < 4){
		$HTML[] = <<<EOF
			<li class="menu-right-title">{$v}
EOF;
	} else {
		$HTML[] = <<<EOF
			<li><a href="?{$k}">{$v}</a>
EOF;
	}
}

	$HTML[] = <<<EOF
						</ul>
					</div>
				</div>
			</div>



			<!--div class="search">
				<form action="" method="GET">
					<input type="hidden" name="q" value="search">
					<input type="text" name="from" id="searchfrom" value="{$from}" placeholder="Departure" class="form-control form-search">
					<input type="text" name="to" id="searchto" value="{$to}" placeholder="Arrival" class="form-control form-search">
					<div class="search-button btn btn-primary" id="sb" onclick="$('#sbclick').click()"><img src="css/search.png" width="20"></div>
					<input type="submit" name="submit" id="sbclick" value="Search" style="display: none">
				</form>
			</div-->
		</div>

<script>
$( "#searchfrom" ).autocomplete({
	source: "ajax/existing_cities.php",
	minLength: 1
});
$( "#searchto" ).autocomplete({
	source: "ajax/existing_cities.php",
	minLength: 1
});
</script>

		<div class="content">
			<div class="inner">
EOF;

if(isset($_SESSION['SUCCESS']) && !empty($_SESSION['SUCCESS'])){
	$HTML[] = '<div class="alert alert-success" role="alert">'
	 . implode("<li>", $_SESSION['SUCCESS']) . 
	 '</div>';
}

if(isset($_SESSION['ERROR']) && !empty($_SESSION['ERROR'])){
	$HTML[] = '<div class="alert alert-danger" role="alert">'
	 . implode("<li>", $_SESSION['ERROR']) . 
	 '</div>';
}
