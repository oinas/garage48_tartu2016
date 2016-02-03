<?php

/**
	Preliminary config for Garage48 hackathon. 
*/

session_start();

require_once "db.php";
require_once "functions.php";

$HTML = array();
$PAGES = array();
/** error handler */
$ERROR = array();
/** success handler */
$SUCCESS = array();
/** webpage title */
$TITLE = "BringMyStuff";
/** webpage location, so clean urls will work with images */
$BASEHREF = "/garage48/";

$CORE = "page/";

$DEFAULT_LOGGED_PAGE = "main";
$DEFAULT_FRONT_PAGE = "front_main";

$tmp = explode("/", isset($_GET['q']) ? $_GET['q'] : "");
$PAGE = isset($tmp[0]) ? $tmp[0] : null;
$ACTION = isset($tmp[1]) ? $tmp[1] : null;
$ID = isset($tmp[2]) ? $tmp[2] : null;

/**
Examples of $PAGE, $ACTION and $ID.

garage48/posts			- shows all the posts
garage48/post/view/3	- shows post with ID 3
garage48/post/edit/3	- edit post with ID 3
garage48/post/delete/3	- delete post with ID 3
garage48/post/add		- add new post
*/
