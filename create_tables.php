<?php

/**
Hack to easily add new tables
*/

require_once "db.php";

/*$DB = new DB(array("table" => "users", "mode" => "createDB", 
"columns" => array("id", "user", "first", "last", "email", "facebookid", "googleid", "date", "update", "status", "description", "banstatus", "password")));*/

// access database
$DB = new DB(array("table" => "users"));
// update entry
$DB->updateEntry(array("id" => "2", "password" => md5("test")));
/*
// add entry to database
$DB->addEntry(
	array(
		"user" => "man",
		"first" => "Karl",
		"last" => "Pott",
		"email" => "karl.pott@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"update" => microtime(true),
		"status" => "0",
		"description" => "I travel a lot!",
		"banstatus" => 0
		)
	);

$DB->addEntry(
	array(
		"user" => "woman",
		"first" => "Mai",
		"last" => "Sang",
		"email" => "mai.sang@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"update" => microtime(true),
		"status" => "0",
		"description" => "I buy a lot!",
		"banstatus" => 0
		)
	);

$DB->addEntry(
	array(
		"user" => "traveler",
		"first" => "Marek",
		"last" => "Pall",
		"email" => "marek.pall@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"update" => microtime(true),
		"status" => "0",
		"description" => "I am traveler!",
		"banstatus" => 0
		)
	);

$DB->addEntry(
	array(
		"user" => "requester",
		"first" => "Kalle",
		"last" => "Tald",
		"email" => "kalle.tald@cico.com",
		"facebookid" => "",
		"googleid" => "",
		"date" => date("Y-m-d H:i:s"),
		"update" => microtime(true),
		"status" => "0",
		"description" => "I request a lot!",
		"banstatus" => 0
		)
	);
*/
