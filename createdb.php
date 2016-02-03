<?php

$m = new MongoClient();

// select a database
$db = $m->cico;

// select collection
$entries = $db->users;


$entries->insert(
	array(
		"user" => "man",
		"password" => md5("test"),
		"first" => "Karl",
		"last" => "Pott",
		"email" => "karl.pott@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"last" => microtime(true),
		"status" => "0",
		"description" => "I travel a lot!",
		"banstatus" => 0
		)
	);
$entries->insert(
		array(
		"user" => "woman",
		"password" => md5("test"),
		"first" => "Mai",
		"last" => "Sang",
		"email" => "mai.sang@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"last" => microtime(true),
		"status" => "0",
		"description" => "I buy a lot!",
		"banstatus" => 0
		)
	);
$entries->insert(
		array(
		"user" => "traveler",
		"password" => md5("test"),
		"first" => "Marek",
		"last" => "Pall",
		"email" => "marek.pall@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"last" => microtime(true),
		"status" => "0",
		"description" => "I am traveler!",
		"banstatus" => 0
		)
	);
$entries->insert(
		array(
		"user" => "requester",
		"password" => md5("test"),
		"first" => "Kalle",
		"last" => "Tald",
		"email" => "kalle.tald@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"last" => microtime(true),
		"status" => "0",
		"description" => "I request a lot!",
		"banstatus" => 0
		)
	);


foreach($entries->find() as $k => $v){
	echo $k . " ";
	print_r($v);
}
