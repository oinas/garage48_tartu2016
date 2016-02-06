<center>
<h1>CICO &middot; DEMO</h1>
<img src="css/biglogo.png" width="200"><br><br>
LOGIN AS USER<br><br>
<?php

require_once "config.php";

$users = $db->users;

foreach($users->find() as $k => $v){
	print_r($v);
}

/** automatic login */
?>
</center>