<?php

$travel_plans = $db->travel_plans;

if($ACTION == "view"){
	$HTML[] = <<<EOF
		<h1>Travel plan</h1>
EOF;
	$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
	$HTML[] = <<<EOF
	<table class="table table-hover table-striped">
EOF;
	$allowed = array("from", "to", "date", "size", "weight", "description");
	showValues($entry, $allowed);
	$HTML[] = <<<EOF
	</table>
EOF;
