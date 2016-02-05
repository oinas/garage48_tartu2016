<?php

$m = new MongoClient();

// select a database
$db = $m->cico;

$pictures = array();
$names = array();
$emails = array();
function getUserPicture($userid){
	global $pictures;
	if(!isset($pictures[$userid])){
		$json = file_get_contents("https://graph.facebook.com/" . $userid . "/picture?type=large&redirect=0");
		$tmp = json_decode($json);
		$pictures[$userid] = $tmp->data->url;		
	}
	return $pictures[$userid];
}

function getUser($userid){
	global $db;

	$user = $db->users;

	$entry = $user->findOne(array("facebookid" => $userid));

	return !empty($entry) ? $entry : null;
}

function getUserName($userid){
	global $names;
	if(!isset($names[$userid])){
		$names[$userid] = getUser($userid)['first'];
	}
	return $names[$userid];
}

function getUserEmail($userid){
	global $emails;
	if(!isset($names[$userid])){
		$emails[$userid] = getUser($userid)['email'];
	}
	return $emails[$userid];
}


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
} else if($_GET['table'] == "wall"){
	$entries = $db->walls;
} 

$res = array();	
if(!empty($entries))
foreach($entries->find() as $k => $v){
	$m = array();
	foreach($v as $_k => $_v){
		if(preg_match("/user/", $_k)){
			if($_GET['table'] == "users"){
				$_v = $v['facebookid'];
			}
			if((int) $_v != $_v || strlen("" . ((int) $_v)) != strlen($_v)){
				continue;
			}
//			$m[$_k . "_picture"] = getUserPicture($_v);
			$m[$_k . "_name"] = getUserName($_v);
			$m[$_k . "_email"] = getUserEmail($_v);
		}
	}
	foreach($m as $_k => $_v){
		$v[$_k] = $_v;
	}
	$res[] = $v;
}
echo json_encode($res);