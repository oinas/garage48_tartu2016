<?php

$requests = $db->requests;

$HTML[] = <<<EOF
	<h1>My product requests</h1>
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

<script>
$(document).ready(function() { 
			$("#tablesorter").tablesorter(); 
		} 
	); 
</script>
EOF;
