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
		<tr>
			<th colspan="2">{$title}
		</tr>
EOF;
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
	} else {
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td><input type="{$type}" name="{$name}" value="{$value}" placeHolder="{$desc}" class="form-control" id="{$name}" autocomplete="off">
			</tr>
EOF;
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

function relativeTime($microtime){
	$diff = microtime(true) - $microtime;
	if($diff < 0){
		$diff = -$diff;
	}
	if($diff < 60){
		return "now";
	} else if($diff < 60 * 60){
		return (int) ($diff / 60) . " minutes ago";
	} else if($diff < 60 * 60 * 24){
		return (int) ($diff / 60 / 60) . " hours ago";
	} else if($diff < 60 * 60 * 24 * 30){
		return (int) ($diff / 60 / 60 / 24) . " days ago";
	} else if($diff < 60 * 60 * 24 * 30 * 12){
		return (int) ($diff / 60 / 60 / 24 / 30) . " months ago";
	} else {
		return (int) ($diff / 60 / 60 / 24 / 30 / 12) . " years ago";
	}
}

function getUser($userid){
	global $db;

	$user = $db->users;

	$entry = $user->findOne(array("facebookid" => $userid));

	return !empty($entry) ? $entry : null;
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