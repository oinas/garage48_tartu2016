<?php

/**
	Preliminary entry point for Garage48 hackathon. Change files in core/ folder.
*/

require_once "config.php";

// for DEMO purpose
if(isset($_GET['user']) && $_GET['password'] == "test"){
	$entries = $db->users;
	$entry = $entries->findOne(array("user" => $_GET['user']));
	if(!empty($entry)){
		$entry['last'] = microtime(true);
		$entries->update(
			array("user" => $entry['user']),
			$entry
		);
		$_SESSION['fb_access_token'] = $entry['facebookAccess'];
		$_SESSION['fb_id'] = $entry['facebookid'];
		$_SESSION['fb_name'] = $entry['first'];
		$_SESSION['fb_email'] = $entry['email'];
		$_SESSION['user'] = $entry['facebookid'];
		header("Location: {$BASEHREF}?front_search");
		die();
	} else {
		header("Location: {$BASEHREF}?");
		die();
	}
}

/** check if we have logged in, if not, show front page */
if(isset($_SESSION['user'])){
	/** upgrade user info */
	$entries = $db->users;
	$entry = $entries->findOne(array("facebookid" => $_SESSION['user']));
	if(isset($_GET['q'])){
		$PAGE = $_GET['q'] == "search" ? "search" : "search_requesters";
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
	$ALLOWED_PAGES = array("about-us", "login", "signup", "concept", "", "how-it-works", "feedback", "video");
	if(!in_array($PAGE, $ALLOWED_PAGES)){
		$PAGE = "403";
		// redirect logged out sessions
		header("Location: {$BASEHREF}?");
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
