<?php

/** find travelers */
$travelers = $db->travel_plans;

$results_requesters = array();
$results_travelers = array();

if(isset($_GET['showall'])){
	foreach($travelers->find()->sort(array("date" => 1)) as $k => $v){
		$today = date("Y-m-d");
		$expire = $v['date'];

		$today_time = strtotime($today);
		$expire_time = strtotime($expire);

		if ($expire_time < $today_time) { 
			continue;
		}
		if(isset($_GET['user'])){
			if(isset($v['user']) && $v['user'] == $_GET['user']){
				if(isset($v['requester'])){
					$results_requesters[] = $v;
				} else {
					$results_travelers[] = $v;
				}
			}
		} else {
			if(isset($v['requester'])){
				$results_requesters[] = $v;
			} else {
				$results_travelers[] = $v;
			}
		}
	}
} else {
	foreach($travelers->find()->sort(array("date" => 1)) as $k => $v){
		$today = date("Y-m-d");
		$expire = $v['date'];

		$today_time = strtotime($today);
		$expire_time = strtotime($expire);

		if ($expire_time < $today_time) { 
			continue;
		}
		$match = 0;
		if(isset($_GET['from']) && strlen($_GET['from']) > 0 && preg_match("/{$_GET['from']}/i", $v['from'])){
			$match++;
		}
		if(isset($_GET['to']) && strlen($_GET['to']) > 0 && preg_match("/{$_GET['to']}/i", $v['to'])){
			$match++;
		}
		if(isset($_GET['from']) && isset($_GET['to']) && strlen($_GET['from']) > 0 && strlen($_GET['to']) > 0){
			if($match == 2){
				if(isset($v['requester'])){
					$results_requesters[] = $v;
				} else {
					$results_travelers[] = $v;
				}
			}
		} else {
			if($match == 1){
				if(isset($v['requester'])){
					$results_requesters[] = $v;
				} else {
					$results_travelers[] = $v;
				}
			}
		}
	}
}

$tmp1 = count($results_travelers);
$tmp2 = count($results_requesters);

if(isset($_GET['userprofile'])){
	$HTML[] = <<<EOF
		<h1>List of travel plans</h1>
EOF;
} else {
	$HTML[] = <<<EOF

<div class="front-search">
	<form action="" method="GET">
	<input type="hidden" name="q" value="search">
	<input type="hidden" name="type" value="traveler">

	<input type="text" name="from" value="{$from}" placeholder="Departure" class="form-control form-search" autocomplete="off" id="to1">
	<input type="text" name="to" value="{$to}" placeholder="Arrival" class="form-control form-search" autocomplete="off" id="from1">
					
	<div class="search-button btn btn-primary btn-darkblue" id="sb" onclick="$('#sbclick1').click()"><span class="glyphicon glyphicon-search"></span></div>
	<input type="submit" name="submit" id="sbclick1" value="Search" style="display: none">

	</form>
</div>
<script>
$( "#from1" ).autocomplete({
	source: "ajax/existing_cities.php",
	minLength: 1
});
$( "#to1" ).autocomplete({
	source: "ajax/existing_cities.php",
	minLength: 1
});
</script>
EOF;

	$HTML[] = <<<EOF
<h1>Search results for travelers</h1>
EOF;
}

$HTML[] = <<<EOF
<div class="results">

<div id="view_travelers">
EOF;

if($tmp1 == 0){
	if(isset($_GET['userprofile'])){
		$HTML[] = "<h4>No entries found</h4>";
	} else {
		$HTML[] = "<h4>No entries found, try to change search terms</h4>";

		$HTML[] = <<<EOF

	<div class="front-search front-search-extra">
	or submit request for your product<br><br>
	<a href="?product_request/add" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> SUBMIT NEW PRODUCT REQUEST</a>
	</div>	
EOF;
	}
} else {
	foreach($results_travelers as $k => $v){
		$user = @getUser($v['user']);
		$user['first'] = generateUserLink($v['user']);
		$pic = getUserPicture($user['facebookid']);;
		$v['from'] = "<a href='https://maps.google.com/?q={$v['from']}' target='_blank'>" . strtoupper($v['from']) . "</a>";
		$v['to'] = "<a href='https://maps.google.com/?q={$v['to']}' target='_blank'>" . strtoupper($v['to']) . "</a>";
		$v['date'] = convertDateTime($v['date'], false) . " - " . dateToRelative($v['date'], false);
		$tmp3 = empty($v['size']) ? "" : "";
		$tmp4 = empty($v['weight']) ? "" : "";
		// luggage type: <span class="glyphicon glyphicon-briefcase"></span>
		// info: <span class="glyphicon glyphicon-info-sign"></span>
		$tmp5 = "Created " . relativeTime($v['update']);
		$HTML[] = <<<EOF
	<div class="search-element">
		<div class="search-profile">
			<img src="$pic">
		</div>
		<div class="search-description">
			{$v['description']}
		</div>
		<div class="search-details">
			<span class="glyphicon glyphicon-user"></span> {$user['first']}
			<br>
			<span class="glyphicon glyphicon-map-marker"></span>FROM <b>{$v['from']}</b> TO <b>{$v['to']}</b>
			<br>
			<span class="glyphicon glyphicon-time"></span> {$v['date']} 
			<br>
		</div>
		<div class="search-button btn btn-warning">
			<a href="?travel_plan/view/{$v['_id']}">More information &amp; Contact traveler</a>
		</div>
	</div>
EOF;
	}
}

$HTML[] = <<<EOF
</div>
</div>

EOF;

// hack to delete no entries found if we have not done any search
if(!isset($_GET['profile']) && !isset($_GET['to'])){
	$fix = count($HTML);
	for($i = $fix - 1; $i > $fix - 6; $i--){
		unset($HTML[$i]);
	}
}
