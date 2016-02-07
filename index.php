<?php

/**
	Preliminary entry point for Garage48 hackathon. Change files in core/ folder.
*/

$start = microtime(true);

require_once "config.php";

$PAGE_NAME = "";

if(isset($_GET['accept-cookies'])){
	$_SESSION['cookies'] = true;
}

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
	$ALLOWED_PAGES = array("about-us", "login", "signup", "concept", "terms", "", "how-it-works", "feedback", "video");
	if(!in_array($PAGE, $ALLOWED_PAGES)){
		$PAGE = "403";
		// redirect logged out sessions
		header("Location: {$BASEHREF}?");
		die();
	}
	$PAGES[] = "front_header";
	$PAGES[] = empty($PAGE) ? $DEFAULT_FRONT_PAGE : $PAGE;
	$PAGES[] = "front_footer";
}

$page_names = array(
	"" => "Front page",
	"how-it-works" => "How CICO works",
	"feedback" => "Feedback",
	"concept" => "Concept",
	"video" => "Video",
	"terms" => "Terms of Use",
	"about-us" => "About Us",
	"front_search" => "Search",
	"users.view" => "User profile view",
	"profile.edit" => "User profile edit",
	"wall" => "Notifications",
	"travel_plan.edit" => $ACTION == "add" ? "Create a new travel plan" : "Edit existing travel plan",
	"product_request.edit" => $ACTION == "add" ? "Make a new product request" : "Edit existing product request",
	"travel_plan" => "List of travel plans",
	"search" => "Search travel plan",
	"pending.view" => "Accepted/Pending product requests",
	"product_request" => "List of product requests",
	"search_requesters" => "Search product requests",
	"planspending.view" => "Accepted/Pending travel plans",
	"messages" => "Last messages",
	"livefeedback" => "Give feedback"
);

if(isset($page_names[$PAGE])){
	$PAGE_NAME = " &middot; " . $page_names[$PAGE];
}

/** controller logic */
foreach($PAGES as $P){
	if(file_exists($CORE . $P . ".php")){
		require_once $CORE . $P . ".php";
	} else {
		require_once $CORE . "404.php";
	}
}

$HTML[] = <<<EOF
<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-73397182-1', 'auto');
  ga('send', 'pageview');

</script>
EOF;

/** display HTML content */
if(!isset($_GET['norender'])){
	echo implode("\n", $HTML);
}

if(isset($_SESSION['ERROR'])){
	unset($_SESSION['ERROR']);
	unset($_SESSION['POST']);
}

if(isset($_SESSION['SUCCESS'])){
	unset($_SESSION['SUCCESS']);
}

if(!empty($ERROR)){
	$_SESSION['POST'] = $_POST;
	$_SESSION['ERROR'] = $ERROR;
}

if(!empty($SUCCESS)){
	$_SESSION['SUCCESS'] = $SUCCESS;
}

$render_time = (int) ((microtime(true) - $start) * 1000);

echo "<!-- Everything rendered in {$render_time} ms -->";