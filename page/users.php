<?php

$users = $db->users;

$HTML[] = <<<EOF
	<h1>List of users</h1>
EOF;

$HTML[] = <<<EOF
	<table class="table table-hover table-striped tablesorter" id="tablesorter">
		<thead>
			<tr>
				<th>#
				<th>Name
				<th>Travels
				<th>Requests
				<th>Grade
				<th>Referrals
				<th>Joined
				<th>Last online
			</tr>
		</thead>
		<tbody>
EOF;

$i = 0;
foreach($users->find()->sort(array("last" => -1)) as $k => $v){
	$i++;
	$v['travels'] = 0;
	$v['requests'] = 0;
	$v['grade'] = 4.6;
	$v['referrals'] = 26;
	$v['joined'] = @convertDateTime($v['date'], false);
	$v['online'] = relativeTime($v['last']);
	$HTML[] = @<<<EOF
		<tr>
			<td>{$i}
			<td><a href="?users/view/{$v['user']}">{$v['first']}</a> 
			<td>{$v['travels']}
			<td>{$v['requests']}
			<td>{$v['grade']}
			<td>{$v['referrals']}
			<td>{$v['joined']}
			<td>{$v['online']}
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
