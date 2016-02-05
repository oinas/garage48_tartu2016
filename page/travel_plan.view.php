<?php

$chats = $db->chats;
$requests = $db->requests;
$travel_plans = $db->travel_plans;

/** check if we have already made a request, then update the same entry only */
$request = $requests->findOne(array("travel" => $ID, "user" => $_SESSION['user']));
$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));

if(isset($_GET['accept'])){
	$tmp = $requests->findOne(array("travel" => $ID, "user" => $_GET['accept']));
	$tmp['status'] = 1;
	$requests -> update(
			array("travel" => $ID, "user" => $_GET['accept']),
			$tmp
		);
}
if(isset($_GET['reject'])){
	$tmp = $requests->findOne(array("travel" => $ID, "user" => $_GET['reject']));
	$tmp['status'] = 2;
	$requests -> update(
			array("travel" => $ID, "user" => $_GET['reject']),
			$tmp
		);
}

if(isset($_GET['revoke'])){
	// delete request
	$requests -> remove(array("travel" => $ID, "user" => $_SESSION['user']));
	header("Location: ?travel_plan/{$ACTION}/{$ID}");	
	$wall_post_revoke = true;
}

if(isset($_POST['submit']) && $_POST['submit'] == "Send message"){
	if($_POST['role'] == "requester"){
		$chats -> insert(
				array(
					"travel" => $ID,
					"user1" => $_SESSION['user'],
					"user2" => $entry['user'],
					"which" => 0,	// which user
					"message" => $_POST['chat'],
					"date" => date("Y-m-d H:i:s"),
					"update" => microtime(true)
				)
			);
		wallPost($entry['user'], $_SESSION['user'], "newrequestchat", "?travel_plan/{$ACTION}/{$ID}/#chat");
		header("Location: ?travel_plan/{$ACTION}/{$ID}/#chat");	
	} else {
		$chats -> insert(
				array(
					"travel" => $ID,
					"user1" => $request['user'],
					"user2" => $entry['user'],
					"which" => 1,	// which user
					"message" => $_POST['chat'],
					"date" => date("Y-m-d H:i:s"),
					"update" => microtime(true)
				)
			);
		wallPost($entry['user'], $_SESSION['user'], "newrequestchat", "?travel_plan/{$ACTION}/{$ID}/#chat");
		header("Location: ?travel_plan/{$ACTION}/{$ID}/#chat{$_POST['pos']}");	
	}
	die();
}

if(isset($_POST['submit'])){
	$_POST['update'] = microtime(true);
	$_POST['travel'] = $ID;
	$_POST['user'] = $_SESSION['user'];
	$_POST['status'] = 0;
	if(empty($request)){
		$_POST['added'] = microtime(true);
		$requests -> insert($_POST);
		header("Location: ?travel_plan/{$ACTION}/{$ID}");
	} else {
		foreach($request as $k => $v){
			if(!isset($_POST[$k])){
				$_POST[$k] = $v;
			}
		}
		$requests -> update(
			array("travel" => $ID, "user" => $_SESSION['user']),
			$_POST
			);
		header("Location: ?travel_plan/{$ACTION}/{$ID}");
	}
	uploadFile("picture", $ID . $_SESSION['user']);
	$wall_post_insert = true;
}

$when = dateToRelative($entry['date']);
$changed = relativeTime($entry['update']);

$tmp = array();

if(isset($wall_post_insert)){
	wallPost($entry['user'], $_SESSION['user'], "newrequest", "?travel_plan/{$ACTION}/{$ID}");
}
if(isset($wall_post_revoke)){
	wallPost($entry['user'], $_SESSION['user'], "newrequestrevoke", "?travel_plan/{$ACTION}/{$ID}");
}

if(isset($entry['size']) && !empty($entry['size'])){
	$tmp[] = "Maximum allowed size: {$entry['size']}<br>";
}
if(isset($entry['weight']) && !empty($entry['weight'])){
	$tmp[] = "Maximum allowed weight: {$entry['weight']}<br>";
}
if(isset($entry['handluggage']) && !empty($entry['handluggage'])){
	$tmp[] = "Items can be transfered only in hand luggage<br>";
}
if(empty($tmp)){
	$tmp = "No details added";
} else {
	$tmp = "<ul><li>" . implode("<li>", $tmp) . "</ul>";
}
$usertmp = generateUserPicture($entry['user'], "chat-picture") . " " . generateUserLink($entry['user']);
$HTML[] = <<<EOF
<a href="#" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left"></span> Back</a>
<h1>
<a href="https://maps.google.com/?q={$entry['from']}" target="_blank">{$entry['from']}</a> 
<small><span class="glyphicon glyphicon-chevron-right"></span></small>
<a href="https://maps.google.com/?q={$entry['to']}" target="_blank">{$entry['to']}</a> 
</h1>
<div class="content-box">

<table class="table">
	<tr>
		<th colspan="2"><span class="glyphicon glyphicon-exclamation-sign"></span> Information
	</tr>
	<tr>
		<td>Posted
		<td>{$usertmp}
	</tr>
	<tr>
		<td>Departure
		<td>{$entry['from']}
	</tr>
	<tr>
		<td>Arrival
		<td>{$entry['to']}
	</tr>
	<tr>
		<td>Departure date
		<td>{$entry['date']} - {$when}
	</tr>
	<tr>
		<td>Entry added/changed
		<td>{$changed}
	</tr>
	<tr>
		<td>Details
		<td>{$tmp}
	</tr>
	<tr>
		<td>Additional information
		<td>{$entry['description']}
	</tr>
