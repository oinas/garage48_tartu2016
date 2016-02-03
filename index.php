<?php

/**
	Preliminary entry point for Garage48 hackathon. Change files in core/ folder.
*/

require_once "config.php";

/** check if we have logged in, if not, show front page */
if(isset($_SESSION['user'])){
	if(!isset($_GET['onlycontent'])){
		$PAGES[] = "header";
	}
	$PAGES[] = empty($PAGE) ? $DEFAULT_LOGGED_PAGE : $PAGE;
	if(!isset($_GET['onlycontent'])){
		$PAGES[] = "footer";
	}
} else {
	$ALLOWED_PAGES = array("");
	$PAGES[] = "front_header";
	$PAGES[] = empty($PAGE) ? $DEFAULT_FRONT_PAGE : $PAGE;
	$PAGES[] = "front_footer";
}

/** controller logic */
foreach($PAGES as $P){
	if(file_exists($CORE . $P . ".php")){
		require_once $CORE . $P . ".php";
	} else {
		require_once $CORE . "404.php";
	}
}

/** display HTML content */
if(!isset($_GET['norender'])){
	echo implode("\n", $HTML);
}

if(isset($_SESSION['ERROR'])){
	unset($_SESSION['ERROR']);
}

if(isset($_SESSION['SUCCESS'])){
	unset($_SESSION['SUCCESS']);
}

if(!empty($ERROR)){
	$_SESSION['ERROR'] = $ERROR;
}


if(!empty($ERROR)){
	$_SESSION['SUCCESS'] = $SUCCESS;
}
