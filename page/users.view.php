<?php

$users = $db->users;

$user = $users->findOne(array("facebookid" => $ID));

$when = dateToRelative($user['date']);
$user['last'] = relativeTime($user['last']);

$user['pic'] = generateUserPicture($user['facebookid'], "user-view-left");
if(!isset($user['phone'])){
	$user['phone'] = "";
}
if(!isset($user['location'])){
	$user['location'] = "";
}

$HTML[] = <<<EOF
<h1>Info about {$user['first']}</h1>

<div class="content-box">
{$user['pic']}
<table class="table table-hover user-table-right">
	<tr>
		<th colspan="2"><span class="glyphicon glyphicon-exclamation-sign"></span> Information
	</tr>
	<tr>
		<td>User
		<td>{$user['user']}
	</tr>
	<tr>
		<td>Name
		<td>{$user['first']}
	</tr>
	<tr>
		<td>Email
		<td>{$user['email']}
	</tr>
	<tr>
		<td>Joined
		<td>{$user['date']} - {$when}
	</tr>
	<tr>
		<td>Last online
		<td>{$user['last']}
	</tr>
	<tr>
		<td>Location
		<td>{$user['location']}
	</tr>
	<tr>
		<td>Phone
		<td>{$user['phone']}
	</tr>
	<tr>
		<td colspan="2">{$user['description']}
	</tr>
</table>

</div>
EOF;

$_GET['user'] = $_SESSION['user'];
$_GET['showall'] = true;
$_GET['userprofile'] = true;

/** show search? */
require_once "page/search.php";


require_once "page/search_requesters.php";