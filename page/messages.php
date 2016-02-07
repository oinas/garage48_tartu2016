<?php

$chats = $db->chats;

$HTML[] = <<<EOF
<h1>Latest messages under travel plans and product requests</h1>
EOF;

$i = 0;
foreach($chats->find()->sort(array("update" => -1)) as $k => $v){
	if($v['user1'] != $_SESSION['user'] && $v['user2'] != $_SESSION['user']){
		continue;
	}
	$i++;
	$travel_plans = $db->travel_plans;
	$travel = $travel_plans->findOne(array("_id" => new MongoId($v['travel'])));
	$tmp = "travel plan";
	if(isset($travel['requester'])){
		$tmp = "product request";
	}
	$date = convertDate($travel['date']);
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
		<small>{$time} &middot; <a href="?travel_plan/view/{$v['travel']}">go to {$tmp} @ {$travel['from']} &gt; {$travel['to']} ({$date})</a></small>
		</div>
EOF;
}

if($i == 0){
		$HTML[] = <<<EOF
		<div class="request-chat">
			No entries found
		</div>
EOF;
}