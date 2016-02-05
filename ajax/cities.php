<?php

if(!isset($_GET['term']) || empty($_GET['term'])){
	die();
}
$t = explode(" ", $_GET['term']);

$_GET['term'] = str_replace(" ", "%20", $_GET['term']);

$f = file_get_contents("https://api.prestaging.teleport.ee/api/cities/?search=" . $_GET['term']);

$matches = json_decode($f);

$list = array();

foreach($matches->_embedded->{'city:search-results'} as $k => $v){
	$_t = explode(", ", $v->matching_full_name);
	$v->matching_full_name = $_t[0] . ", " . $_t[2];
	if(preg_match("/^" . $t[0] . "/i", $v->matching_full_name)){
		$list[] = array("id" => $k, "value" => $v->matching_full_name);
	}
}

echo json_encode($list);
