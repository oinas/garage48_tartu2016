<?php

$travel_plans = $db->travel_plans;
if(isset($_POST['submit'])){
	$today = date("Y-m-d");
	$expire = $_POST['date'];

	$today_time = strtotime($today);
	$expire_time = strtotime($expire);
	if(strlen($_POST['from']) < 3){
		$ERROR[] = "You have not entered departure or is in wrong format";
	}
	if(strlen($_POST['to']) < 3){
		$ERROR[] = "You have not entered destination or is in wrong format";
	}
	if(strlen($_POST['date']) < 3 || $expire < $today || 
		!preg_match("/([0-9]+)\-([0-9]+)\-([0-9]+)/", $_POST['date'])){
		$ERROR[] = "You have not entered date or is in wrong format";
	}

	if(empty($ERROR)){
		$_POST['update'] = microtime(true);
		$_POST['user'] = $_SESSION['user'];
		if($ACTION == "add"){
			$_POST['added'] = date("Y-m-d H:i:s");
			$travel_plans -> insert($_POST);
			$_id = $_POST['_id'];
			wallPost($_SESSION['user'], $_id, "travelplanadded", "?travel_plan/view/{$ID}");
			header("Location: ?travel_plan");
			$SUCCESS[] = "You have successfully added new travel plan";
		} else {
			$_POST['modified'] = date("Y-m-d H:i:s");
			$travel_plans -> update(array("_id" => new MongoId($ID)), $_POST);
			$_id = $_POST['_id'];
			wallPost($_SESSION['user'], $_id, "travelplanupdated", "?travel_plan/view/{$ID}");
			header("Location: ?travel_plan");
			$SUCCESS[] = "You have successfully modified new travel plan";
		}
	} else {
		header("Location: " . $_SERVER['REQUEST_URI']);
	}
}

/** if we edit travel plan, populate fields to $_POST, that are latter used */
if($ACTION == "edit"){
	try {
		$_POST = $travel_plans->findOne(array("_id" => new MongoId($ID)));	
	} catch (Exception $e){
		header("Location: ?travel_plan");
		die();
	}
} else if($ACTION == "delete"){
	try {
		$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
	} catch (Exception $e){
		header("Location: ?travel_plan");
	}
	if(isset($_GET['confirm'])){
		$travel_plans->remove(array("_id" => new MongoId($ID)));
		wallPost($_SESSION['user'], $_id, "travelplandeleted", "?travel_plan/view/{$ID}");
		header("Location: ?travel_plan");
	}
	$entry['date'] = convertDate($entry['date']);
	$HTML[] = <<<EOF
		<div class="alert alert-danger center" role="alert">
			Are your sure you want to delete travel plan from {$entry['from']} to {$entry['to']} ({$entry['date']})?<br><br>
			<a href="?travel_plan/{$ACTION}/{$ID}/&confirm" class="btn btn-danger">Yes</a>
			&nbsp;&nbsp;
			<a href="#" onClick="history.go(-1)">No</a>
		</div>
EOF;
}

if($ACTION != "delete"){
	/** add form */
	formHeader($ACTION == "add" ? "Add new travel plan" : "Edit existing travel plan");
	formField("From", "from", "text", "", "Departure");
	formField("To", "to", "text", "", "Arrival");
	formField("Date", "date", "text", "", "Date of departure");
//	formField("Package size", "size", "text", "", "Package dimensions (WxHxD)");
//	formField("Weight", "weight", "text", "", "Maximum lugage weight");
//	formField("Hand luggage", "handluggage", "checkbox", "", " Some of the items might be restricted");
	formField("Additional informations", "description", "textarea");
	formFooter($ACTION == "add" ? "Add new plan" : "Modify plan");

	$HTML[] = <<<EOF
	<script>
	$( "#from" ).autocomplete({
		source: "ajax/cities.php",
		minLength: 2
	});
	$( "#to" ).autocomplete({
		source: "ajax/cities.php",
		minLength: 2
	});
	</script>
EOF;
}