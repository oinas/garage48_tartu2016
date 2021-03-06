<?php

/**
	Preliminary config for Garage48 hackathon. 
*/

session_start();

/** mongoDB database */
$m = new MongoClient();

// select a database, use $db to access it
$db = $m->cico;

require_once "functions.php";

$HTML = array();
$PAGES = array();
/** error handler */
$ERROR = array();
/** success handler */
$SUCCESS = array();
/** webpage title */
$TITLE = "CICO";
/** webpage location, so clean urls will work with images */
if(file_exists("../index2.html")){
	$BASEHREF = "/garage48_tartu2016/";
	$URL = "http://cico.com";
} else {
	$BASEHREF = "/";
	$URL = "http://cico.northeurope.cloudapp.azure.com/";
}

$CORE = "page/";

/** default landing page for logged in user */
$DEFAULT_LOGGED_PAGE = "front_search";
/** default landing page for unlogged user */
$DEFAULT_FRONT_PAGE = "front_main";

$tmp = explode("/", isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : "");
$PAGE = isset($tmp[0]) ? $tmp[0] : null;
$ACTION = isset($tmp[1]) ? $tmp[1] : null;
$ID = isset($tmp[2]) ? $tmp[2] : null;

/** clean URLS, we can ignore it for time being? 
$tmp = explode("/", isset($_GET['q']) ? $_GET['q'] : "");
$PAGE = isset($tmp[0]) ? $tmp[0] : null;
$ACTION = isset($tmp[1]) ? $tmp[1] : null;
$ID = isset($tmp[2]) ? $tmp[2] : null;
*/

/**
Examples of $PAGE, $ACTION and $ID.

garage48/posts			- shows all the posts
garage48/post/view/3	- shows post with ID 3
garage48/post/edit/3	- edit post with ID 3
garage48/post/delete/3	- delete post with ID 3
garage48/post/add		- add new post
*/
