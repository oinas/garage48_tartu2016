<center>
<h1>CICO &middot; DEMO</h1>
<img src="css/biglogo.png" width="200"><br><br>
SELECT USER<br><br>
<?php

require_once "config.php";

$users = $db->users;

foreach($users->find() as $k => $v){
	$rel = relativeTime($v['last']);
	if(file_exists("../index2.html")){
		echo <<<EOF
			<a href="/garage48_tartu2016/?user={$v['user']}&amp;password=test">
			{$v['user']} &middot; {$v['first']} (last online {$rel}
			</a><br><br>
EOF;
	} else {
		echo <<<EOF
			<a href="/?user={$v['user']}&amp;password=test">
			{$v['user']} &middot; {$v['first']} (last online {$rel})
			</a><br><br>
EOF;
	}
}

/** automatic login */
?>
</center>