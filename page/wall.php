<?php

$HTML[] = "<h1>Notifications</h1>";

$walls = $db->walls;

$messages = array();

foreach($walls->find(array("user1" => $_SESSION['user']))->sort(array("update" => -1)) as $k => $v){
	$v['user1'] = "" . $v['user1'];
	$v['user2'] = "" . $v['user2'];
	$key = $v['user1'] . $v['user2'] . $v['event'];
	if(isset($messages[$key])){
		$messages[$key]['count']++;
	} else {
		$messages[$key] = array(
				"_id" => $k,
				"value" => $v,
				"count" => 1
			);
	}
}

foreach($messages as $k => $v){
	$tmp = $v['value'];
	$d = "";
	$fellow = $tmp['user2'];
	//print_r($fellow);
	$fellow_name = "";
	if($fellow > 0){
		$fellow_name = generateUserLink($fellow);
	}
	if($tmp['event'] == "newrequestchat"){
		$d = "You have new incoming messages from <a href='{$tmp['page']}'>traveling plan</a> by {$fellow_name}.";
	}
	if($tmp['event'] == "requestaccepted"){
		$d = "Your request has been accepted under <a href='{$tmp['page']}'>traveling plan</a> by {$fellow_name}.";
	}
	if($tmp['event'] == "requestrejected"){
		$d = "Your request has been rejected under <a href='{$tmp['page']}'>traveling plan</a> by {$fellow_name}.";
	}
	if($tmp['event'] == "requestacceptedadmin"){
		$d = "You have accepted request by {$fellow_name} under <a href='{$tmp['page']}'>traveling plan</a>.";
	}
	if($tmp['event'] == "requestrejectedadmin"){
		$d = "You have rejected request by {$fellow_name} under <a href='{$tmp['page']}'>traveling plan</a>.";
	}
	if($tmp['event'] == "travelplanadded"){
		$d = "You have added new <a href='{$tmp['page']}{$tmp['user2']}'>travel plan.</a>";
	}
	if($tmp['event'] == "newrequest"){
		$d = "You have new request pending under <a href='{$tmp['page']}'>travel plan</a> by {$fellow_name}.";
	}
	if($tmp['event'] == "newrequestrevoke"){
		$d = "User {$fellow_name} has revoked pending request from <a href='{$tmp['page']}'>travel plan</a>.";
	}
	if($tmp['event'] == "newrequestclient"){
		$d = "You have made new request under <a href='{$tmp['page']}'>travel plan</a> by {$fellow_name}.";
	}
	if($tmp['event'] == "newrequestrevokeclient"){
		$d = "You have revoked pending request from <a href='{$tmp['page']}'>travel plan</a> by {$fellow_name}.";
	}
	if($tmp['event'] == "travelplandeleted"){
		$d = "You have deleted travel plan.";
	}
	if(empty($d)){
		echo $tmp['event'] . "<br>";
	}
	$t = relativeTime($tmp['update']);
	$badge = $v['count'] > 1 ? ' &middot; <div class="badge">' . $v['count'] . '</div>' : '';
	if(!empty($d)){
		$HTML[] = <<<EOF
			<div class="wall-event">
			{$d}
			<br>
			<small>{$t}</small>{$badge}
			</div>
EOF;
	}
}

$notifications = $db->notifications;
$n = $notifications->findOne(array("user" => $_SESSION['user']));

if(isset($n['count'])){
	$notifications -> update(
		array("user" => $_SESSION['user']),
		array("user" => $_SESSION['user'], "count" => 0)
	);
}
