<?php

/** find travelers */
$travelers = $db->travel_plans;

$results_travelers = array();

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
			$results_travelers[] = $v;
		}
	} else {
		if($match == 1){
			$results_travelers[] = $v;
		}
	}
}

$requesters = $db->requesters;

$results_requesters = array();

foreach($requesters->find()->sort(array("date" => 1)) as $k => $v){
	$match = 0;
	if(strlen($_GET['from']) > 0 && preg_match("/{$_GET['from']}/i", $v['from'])){
		$match++;
	}
	if(strlen($_GET['to']) > 0 && preg_match("/{$_GET['to']}/i", $v['to'])){
		$match++;
	}
	if(strlen($_GET['from']) > 0 && strlen($_GET['to']) > 0){
		if($match == 2){
			$results_requesters[] = $v;
		}
	} else {
		if($match == 1){
			$results_requesters[] = $v;
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

<h1>Search results</h1>

<div class="results">

<div id="view_travelers">
EOF;

if($tmp1 == 0){
	$HTML[] = "<h1>No entries found, try to change search terms</h1>";
} else {
	foreach($results_travelers as $k => $v){
		$user = getUser($v['user']);
		$pic = getUserPicture($user['facebookid']);;
		$v['from'] = strtoupper($v['from']);
		$v['to'] = strtoupper($v['to']);
		$v['date'] = convertDateTime($v['date'], false);
		$tmp3 = empty($v['size']) ? "" : "";
		$tmp4 = empty($v['weight']) ? "" : "";
		$HTML[] = <<<EOF
	<div class="search-element hand">
		<div class="search-profile">
			<img src="$pic">
		</div>
		<div class="search-description">
			{$v['description']}
		</div>
		<span class="glyphicon glyphicon-user"></span> {$user['first']}
		<br>
		<span class="glyphicon glyphicon-map-marker"></span>FROM <b>{$v['from']}</b> TO <b>{$v['to']}</b>
		<br>
		<span class="glyphicon glyphicon-time"></span> {$v['date']} 
	</div>
EOF;
	}
}

$HTML[] = <<<EOF
</div>

<div id="view_requesters" style="display: none;">
EOF;

if($tmp2 == 0){
	$HTML[] = "<h1>No entries found, try to change search terms</h1>";
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


