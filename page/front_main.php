<?php

/* LOGIN FUNCTIONALLITY HERE */
if(isset($_POST['submit'])){
	$DB = new DB(array("table" => "users"));
	
	foreach($DB->getEntries() as $k => $v){
		if($v['user'] == $_POST['user']){
			if($v['password'] == md5($_POST['password'])){
				// login correct
				$SUCCESS[] = "Login successful";
				header("Location: wall");
				$_SESSION['user'] = $v['id'];
			} else {
				$ERROR[] = "User and/or password wrong";
			}
		}
		echo $k . "\n";
		print_r($v);
	}
	
	//header("Location: wall");
}

$HTML[] = <<<EOF
Demo users: man, woman, user, traveler, requester<br>
Password is test
EOF;

formHeader();
formField("User", "user", "text");
formField("Password", "password", "password");
formFooter();
