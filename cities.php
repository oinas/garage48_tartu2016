<?php

$f = file("data/cities.txt");

$list = array();

foreach($f as $k => $v){
	$v = trim($v);
	if(preg_match("/{$_GET['term']}/", $v)){
		$v = explode("\t", $v)[0];
		$list[] = array("id" => $k, "value" => $v);
		//echo $v;
	}
}
echo json_encode($list);