<?php

/**
	Preliminary entry point for Garage48 hackathon. Change files in core/ folder.
*/

require_once "config.php";

/** check if we have logged in, if not, show front page */
if(isset($_SESSION['user'])){
	/** upgrade user info */
	$entries = $db->users;
	$entry = $entries->findOne(array("facebookid" => $_SESSION['user']));
	if(isset($_GET['q'])){
		$PAGE = "search";
	}

	if(!empty($entry)){
	  $entry['last'] = microtime(true);
	  /** update */
	  $entries->update(
	      array("facebookid" => $_SESSION['user']),
	      $entry
	    );
	}

	if(!isset($_GET['onlycontent'])){
		$PAGES[] = "header";
		$PAGES[] = "wall/leftside";
	}
	if(in_array($ACTION, array("new", "delete", "edit", "add"))){
		$PAGE .= ".edit";
	} else if($ACTION == "view"){
		$PAGE .= ".view";
	}
	$PAGES[] = empty($PAGE) ? $DEFAULT_LOGGED_PAGE : $PAGE;
	if(!isset($_GET['onlycontent'])){
		$PAGES[] = "wall/rightside";
		$PAGES[] = "footer";
	}
} else {
	/** simple hack to not allow user to access login section */
	$ALLOWED_PAGES = array("about-us", "login", "signup", "concept", "", "how-it-works");
	if(!in_array($PAGE, $ALLOWED_PAGES)){
		$PAGE = "403";
	}
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

if(!empty($SUCCESS)){
	$_SESSION['SUCCESS'] = $SUCCESS;
}
