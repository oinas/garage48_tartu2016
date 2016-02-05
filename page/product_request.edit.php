<?php

$travel_plans = $db->travel_plans;

if(isset($_POST['submit'])){
	$_POST['update'] = microtime(true);
	$_POST['user'] = $_SESSION['user'];
	$_POST['requester'] = true;	//mark 
	if($ACTION == "add"){
		$_POST['added'] = date("Y-m-d H:i:s");
		$travel_plans -> insert($_POST);
		$ID = $_POST["_id"];
		header("Location: ?travel_plan/{$ACTION}/{$ID}");
		wallPost($_SESSION['user'], $_SESSION['user'], "requestmodified", "?travel_plan/view/{$ID}");
		uploadFile("picture", $ID . $_SESSION['user']);
	} else {
		$_POST['modified'] = date("Y-m-d H:i:s");
		$travel_plans -> update(array("_id" => new MongoId($ID)), $_POST);
		$ID = $_POST["_id"];
		header("Location: ?travel_plan/{$ACTION}/{$ID}");
		wallPost($_SESSION['user'], $_SESSION['user'], "requestcreated", "?travel_plan/view/{$ID}");
		uploadFile("picture", $ID . $_SESSION['user']);
	}
}

/** if we edit travel plan, populate fields to $_POST, that are latter used */
if($ACTION == "edit"){
	$_POST = $travel_plans->findOne(array("_id" => new MongoId($ID)));
} else if($ACTION == "delete"){
	$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
	if(isset($_GET['confirm'])){
		$travel_plans->remove(array("_id" => new MongoId($ID)));
		header("Location: ?travel_plan");
	}
	$entry['date'] = convertDate($entry['date']);
	$HTML[] = <<<EOF
		<div class="alert alert-danger center" role="alert">
			Are your sure you want to delete product request from {$entry['from']} to {$entry['to']} ({$entry['date']})?<br><br>
			<a href="?{$PAGE}/{$ACTION}/{$ID}/&confirm" class="btn btn-danger">Yes</a>
			&nbsp;&nbsp;
			<a href="#" onClick="history.go(-1)">No</a>
		</div>
EOF;
}

if($ACTION != "delete"){
	/** add form */
	formHeader($ACTION == "add" ? "Add new product request" : "Edit existing product request");
	formField("From", "from", "text", "", "From");
	formField("To", "to", "text", "", "To");
	formField("Requests ends", "date", "text", "", "When would request end");
//	formField("Approximate package size", "size", "text", "", "Package dimensions (WxHxD)");
//	formField("Aprroximate Weight", "weight", "text", "", "Maximum lugage weight");
	//formField("Hand luggage", "handluggage", "checkbox", "", " Item is allowed");
//	formField("", "fragile", "checkbox", "", " Product is fragile, handle with care");
//	formField("", "solid", "checkbox", "", " Product is solid");
//	formField("", "liquid", "checkbox", "", " Product is liquid");
	formField("Description of product(s)", "description", "textarea");
	formField("You can add picture of the item", "picture", "file");
	formFooter($ACTION == "add" ? "Add new product request" : "Modify existing product request");

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