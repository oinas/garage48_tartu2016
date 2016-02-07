<?php

$form = isset($_GET['form']) ? $_GET['form'] : "";
$to = isset($_GET['to']) ? $_GET['to'] : "";

$HTML[] = <<<EOF

<div class="front-search">
	<form action="" method="GET">
	<h1>Searching traveler?</h1>
	<input type="hidden" name="q" value="search">
	<input type="hidden" name="type" value="traveler">

	<input type="text" name="from" value="{$from}" placeholder="Departure" class="form-control form-search" autocomplete="off" id="to1">
	<input type="text" name="to" value="{$to}" placeholder="Arrival" class="form-control form-search" autocomplete="off" id="from1">
					
	<div class="search-button btn btn-primary btn-darkblue" id="sb" onclick="$('#sbclick1').click()"><span class="glyphicon glyphicon-search"></span></div>
	<input type="submit" name="submit" id="sbclick1" value="Search" style="display: none">

	</form>
	<div class="front-search-extra">
	<span>If you are traveling yourself, help others out</span><br><br>
	<a href="?travel_plan/add" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> SUBMIT NEW TRAVEL PLAN</a>
	</div>
</div>

EOF;


$HTML[] = <<<EOF

<div class="front-search">
	<form action="" method="GET">
	<h1>Searching product request?</h1>
	<input type="hidden" name="q" value="search_requesters">
	<input type="hidden" name="type" value="requester">

	<input type="text" name="from" value="{$from}" placeholder="Departure" class="form-control form-search" autocomplete="off" id="to2">
	<input type="text" name="to" value="{$to}" placeholder="Arrival" class="form-control form-search" autocomplete="off" id="from2">
					
	<div class="search-button btn btn-primary btn-darkblue" id="sb" onclick="$('#sbclick2').click()"><span class="glyphicon glyphicon-search"></span></div>
	<input type="submit" name="submit" id="sbclick2" value="Search" style="display: none">

	</form>
	<div class="front-search-extra">
	<span>If you did not find traveler, submit request for your product</span><br><br>
	<a href="?product_request/add" class="btn btn-success"><span class="glyphicon glyphicon-plus"></span> SUBMIT NEW PRODUCT REQUEST</a>
	</div>	
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
$( "#from2" ).autocomplete({
	source: "ajax/existing_cities.php",
	minLength: 1
});
$( "#to2" ).autocomplete({
	source: "ajax/existing_cities.php",
	minLength: 1
});
</script>

EOF;
