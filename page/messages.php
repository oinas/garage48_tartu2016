<?php

$chats = $db->chats;

$HTML[] = <<<EOF
<h1>Latest messages under travel plans</h1>
EOF;

foreach($chats->find()->sort(array("update" => -1)) as $k => $v){
	$travel_plans = $db->travel_plans;
	$travel = $travel_plans->findOne(array("_id" => new MongoId($v['travel'])));
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
		<small>{$time} &middot; <a href="?travel_plan/view/{$v['travel']}">go to travel plan @ {$travel['from']} &gt; {$travel['to']} ({$date})</a></small>
		</div>
EOF;
}

