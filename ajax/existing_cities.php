<?php

/** mongoDB database */
$m = new MongoClient();

// select a database, use $db to access it
$db = $m->cico;

$travel_plans = $db->travel_plans;

$locations = array();

foreach($travel_plans->find(array()) as $k => $v){
	if(preg_match("/{$_GET['term']}/i", $v['to'])){
		$tmp = strtolower($v['to']);
		$locations[$tmp] = $v['to'];
	}
	if(preg_match("/{$_GET['term']}/i", $v['from'])){
		$tmp = strtolower($v['from']);
		$locations[$tmp] = $v['from'];
	}
}
echo json_encode($locations);
