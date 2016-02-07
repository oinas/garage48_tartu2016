<?php

if(isset($_POST['submit'])){
	if(strlen($_POST['feedback']) > 3){
		$feedback = $db->feedback;
		$_POST['user'] = $_SESSION['user'];
		$_POST['fb_id'] = $_SESSION['fb_id'];
		$_POST['fb_name'] = $_SESSION['fb_name'];
		$_POST['date'] = date("Y-m-d H:i:s");
		$_POST['update'] = microtime(true);
		$feedback->insert(
				$_POST
			);
		$SUCCESS[] = "Your feedback has been successfully added";
		header("Location: ?livefeedback/added");
	} else {
		$ERROR[] = "Messages is too short";
	}
}

if($ACTION == "added"){
	$HTML[] = <<<EOF
		<h1>Thank you for submitting feedback</h1>
		<p><br>Your feedback is valuable for us. <a href="?front_search">Go to search!</a></p>
EOF;
} else {
	formHeader("Give us feedback to improve our service");
	formField("Your suggestions and feedback", "feedback", "textarea", "");
	formFooter("Send feedback");
}
