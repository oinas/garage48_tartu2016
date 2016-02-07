<?php

$travel_plans = $db->travel_plans;

$HTML[] = <<<EOF
	<div class="right">
		<a href="?travel_plan/add" class="btn btn-success"><span class='glyphicon glyphicon-plus'></span> Add new travel plan</a>
	</div>
	<h1>Travel plans waiting for product requests</h1>
	<table class="table table-hover table-striped tablesorter" id="tablesorter">
		<thead>
			<tr>
				<th>#
				<th>Departure
				<th>Arrival
				<th>Departure date
				<th>Requests<br><small>pending/accepted</small>
				<td>
			</tr>
		</thead>
		<tbody>
EOF;

$i = 0;
foreach($travel_plans->find(array("user" => $_SESSION['user']))->sort(array("date" => 1)) as $k => $v){
	$i++;
	$v['date'] = convertDate($v['date'], false);

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
				<a href="?travel_plan/edit/{$v['_id']}"><span class="glyphicon glyphicon-edit"></span></a>
			<td><a href="?travel_plan/view/{$v['_id']}">{$v['from']}</a> 
				&nbsp;&nbsp;
			<td>{$v['to']}
			<td>{$v['date']}
			<td>{$pending}/{$accepted}
			<td><a href="?travel_plan/delete/{$v['_id']}"><span class="glyphicon glyphicon-remove"></span></a>
			
		</tr>
EOF;
	}
	if($i == 0){
		$HTML[] = <<<EOF
		<tr>
			<td colspan="6">No entries found
		</tr>
EOF;
	}

	$HTML[] = <<<EOF
			</tbody>
		</table>

<!--script>
$(document).ready(function() { 
			$("#tablesorter").tablesorter({ headers: 'myheaders' }); 
		} 
	); 
</script-->
EOF;


$requests = $db->requests;

$HTML[] = <<<EOF
	<h1>Travel plans added to product requests</h1>
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
	if(!isset($travel['requester'])){
		continue;
	}

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
	if($i == 0){
		$HTML[] = <<<EOF
		<tr>
			<td colspan="6">No entries found
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
