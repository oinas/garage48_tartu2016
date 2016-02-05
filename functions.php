<?php

/**
	Some quick/dirty functions/hacks to display content fast. Use $HTML[] to add content and mark it as GLOBAL
*/

/**
	Create HTML tables with rows and input fields fast
*/
function formHeader($title = ""){
	global $HTML;
	$HTML[] = <<<EOF
	<form action="" method="POST" enctype="multipart/form-data">
	<table class="table table-stripped table-hover form-table">
EOF;
	if(!empty($title)){
		$HTML[] = <<<EOF
		<tr>
			<th colspan="2">{$title}
		</tr>
EOF;
	}
}

function formFooter($submit = "Submit", $cancel = "Cancel"){
	global $HTML;
	$HTML[] = <<<EOF
		<tr>
			<td colspan="2">
				<input type="submit" name="submit" value="{$submit}" class="btn btn-primary">
				<input type="button" name="cancel" value="{$cancel}" class="btn" onClick="history.go(-1)">
		</tr>
	</table>
	</form>
EOF;
}

/**
	General purpose function to make quickly form elements
*/
function formField($title, $name, $type = "text", $value = "", $desc = "", $values = array()){
	global $HTML;
	if(empty($value)){
		// if we do not have preset value, take it from $_SESSION
		if(isset($_SESSION['POST'][$name])){
			$value = $_SESSION['POST'][$name];
		}
		if(isset($_POST[$name])){
			$value = $_POST[$name];
		}
	}
	if($type == "textarea"){
		$HTML[] = <<<EOF
			<tr>
				<td colspan="2"><strong>{$title}</strong><br>
				<textarea name="{$name}" rows="6" class="form-control" id="{$name}">{$value}</textarea>
			</tr>
EOF;
	} else if($type == "select"){
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td><select name="{$name}" class="form-control" id="{$name}">
					<option value="">{$desc}	
EOF;
		foreach($values as $k => $v){
			$HTML[] = <<<EOF
					<option value="{$k}">{$v}
EOF;
		}
		$HTML[] = <<<EOF
					</select>
			</tr>	
EOF;
	} else if($type == "file"){
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td><input type="file" name="{$name}" id="{$name}">	
			</tr>
EOF;
	} else if($type == "checkbox"){
		$tmp = $value == "true" ? " CHECKED" : "";
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td>
					<label>
					<input type="checkbox" name="{$name}" id="{$name}" value="true" {$tmp}>	
					{$desc}
					</label>
			</tr>
EOF;
	} else {
		if($type == "hidden"){
			$HTML[] = <<<EOF
			<input type="{$type}" name="{$name}" value="{$value}" placeHolder="{$desc}" class="form-control" id="{$name}" autocomplete="off">
EOF;
		} else {
			$tmp = ($type == "disabled") ? "disabled" : "";
			$HTML[] = <<<EOF
				<tr>
					<td><strong>{$title}</strong>
					<td><input type="{$type}" name="{$name}" value="{$value}" placeHolder="{$desc}" class="form-control" id="{$name}" autocomplete="off" {$tmp}>
				</tr>
EOF;
		}
	}
	if($name == "date"){
		$HTML[] = <<<EOF
			  <script>
			  $(function() {
			    $( "#date" ).datepicker({ dateFormat: 'yy-mm-dd' });
			  });
			  </script>
EOF;
	}
}

function showValues($entry, $allowed){
	global $HTML;
	foreach($entry as $k => $v){
		if(in_array($k, $allowed)){
			$k = ucfirst($k);
			$HTML[] = <<<EOF
				<tr>
					<td><strong>{$k}</strong>
					<td>{$v}
				</tr>
EOF;
		}
	}
}

