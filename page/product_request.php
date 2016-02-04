<?php

$product_requests = $db->product_requests;

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
				<th>Approximate size
				<th>Approximate weight
			</tr>
		</thead>
		<tbody>
EOF;



$i = 0;
foreach($travel_plans->find(array("user" => $_SESSION['user']))->sort(array("date" => -1)) as $k => $v){
	$i++;
	$v['date'] = convertDate($v['date'], false) . "<br>" . relativeTime(convertDateToTime($v['date'], false));

	$HTML[] = <<<EOF
		<tr>
			<td>{$i}
			<td><a href="?product_request/view/{$v['_id']}">{$v['from']}</a> 
				&nbsp;&nbsp;
				<a href="?product_request/edit/{$v['_id']}"><span class="glyphicon glyphicon-edit"></span></a>
				<a href="?product_request/delete/{$v['_id']}"><span class="glyphicon glyphicon-remove"></span></a>
			<td>{$v['to']}
			<td>{$v['date']}
			<td>{$v['size']}
			<td>{$v['weight']}
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
