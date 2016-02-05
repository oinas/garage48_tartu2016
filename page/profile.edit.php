<?php

$users = $db->users;

$user = $users->findOne(array("facebookid" => $_SESSION['user']));

if(isset($_POST['submit'])){
	$user['location'] = $_POST['location'];
	$user['phone'] = $_POST['phone'];
	$user['description'] = $_POST['description'];
	$users -> update(
		array("facebookid" => $_SESSION['user']),
		$user
	);	
	wallPost($_SESSION['user'], $_SESSION['user'], "editprofile", "?profile/edit");
}

$_POST = $user;

formHeader("Edit your profile");
formField("User", "user", "disabled", "", "Your user");
formField("Name", "first", "disabled", "", "Your name");
formField("E-mail", "email", "disabled", "", "Your email");
formField("Joined", "date", "disabled", "", "");
formField("Location", "location", "text", "", "Your location (country, city)");
formField("Phone", "phone", "text", "", "Your phone number");
formField("Short description", "description", "textarea");
formFooter("Save changes");