</table>

</div>
EOF;


if($_SESSION['user'] != $entry['user']){
	if(empty($request)){
		/** only if we have not sent request */
		$HTML[] = <<<EOF
		<div class="content-box medium">
		<h1>Make a request to a traveler?</h1>
EOF;
		formHeader("");
		formField("Request description", "description", "textarea", "Hey, I saw your traveling plan, could you please fetch me ");
		formField("You can add picture of the item", "picture", "file");
		formFooter("Make a request");
		$HTML[] = <<<EOF
		</div>
EOF;
	} else {
		$tmp = $request['description'];
		if(file_exists("upload/" . $ID . $_SESSION['user'])){
			$tmp .= " <img class='right' width='200' src='upload/" . $ID . $_SESSION['user'] . "'>";
		}
		$tmp .= "<br><small>Posted by " . generateUserLink($request['user']) . " @ " . relativeTime($request['update']) . "</small>";

		if($request['status'] == 0){
			$HTML[] = <<<EOF
			<div class="content-box medium yellowback">
			<h1>Your request is pending</h1>
			Please wait while traveler will see your notifications and answers to you!<br><br>
			<div class="btn btn-warning"><a href="?travel_plan/{$ACTION}/{$ID}/&revoke">Revoke request</a>
			</div>
			<br><br>
			{$tmp}
			<div style="clear: both;">
			</div>
EOF;
		} else if($request['status'] == 1){
			$HTML[] = <<<EOF
			<div class="content-box medium">
			<h1>Your request has been accepted</h1>
			{$tmp}

EOF;
			foreach($chats->find(array(	"travel" => $ID,
								"user1" => $_SESSION['user'],
								"user2" => $entry['user']))
							->sort(array( "update" => 1)) as $k => $v){
				if($v['which'] == 0){
					$pic = generateUserPicture($v['user1'], "chat-picture");
				} else {
					$pic = generateUserPicture($v['user2'], "chat-picture");
				}
				$time = relativeTime($v['update']);
				$HTML[] = <<<EOF
					<div class="request-chat">
					{$pic}
					{$v['message']}<br>
					<small>{$time}</small>
					</div>
EOF;
			}
			$HTML[] = '<a name="chat"></a>';
			formHeader("");
			formField("", "role", "hidden", "requester");
			formField("", "chat", "textarea");
			formFooter("Send message");
			$HTML[] = <<<EOF
			</div>
EOF;
		} else {
			$HTML[] = <<<EOF
			<div class="content-box medium redback">
			<h1>Your request has been rejected</h1>
			You cannot make new request to this traveling plan!<br><br>
			{$tmp}
			<div style="clear: both;">
			</div>
EOF;
		}
	}
} else {
	/** show requests */
	$request = $requests->find(array("travel" => $ID));

	foreach($request as $k => $v){
		$tmp = $v['description'];
		if(file_exists("upload/" . $ID . $v['user'])){
			$tmp .= " <img class='right' width='200' src='upload/" . $ID . $v['user'] . "'>";
		}
		$tmp .= "<br><small>Posted by " . generateUserLink($v['user']) . " @ " . relativeTime($v['update']) . "</small>";

		if($v['status'] == 0){
			$HTML[] = <<<EOF
			<div class="content-box medium yellowback">
			<h1>Request is pending</h1>
			Please wait while traveler will see your notifications and answers to you!<br><br>
			<div class="btn btn-success"><a href="?travel_plan/{$ACTION}/{$ID}/&accept={$v['user']}">Accept request</a></div>
			<div class="btn btn-warning"><a href="?travel_plan/{$ACTION}/{$ID}/&reject={$v['user']}">Reject request</a></div>
			<br><br>
			{$tmp}
			<div style="clear: both;">
			</div>
EOF;
		} else if($v['status'] == 1){
			$HTML[] = <<<EOF
			<div class="content-box medium">
			<h1>You have accepted following request</h1>
			{$tmp}

EOF;
			foreach($chats->find(array(	"travel" => $ID,
								"user1" => $v['user'],
								"user2" => $entry['user']))
							->sort(array( "update" => 1)) as $_k => $_v){
				if($_v['which'] == 0){
					$pic = generateUserPicture($_v['user1'], "chat-picture");
				} else {
					$pic = generateUserPicture($_v['user2'], "chat-picture");
				}
				$time = relativeTime($_v['update']);
				$HTML[] = <<<EOF
					<div class="request-chat">
					{$pic}
					{$_v['message']}<br>
					<small>{$time}</small>
					</div>
EOF;
			}
			$HTML[] = '<a name="chat{$k}"></a>';
			formHeader("");
			formField("", "pos", "hidden", "{$k}");
			formField("", "role", "hidden", "traveler");
			formField("", "chat", "textarea");
			formFooter("Send message");
			$HTML[] = <<<EOF
			</div>
EOF;
		} else {
			/*$HTML[] = <<<EOF
			<div class="content-box medium redback">
				<h1>Request has been rejected</h1>
			</div>
EOF;*/
		}
	}
}



/*
$HTML[] = <<<EOF
<table class="table table-hover table-striped">
EOF;
$allowed = array("from", "to", "date", "size", "weight", "description", "update", "handluggage");
$entry['update'] = relativeTime($entry['update']);
showValues($entry, $allowed);
$HTML[] = <<<EOF
</table>
EOF;
*/
