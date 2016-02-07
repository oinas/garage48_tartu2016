<?php

$HTML[] = <<<EOF
	<h1>User feedback</h1>
EOF;

$feedback = $db->feedback;

foreach($feedback->find()->sort(array("date" => -1)) as $k => $v){
	$pic = generateUserPicture($v['user'], "chat-picture");
	$time = relativeTime($v['update']);
	$date = convertDateTime($v['date'], true);
	$HTML[] = <<<EOF
	<div class="request-chat">
		{$pic}
		{$v['feedback']}<br>
		<small>{$time} &middot; {$date}</small>
	</div>
EOF;
}


