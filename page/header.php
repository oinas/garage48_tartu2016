<?php

$to = "";
$from = "";
if(isset($_GET['to'])){
	$to = $_GET['to'];
}
if(isset($_GET['from'])){
	$from = $_GET['from'];
}

$HTML[] = <<<EOF
<!DOCTYPE html> 
<html lang=en>
	<head>
		<title>{$TITLE} &middot; {$PAGE}</title>
		<meta charset="utf-8" />
		<base href="{$BASEHREF}" />
		<link rel="shortcut icon" href="favicon.ico" />
		<script src="https://code.jquery.com/jquery-2.2.0.min.js"></script>
		<script src="https://code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
		<script src="http://tablesorter.com/__jquery.tablesorter.min.js"></script> 
		<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
<!-- FACEBOOK START -->
		<meta property="og:title" content="{$PAGE}" />
		<meta property="og:site_name" content="{$TITLE}" />
		<meta property="og:type" content="website" />
		<meta property="og:description" content="DESCRIPTION GOES HERE" />
		<meta property="og:url" content="{$URL}" />
<!-- FACEBOOK END -->
		<meta name="description" content="DESCRIPTION GOES HERE" />
		<link rel="stylesheet" type="text/css" href="css/main.css" />
		<link rel="stylesheet" media="(max-width: 800px)" href="css/mobile.css" />
		<link href='https://fonts.googleapis.com/css?family=Raleway' rel='stylesheet' type='text/css' />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
	</head>
	<body>

	<div class="body">
		<div class="header">
			<img src="css/logo.png" class="header-logo">


			

			<div class="menu">
				<ul class="menu-ul">
					<li><a href="?concept">Concept</a>
					<li><a href="?about-us">About Us</a>
					<li><a href="?logout">Log out</a>
				</ul>
			</div>



			<div class="search">
				<form action="" method="GET">
					<input type="hidden" name="q" value="search">
					<input type="text" name="from" id="searchfrom" value="{$from}" placeholder="Departure" class="form-control form-search">
					<input type="text" name="to" id="searchto" value="{$to}" placeholder="Arrival" class="form-control form-search">
					<div class="search-button btn btn-primary" id="sb" onclick="$('#sbclick').click()"><img src="css/search.png" width="20"></div>
					<input type="submit" name="submit" id="sbclick" value="Search" style="display: none">
				</form>
			</div>
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
