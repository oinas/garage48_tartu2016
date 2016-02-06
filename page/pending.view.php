<?php

$travel_plans = $db->travel_plans;
$requests = $db->requests;

if($ID == "pending"){
	$status_is = 0;
	$HTML[] = <<<EOF
	<h1>Pending requests for your travel plans</h1>
EOF;
} else {
	$status_is = 1;
	$HTML[] = <<<EOF
	<h1>Accepted requests for your travel plans</h1>
EOF;
}

$HTML[] = <<<EOF

	<table class="table table-hover table-striped tablesorter" id="tablesorter">
		<thead>
			<tr>
				<th>#
				<th>Departure
				<th>Arrival
				<th>Request ending
				<th>Requester(s)
			</tr>
		</thead>
		<tbody>
EOF;

$i = 0;
foreach($travel_plans->find(array("user" => $_SESSION['user']))->sort(array("date" => -1)) as $k => $v){
	$r = 0;
	$people = array();
	foreach($requests->find(array("travel" => $k)) as $_k => $_v){
		if($_v['status'] == $status_is){
			$people[] = generateUserLink($_v['user']);
			$r++;
		}
	}
	if($r == 0){
		//if there are no accepted/pending requests, do not show entry
		continue;
	}
	$i++;
	$v['date'] = convertDate($v['date'], false);
	$v['requesters'] = implode(", ", $people);
	$HTML[] = <<<EOF
		<tr>
			<td>{$i}
			<td><a href="?travel_plan/view/{$v['_id']}">{$v['from']}</a> 
				&nbsp;&nbsp;

				<a href="?travel_plan/edit/{$v['_id']}"><span class="glyphicon glyphicon-edit"></span></a>
				<a href="?travel_plan/delete/{$v['_id']}"><span class="glyphicon glyphicon-remove"></span></a>
			<td>{$v['to']}
			<td>{$v['date']}
			<td>{$v['requesters']}
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

