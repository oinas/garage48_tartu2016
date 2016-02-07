<?php

if(file_exists("../index2.html")){
	$css = "localmain.css";
} else {
	$css = "main.css";
}

$HTML[] = <<<EOF
<!DOCTYPE html> 
<html lang=en>
	<head>
		<title>{$TITLE} &middot; {$PAGE}</title>
		<meta charset="utf-8" />
		<base href="{$BASEHREF}" />
		<link rel="shortcut icon" href="favicon.ico?v1" />
		<link rel="stylesheet" href="css/bootstrap.min.css">
<!-- FACEBOOK START -->
		<meta property="og:title" content="CICO brings you what you desire" />
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
	<div class="front body">
		<div class="header">
			<a href="?"><img src="css/logo.png" class="header-logo"></a>
			<div class="menu">
				<ul class="menu-ul">
					<li><a href="?">Log in</a>
					<li><a href="?how-it-works">How It Works?</a>
					<li><a href="?feedback">Feedback</a>
					<li><a href="?concept">Concept</a>
					<li><a href="?video">Video</a>
					<li><a href="?terms">Terms of Use</a>
					<li><a href="?about-us">About Us</a>
				</ul>
			</div>
		</div>

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
