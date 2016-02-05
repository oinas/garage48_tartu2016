<?php

/** find travelers */
$travelers = $db->travel_plans;
$requesters = $db->requesters;

$results_travelers = array();
$results_requesters = array();

if(isset($_GET['showall'])){
	foreach($travelers->find()->sort(array("date" => 1)) as $k => $v){
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
		$match = 0;
		if(strlen($_GET['from']) > 0 && preg_match("/{$_GET['from']}/i", $v['from'])){
			$match++;
		}
		if(strlen($_GET['to']) > 0 && preg_match("/{$_GET['to']}/i", $v['to'])){
			$match++;
		}
		if(strlen($_GET['from']) > 0 && strlen($_GET['to']) > 0){
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

$HTML[] = <<<EOF

<div class="right">
	<div class="btn btn-primary" id="btn_travelers">Travelers <div class="badge">{$tmp1}</div></div>
	<div class="btn btn-button" id="btn_requesters">Requesters <div class="badge">{$tmp2}</div></div>
</div>
EOF;

if(isset($_GET['userprofile'])){
	$HTML[] = <<<EOF
		<h1>List of travel plans and requests</h1>
EOF;
} else {
	$HTML[] = <<<EOF
		<h1>Search results</h1>
EOF;
}

$HTML[] = <<<EOF
<div class="results">

<div id="view_travelers">
EOF;

if($tmp1 == 0){
	if(isset($_GET['userprofile'])){
		$HTML[] = "<h1>No entries found</h1>";
	} else {
		$HTML[] = "<h1>No entries found, try to change search terms</h1>";
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

<div id="view_requesters" style="display: none;">
EOF;

if($tmp2 == 0){
	if(isset($_GET['userprofile'])){
		$HTML[] = "<h1>No entries found</h1>";
	} else {
		$HTML[] = "<h1>No entries found, try to change search terms</h1>";
	}
} else {
	foreach($results_requesters as $k => $v){
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
			<a href="?travel_plan/view/{$v['_id']}">More information &amp; Contact requester</a>
		</div>
	</div>
EOF;
	}
}

$HTML[] = <<<EOF

</div>
</div>

<script>
$("#btn_travelers").click(function(){
	$("#view_travelers").show(1000, "easeInOutQuad");
	$("#view_requesters").hide(1000, "easeInOutQuad");
	$("#btn_travelers").switchClass( "btn-button", "btn-primary", 1000, "easeInOutQuad" );
	$("#btn_requesters").switchClass( "btn-primary", "btn-button", 1000, "easeInOutQuad" );
});
$("#btn_requesters").click(function(){
	$("#view_travelers").hide(1000, "easeInOutQuad");
	$("#view_requesters").show(1000, "easeInOutQuad");
	$("#btn_requesters").switchClass( "btn-button", "btn-primary", 1000, "easeInOutQuad" );
	$("#btn_travelers").switchClass( "btn-primary", "btn-button", 1000, "easeInOutQuad" );
});
</script>
EOF;


