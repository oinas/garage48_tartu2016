<?php

$travel_plans = $db->travel_plans;

$HTML[] = <<<EOF
	<h1>Travel plans</h1>
	<table class="table table-hover table-striped tablesorter" id="tablesorter">
		<thead>
			<tr>
				<th>#
				<th>Departure
				<th>Arrival
				<th>Departure date
				<th>Pending/Accepted<br>requests
			</tr>
		</thead>
		<tbody>
EOF;

$i = 0;
foreach($travel_plans->find(array("user" => $_SESSION['user']))->sort(array("date" => -1)) as $k => $v){
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
				<a href="?travel_plan/delete/{$v['_id']}"><span class="glyphicon glyphicon-remove"></span></a>
			<td><a href="?travel_plan/view/{$v['_id']}">{$v['from']}</a> 
				&nbsp;&nbsp;
			<td>{$v['to']}
			<td>{$v['date']}
			<td>{$pending}/{$accepted}
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
