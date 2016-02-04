<?php

$travel_plans = $db->travel_plans;

if(isset($_POST['submit'])){
	$_POST['update'] = microtime(true);
	$_POST['user'] = $_SESSION['user'];
	if($ACTION == "add"){
		$_POST['added'] = date("Y-m-d H:i:s");
		$travel_plans -> insert($_POST);
		header("Location: ?travel_plan_view");
	} else {
		$_POST['modified'] = date("Y-m-d H:i:s");
		$travel_plans -> update(array("_id" => new MongoId($ID)), $_POST);
		header("Location: ?travel_plan_view");
	}
}

/** if we edit travel plan, populate fields to $_POST, that are latter used */
if($ACTION == "edit"){
	$_POST = $travel_plans->findOne(array("_id" => new MongoId($ID)));
} else if($ACTION == "delete"){
	$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
	if(isset($_GET['confirm'])){
		$travel_plans->remove(array("_id" => new MongoId($ID)));
		header("Location: ?travel_plan_view");
	}
	$entry['date'] = convertDate($entry['date']);
	$HTML[] = <<<EOF
		<div class="alert alert-danger center" role="alert">
			Are your sure you want to delete travel plan from {$entry['from']} to {$entry['to']} ({$entry['date']})?<br><br>
			<a href="?{$PAGE}/{$ACTION}/{$ID}/&confirm" class="btn btn-danger">Yes</a>
			&nbsp;&nbsp;
			<a href="#" onClick="history.go(-1)">No</a>
		</div>
EOF;
}

if($ACTION != "delete"){
	/** add form */
	formHeader("Add new travel plan");
	formField("From", "from", "text", "", "Departure");
	formField("To", "to", "text", "", "Arrival");
	formField("Date", "date", "text", "", "Date of departure");
	formField("Package size", "size", "text", "", "Package dimensions (WxHxD)");
	formField("Weight", "weight", "text", "", "Maximum lugage weight");
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