$MONTHS = array("", "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

function convertDate($date){
	global $MONTHS;
	$tmp = array();
	$tmp = explode("-", $date);
	if(count($tmp) >= 3){
		return $tmp[2] . ". " . $MONTHS[(int) $tmp[1]] . " " . $tmp[0];
	} else {
		return "";	// invalid date, do not return anything
	}
}

function convertDateTime($date, $showTime = true){
	global $MONTHS;
	$tmp = explode(" ", $date);
	$tmp1 = explode("-", $tmp[0]);
	$rtn = $tmp1[2] . ". " . $MONTHS[(int) $tmp1[1]] . " " . $tmp1[0];
	if($showTime && count($tmp) >= 2){
		$tmp2 = explode(":", $tmp[1]);
		if($showTime){
			$rtn .= " " . $tmp2[0] . ":" . $tmp2[1];
		}
	}
	return $rtn;
}

function convertDateToTime($date, $showTime = true){
	if($showTime){
		$tmp = explode(" ", $date);
		$tmp1 = $tmp[0];
		$tmp2 = $tmp[1];

		list($year, $month, $day) = explode('-', $tmp1);
		list($hour, $minut, $second) = explode(':', $tmp2);
		return mktime($second, $minut, $hour, $month, $day, $year);
	} else {
		list($year, $month, $day) = explode('-', $date);
		return mktime(0, 0, 0, $month, $day, $year);
	}
}

function dateToRelative($date){
	$date = explode(" ", $date)[0];
	return relativeTime(convertDateToTime($date, false));
}

function relativeTime($microtime){
	$diff = microtime(true) - $microtime;
	$past = "ago";
	$past2 = "";
	if($diff < 0){
		$diff = -$diff;
		$past = "";
		$past2 = "in ";
	}
	if($diff < 60){
		return "now";
	} else if($diff < 60 * 60){
		return $past2 . (int) ($diff / 60) . " minute(s) " . $past;
	} else if($diff < 60 * 60 * 24){
		return $past2 . (int) ($diff / 60 / 60) . " hour(s) " . $past;
	} else if($diff < 60 * 60 * 24 * 30){
		return $past2 . (int) ($diff / 60 / 60 / 24) . " day(s) " . $past;
	} else if($diff < 60 * 60 * 24 * 30 * 12){
		return $past2 . (int) ($diff / 60 / 60 / 24 / 30) . " month(s) " . $past;
	} else {
		return $past2 . (int) ($diff / 60 / 60 / 24 / 30 / 12) . " year(s) " . $past;
	}
}

function getUser($userid){
	global $db;

	$user = $db->users;

	$entry = $user->findOne(array("facebookid" => $userid));

	return !empty($entry) ? $entry : null;
}

function generateUserLink($userid){
	$entry = getUser($userid);

	return <<<EOF
		<a href="?users/view/{$userid}">{$entry['first']}</a>
EOF;
}

function generateUserPicture($userid, $class){
	$pic = getUserPicture($userid);
	return <<<EOF
	<img src="{$pic}" class="{$class}">
EOF;
}

/** TODO: THIS IS REALLY DIRTY HACK AND SHOULD NOT BE USED FOR LARGE SITES */
function getUserPicture($userid){
	if(empty($userid)){
		return "css/noimage.jpg";
	}
	if(!isset($_SESSION['fb_pictures'][$userid])){
		$json = file_get_contents("https://graph.facebook.com/" . $userid . "/picture?type=large&redirect=0");
		$tmp = json_decode($json);
		$_SESSION['fb_pictures'][$userid] = $tmp->data->url;		
	}
	return !empty($_SESSION['fb_pictures'][$userid]) ? $_SESSION['fb_pictures'][$userid] : "css/noimage.jpg";
}

function getTravels($arr){
	global $db;
	if(empty($arr)){
		return null;
	}

	$travels = $db->travel_plans;

	$entry = $travels->find($arr);

	return $entry;
}

function getRequests($arr){
	global $db;
	if(empty($arr)){
		return null;
	}

	$requests = $db->requests;

	$entry = $requests->find($arr);

	return $entry;
}

function getCount($mongolist){
	$count = 0;
	foreach($mongolist as $k => $v){
		$count++;
	}
	return $count;
}

function uploadFile($file, $target_file){
	if(!isset($_FILES[$file])){
		if (file_exists("upload/" . $target_file)) {
			unlink("upload/" . $target_file);
		}
		return false;
	}
	if($_FILES[$file]['error'] != 0){
		return false;
	}
	$image_file_type = pathinfo($_FILES[$file]["name"], PATHINFO_EXTENSION);
	if (file_exists("upload/" . $target_file)) {
		unlink("upload/" . $target_file);
	}
	if($image_file_type != "jpg" && $image_file_type != "png" && $image_file_type != "jpeg"
&& $image_file_type != "gif" ) {
		return false;
	}
	move_uploaded_file($_FILES[$file]["tmp_name"], "upload/" . $target_file);
}

/** just quick HACK to write wall posts randomly */
function wallPost($user1, $user2, $event, $page){
	global $db;

	$notifications = $db->notifications;
	$n = $notifications->findOne(array("user" => $user1));
	if(empty($n)){
		$notifications -> insert(
				array("user" => $user1, "count" => 1)
			);
	} else {
		$notifications -> update(
			array("user" => $user1),
			array("user" => $user1, "count" => $n['count'] + 1)
		);
	}

	$walls = $db->walls;

	$walls -> insert(
		array(
			"user1" => $user1,
			"user2" => $user2,
			"event" => $event,
			"page" => $page,
			"date" => date("Y-m-d H:i:s"),
			"update" => microtime(true),
			"viewed" => 0
		)
	);
}