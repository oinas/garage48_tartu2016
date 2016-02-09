<?php

$chats = $db->chats;
$requests = $db->requests;
$travel_plans = $db->travel_plans;

/** check if we have already made a request, then update the same entry only */
try {
	$request = $requests->findOne(array("travel" => $ID, "user" => $_SESSION['user']));
	$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
} catch (Exception $e){
	header("Location: ?product_request");
}
// chat removal
if(isset($_GET['remove'])){
	try {
		$chats -> remove(
			array("_id" => new MongoId($_GET['remove']))
		);
	} catch (Exception $e){
	}
	header("Location: ?product_request/{$ACTION}/{$ID}");
	die();
}

if(isset($_GET['accept'])){
	try {
		$tmp = $requests->findOne(array("travel" => $ID, "user" => $_GET['accept']));
	} catch (Exception $e){
		header("Location: ?product_request/{$ACTION}/{$ID}");
		die();
	}
	$tmp['status'] = 1;
	$requests -> update(
			array("travel" => $ID, "user" => $_GET['accept']),
			$tmp
		);
	wallPost($_GET['accept'], $_SESSION['user'], "requestaccepted", "?product_request/{$ACTION}/{$ID}");
	wallPost($_SESSION['user'], $_GET['accept'], "requestacceptedadmin", "?product_request/{$ACTION}/{$ID}");
	//header("Location: ?product_request/{$ACTION}/{$ID}");	
}
if(isset($_GET['reject'])){
	try {
		$tmp = $requests->findOne(array("travel" => $ID, "user" => $_GET['reject']));
	} catch (Exception $e){
		header("Location: ?product_request/{$ACTION}/{$ID}");
		die();
	}
	$tmp['status'] = 2;
	$requests -> update(
			array("travel" => $ID, "user" => $_GET['reject']),
			$tmp
		);
	wallPost($_GET['reject'], $_SESSION['user'], "requestrejected", "?product_request/{$ACTION}/{$ID}");
	wallPost($_SESSION['user'], $_GET['reject'], "requestrejectedadmin", "?product_request/{$ACTION}/{$ID}");
	//header("Location: ?product_request/{$ACTION}/{$ID}");	
}

//die();
if(isset($_GET['revoke'])){
	// delete request
	try {
		$requests -> remove(array("travel" => $ID, "user" => $_SESSION['user']));
	} catch (Exception $e){
	}
	header("Location: ?product_request/{$ACTION}/{$ID}");	
	$wall_post_revoke = true;
}

if(isset($_POST['submit']) && $_POST['submit'] == "Send message"){
	if(!isset($_POST['chat']) || strlen($_POST['chat']) < 1 || empty($_POST['chat'])){
		if($_POST['role'] == "requester"){
			header("Location: ?product_request/{$ACTION}/{$ID}/#chat-box");	
		} else {
			header("Location: ?product_request/{$ACTION}/{$ID}/#chat-box{$_POST['pos']}");	
		}
		die();
	}
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
		wallPost($entry['user'], $_SESSION['user'], "newrequestchat", "?product_request/{$ACTION}/{$ID}/#chat");
		header("Location: ?product_request/{$ACTION}/{$ID}/#chat");	
	} else {
		$chats -> insert(
				array(
					"travel" => $ID,
					"user1" => $_POST['user1'],
					"user2" => $entry['user'],
					"which" => 1,	// which user
					"message" => $_POST['chat'],
					"date" => date("Y-m-d H:i:s"),
					"update" => microtime(true)
				)
			);
		wallPost($_POST['user1'], $entry['user'], "newrequestchat", "?product_request/{$ACTION}/{$ID}/#chat");
		header("Location: ?product_request/{$ACTION}/{$ID}/#chat{$_POST['pos']}");	
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
		header("Location: ?product_request/{$ACTION}/{$ID}");
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
		header("Location: ?product_request/{$ACTION}/{$ID}");
	}
	uploadFile("picture", $ID . $_SESSION['user']);
	$wall_post_insert = true;
}

$when = dateToRelative($entry['date']);
$changed = relativeTime($entry['update']);

$tmp = array();

