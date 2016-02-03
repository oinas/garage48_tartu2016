<?php

/* LOGIN FUNCTIONALLITY HERE */
if(isset($_POST['submit'])){
	$entry = $db->users->findOne(array("user" => "traveler"));
	if(!empty($entry)){
		if($entry['password'] == md5($_POST['password'])){
			// login correct
			$SUCCESS[] = "Login successful";
			header("Location: ?wall");
			$_SESSION['user'] = $v['id'];
		} else {
			$ERROR[] = "User and/or password wrong";
		}
	}
}

$HTML[] = <<<EOF
Demo users: man, woman, user, traveler, requester<br>
Password is test
EOF;

formHeader();
formField("User", "user", "text");
formField("Password", "password", "password");
formFooter();
