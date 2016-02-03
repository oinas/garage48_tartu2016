<?php

$travel_plans = $db->travel_plans;

if($ACTION == "view"){
	$HTML[] = <<<EOF
		<h1>Travel plan</h1>
EOF;
	$entry = $travel_plans->findOne(array("_id" => new MongoId($ID)));
	$HTML[] = <<<EOF
	<table class="table table-hover table-striped">
EOF;
	$allowed = array("from", "to", "date", "size", "weight", "description");
	showValues($entry, $allowed);
	$HTML[] = <<<EOF
	</table>
EOF;
} else {

	$HTML[] = <<<EOF
		<h1>Travel plans</h1>
EOF;

	$HTML[] = <<<EOF
		<table class="table table-hover table-striped">
			<tr>
				<th>#
				<th>Departure
				<th>Arrival
				<th>Date
				<th>Lugage size
				<th>Lugage weight
			</tr>
EOF;



	$i = 0;
	foreach($travel_plans->find(array("user" => $_SESSION['user'])) as $k => $v){
		$i++;
		$v['date'] = convertDate($v['date'], false);
		$HTML[] = <<<EOF
			<tr>
				<td>{$i}
				<td><a href="?travel_plan_view/view/{$v['_id']}">{$v['from']}</a> 

					<a href="?travel_plan/edit/{$v['_id']}"><span class="glyphicon glyphicon-edit"></span></a>
					<a href="?travel_plan/delete/{$v['_id']}"><span class="glyphicon glyphicon-remove"></span></a>
				<td>{$v['to']}
				<td>{$v['date']}
				<td>{$v['size']}
				<td>{$v['weight']}
			</tr>
EOF;
	}

	$HTML[] = <<<EOF
		</table>
EOF;

}
