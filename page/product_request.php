<?php

$requests = $db->requests;

$HTML[] = <<<EOF
	<div class="right">
		<a href="?product_request/add" class="btn btn-success"><span class='glyphicon glyphicon-plus'></span> Create new product request</a>
	</div>
	<h1>My product requests connected with travel plan</h1>
EOF;

$HTML[] = <<<EOF
	<table class="table table-hover table-striped tablesorter" id="tablesorter">
		<thead>
			<tr>
				<th>#
				<th>Departure
				<th>Arrival
				<th>Date
				<th>Traveler
				<th>Status
			</tr>
		</thead>
		<tbody>
EOF;

$i = 0;
foreach($requests->find(array("user" => $_SESSION['user']))->sort(array("date" => -1)) as $k => $v){

	$travels = $db->travel_plans;

	$travel = $travels->findOne(array("_id" => new MongoId($v['travel'])));
	$i++;
	$v['date'] = convertDate($travel['date'], false) . "<br>" . relativeTime(convertDateToTime($travel['date'], false));

	$traveler = generateUserLink($travel['user']);
	if($v['status'] == 0){
		$status = "pending";
	} else if($v['status'] == 1){
		$status = "accepted";
	} else {
		$status = "rejected";
	}

	$HTML[] = <<<EOF
		<tr>
			<td>{$i}
			<td><a href="?travel_plan/view/{$v['travel']}">{$travel['from']}</a> 
			<td>{$travel['to']}
			<td>{$v['date']}
			<td>$traveler
			<td>$status
		</tr>
EOF;
	}

	$HTML[] = <<<EOF
			</tbody>
		</table>

<!--script>
$(document).ready(function() { 
			$("#tablesorter").tablesorter(); 
		} 
	); 
</script-->
EOF;

$HTML[] = <<<EOF
	<h1>My product requests not connected with travel plan</h1>
EOF;



$HTML[] = <<<EOF
	<table class="table table-hover table-striped tablesorter" id="tablesorter2">
		<thead>
			<tr>
				<th>#
				<th>From
				<th>To
				<th>Requests ends
				<th>Pending/Accepted requests
			</tr>
		</thead>
		<tbody>
EOF;

$travelers = $db->travel_plans;
$results_requesters = array();

foreach($travelers->find()->sort(array("date" => 1)) as $k => $v){
	if(isset($v['user']) && $v['user'] == $_SESSION['user']){
		if(isset($v['requester'])){
			$results_requesters[] = $v;
		}
	}
}

$i = 0;
foreach($results_requesters as $k => $v){
	$i++;
	$v['date'] = convertDate($travel['date'], false) . "<br>" . relativeTime(convertDateToTime($travel['date'], false));

	$accepted = 0;
	$pending = 0;
	
	$list = getRequests(array("travel" => "" . $v['_id']));
	foreach($list as $_k => $_v){
		if($_v['status'] == 0){
			$pending++;
		} else if($_v['status'] == 1){
			$accepted++;
		}
	}

	$HTML[] = <<<EOF
		<tr>
			<td>{$i}
				<a href="?product_request/edit/{$v['_id']}"><span class="glyphicon glyphicon-edit"></span></a>
				<a href="?product_request/delete/{$v['_id']}"><span class="glyphicon glyphicon-remove"></span></a>

			<td><a href="?travel_plan/view/{$v['_id']}">{$v['from']}</a> 
			<td>{$v['to']}
			<td>{$v['date']}
			<td>$pending/$accepted
		</tr>
EOF;
}



	$HTML[] = <<<EOF
			</tbody>
		</table>

<!--script>
$(document).ready(function() { 
			$("#tablesorter2").tablesorter(); 
		} 
	); 
</script-->
EOF;
