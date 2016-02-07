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
		$_POST['requester'] = true;	//mark 
		if($ACTION == "add"){
			$_POST['added'] = date("Y-m-d H:i:s");
			$travel_plans -> insert($_POST);
			$ID = $_POST["_id"];
			header("Location: ?product_request/view/{$ID}");
			wallPost($_SESSION['user'], $_SESSION['user'], "requestmodified", "?product_request/view/{$ID}");
			uploadFile("picture", $ID . $_SESSION['user']);
			$SUCCESS[] = "You have successfully added new product request";
		} else {
			$_POST['modified'] = date("Y-m-d H:i:s");
			try {
				$travel_plans -> update(array("_id" => new MongoId($ID)), $_POST);
			} catch (Exception $e){
				header("Location: ?product_request");
				die();
			}
			$ID = $_POST["_id"];
			header("Location: ?product_request/view/{$ID}");
			wallPost($_SESSION['user'], $_SESSION['user'], "requestcreated", "?product_request/view/{$ID}");
			uploadFile("picture", $ID . $_SESSION['user']);
			$SUCCESS[] = "You have successfully modified your product request";
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
		header("Location: ?product_request");
		die();
	}
} else if($ACTION == "delete"){
	try {
		$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
	} catch (Exception $e){
		header("Location: ?product_request");
		die();
	}
	if(isset($_GET['confirm'])){
		$travel_plans->remove(array("_id" => new MongoId($ID)));
		header("Location: ?product_request");
		die();
	}
	$entry['date'] = convertDate($entry['date']);
	$HTML[] = <<<EOF
		<div class="alert alert-danger center" role="alert">
			Are your sure you want to delete product request from {$entry['from']} to {$entry['to']} ({$entry['date']})?<br><br>
			<a href="?product_request/{$ACTION}/{$ID}/&confirm" class="btn btn-danger">Yes</a>
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