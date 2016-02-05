<?php

if(!isset($_GET['accessToken'])){
	$_GET['accessToken'] = "";
}
if(!isset($_GET['table'])){
	$_GET['table'] = "";
}

if($_GET['accessToken'] != "cico"){
	echo "Invalid access token\n";
	die();
}

$m = new MongoClient();

// select a database
$db = $m->cico;

if($_GET['table'] == "users"){
	$entries = $db->users;
} else if($_GET['table'] == "travel_plans"){
	$entries = $db->travel_plans;
} else if($_GET['table'] == "chats"){
	$entries = $db->chats;
} else if($_GET['table'] == "requests"){
	$entries = $db->requests;
} else if($_GET['table'] == "notifications"){
	$entries = $db->notifications;
} 

$res = array();	

foreach($entries->find() as $k => $v){
	$res[] = $v;
}
echo json_encode($res);