if(isset($wall_post_insert)){
	wallPost($entry['user'], $_SESSION['user'], "newrequest", "?product_request/{$ACTION}/{$ID}");
	wallPost($_SESSION['user'], $entry['user'], "newrequestclient", "?product_request/{$ACTION}/{$ID}");
}
if(isset($wall_post_revoke)){
	wallPost($entry['user'], $_SESSION['user'], "newrequestrevoke", "?product_request/{$ACTION}/{$ID}");
	wallPost($_SESSION['user'], $entry['user'], "newrequestrevokeclient", "?product_request/{$ACTION}/{$ID}");
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
$entry['date'] = convertDate($entry['date']);
if(isset($entry['requester'])){
	$HTML[] = <<<EOF

<a href="#" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left"></span> Back to search</a>
<h1 class="travel-plan-h1">Product request
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
		<td>Posted by
		<td>{$usertmp}
	</tr>
	<tr>
		<td>From
		<td>{$entry['from']}
	</tr>
	<tr>
		<td>To
		<td>{$entry['to']}
	</tr>
	<tr>
		<td>Request ends
		<td>{$entry['date']} - {$when}
	</tr>
	<tr>
		<td>Information about product(s)
		<td>{$entry['description']}
	</tr>
</table>

</div>

EOF;
} else {
	$HTML[] = <<<EOF
<a href="#" onclick="history.go(-1)"><span class="glyphicon glyphicon-chevron-left"></span> Back to search</a>
<h1>
Travel plan
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
		<td>Additional information
		<td>{$entry['description']}
	</tr>
</table>

</div>
EOF;
}

if($_SESSION['user'] != $entry['user']){
	if(empty($request)){
		if(isset($entry['requester'])){
			/** only if we have not sent request */
			$HTML[] = <<<EOF
			<div class="content-box medium">
			<h1>Make a request to bring the product?</h1>
EOF;
			formHeader("");
			formField("Message", "description", "textarea", "Hey, I saw your product request, could you please tell me more about it!");
			formFooter("Send a message");
			$HTML[] = <<<EOF
			</div>
EOF;
		} else {
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
		}
	} else {
		$tmp = $request['description'];
		if(file_exists("upload/" . $ID . $_SESSION['user'])){
			$tmp .= " <img class='right' width='200' src='upload/" . $ID . $_SESSION['user'] . "'>";
		}
		$t = isset($entry['requester']) ? "Posted by" : "Requested by";
		$tmp .= "<br><small>" . $t . " " . generateUserLink($request['user']) . " @ " . relativeTime($request['update']) . "</small>";

		if($request['status'] == 0){
			$HTML[] = <<<EOF
			<div class="content-box medium yellowback">
			<h1>Request is pending</h1>
EOF;

			if(isset($entry['requester'])){
			$HTML[] = <<<EOF
				Please wait while product requester will see your notifications and answers to you!<br><br>
			<div class="btn btn-warning"><a href="?product_request/{$ACTION}/{$ID}/&revoke">Revoke request</a>
EOF;
			} else {
			$HTML[] = <<<EOF
				Traveler is interested in your product request!<br><br>			
				<div class="btn btn-success"><a href="?product_request/{$ACTION}/{$ID}/&accept={$_SESSION['user']}">Accept request</a></div>
				<div class="btn btn-warning"><a href="?product_request/{$ACTION}/{$ID}/&reject={$_SESSION['user']}">Reject request</a></div>

EOF;
			}
			$HTML[] = <<<EOF
			</div>
			<br><br>
			{$tmp}
			<div style="clear: both;">
			</div>
EOF;
		} else if($request['status'] == 1){
			$ONLYCHAT = count($HTML);
				$t = "You have accepted travel plan";
			if(isset($entry['requester'])){
				$t = "Your travel plan has been accepted";
			}
			$HTML[] = <<<EOF
			<div class="content-box medium">
			<h1>{$t}</h1>
			{$tmp}
			<div id="chat-box">

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
				$id = "" . $v['_id'];
				$time = relativeTime($v['update']);
				if($v['which'] == 1 || !isset($entry['requester'])){
					$chatremove = "";
				} else {
					$chatremove = <<<EOF
						<div class="request-chat-remove">
							<a href="?product_request/{$ACTION}/{$ID}/&remove={$id}" title="Remove message"><span class="glyphicon glyphicon-remove"></span></a>
						</div>
EOF;
				}
				$HTML[] = <<<EOF
					<div class="request-chat">
					{$chatremove}
					{$pic}
					{$v['message']}<br>
					<small>{$time}</small>
					</div>
EOF;
			}

			if(isset($_GET['onlychat'])){
				for($i = $ONLYCHAT + 1; $i < count($HTML); $i++){
					echo $HTML[$i];
				}
				die();
			}

			$HTML[] = '</div><a name="chat"></a>';
			formHeader("");
			formField("", "role", "hidden", "requester");
			$HTML[] = <<<EOF

EOF;
			formField("", "chat", "textarea");
			formFooter("Send message");
			$HTML[] = <<<EOF
			<script>
				setInterval(function(){
				      $('#chat-box').load('?product_request/view/{$ID}/&onlychat');
				 }, 60000);
			</script>
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

	$l = 0;
	foreach($request as $k => $v){
		$l++;
		$tmp = $v['description'];
		if(file_exists("upload/" . $ID . $v['user'])){
			$tmp .= " <img class='right' width='200' src='upload/" . $ID . $v['user'] . "'>";
		}
		$t = isset($v['requester']) ? "Posted by" : "Requested by";
		$tmp .= "<br><small>" . $t . " " . generateUserLink($v['user']) . " @ " . relativeTime($v['update']) . "</small>";

		if($v['status'] == 0){
			$HTML[] = <<<EOF
			<div class="content-box medium yellowback">
			<h1>Request is pending</h1>
EOF;
			if(isset($entry['requester'])){
			$HTML[] = <<<EOF

				You have pending request from traveler to be accepted!<br><br>

				<div class="btn btn-success"><a href="?product_request/{$ACTION}/{$ID}/&accept={$v['user']}">Accept request</a></div>
				<div class="btn btn-warning"><a href="?product_request/{$ACTION}/{$ID}/&reject={$v['user']}">Reject request</a></div>

EOF;
			} else {
			$HTML[] = <<<EOF
				You need to wait product requester to accept your request!<br><br>
				<div class="btn btn-warning"><a href="?product_request/{$ACTION}/{$ID}/&revoke">Revoke request</a></div>

EOF;
			}
			$HTML[] = <<<EOF
			<br><br>
			{$tmp}
			<div style="clear: both;"></div>
			</div>
EOF;
		} else if($v['status'] == 1){
				$t = "Your request has been accepted";
			if(isset($entry['requester'])){
				$t = "You have accepted following request";
			}
			$HTML[] = <<<EOF
			<div class="content-box medium">
			<h1>{$t}</h1>
			{$tmp}
			<div id="chat-box{$k}">

EOF;
			$ONLYCHAT = count($HTML);
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
				$id = "" . $_v['_id'];

				if($_v['which'] == 1 || !isset($entry['requester'])){
					$chatremove = <<<EOF
						<div class="request-chat-remove">
							<a href="?product_request/{$ACTION}/{$ID}/&remove={$id}" title="Remove message"><span class="glyphicon glyphicon-remove"></span></a>
						</div>
EOF;
				} else {
					$chatremove = "";
				}

				$HTML[] = <<<EOF
					<div class="request-chat">
						<div class="request-chat-remove">
							{$chatremove}
						</div>
					{$pic}
					{$_v['message']}<br>
					<small>{$time}</small>
					</div>
EOF;
			}

			if(isset($_GET['onlychat' . $k])){
				for($i = $ONLYCHAT; $i < count($HTML); $i++){
					echo $HTML[$i];
				}
				die();
			}
			$HTML[] = '</div><a name="chat' . $k . '"></a>';
			formHeader("");
			formField("", "user1", "hidden", "{$v['user']}");
			formField("", "pos", "hidden", "{$k}");
			formField("", "role", "hidden", "traveler");
			formField("", "chat", "textarea");
			formFooter("Send message");
			$HTML[] = <<<EOF
			<script>
				setInterval(function(){
				      $('#chat-box{$k}').load('?product_request/view/{$ID}/&onlychat{$k}');
				 }, 60000);
			</script>
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
