<?php

/**
	Some quick/dirty functions/hacks to display content fast. Use $HTML[] to add content and mark it as GLOBAL
*/

/**
	Create HTML tables with rows and input fields fast
*/
function formHeader($title = ""){
	global $HTML;
	$HTML[] = <<<EOF
	<form action="" method="POST" enctype="multipart/form-data">
	<table class="form-table">
		<tr>
			<th colspan="2">{$title}
		</tr>
EOF;
}

function formFooter($submit = "Submit", $cancel = "Cancel"){
	global $HTML;
	$HTML[] = <<<EOF
		<tr>
			<td colspan="2">
				<input type="submit" name="submit" value="{$submit}" class="btn btn-primary">
				<!-- quick hack to back, does not work if person creates new window or writes it itself -->
				<input type="button" name="cancel" value="{$cancel}" class="btn" onClick="history.go(-1)">
		</tr>
EOF;
}

/**
	General purpose function to make quickly form elements
*/
function formField($title, $name, $type = "text", $value = "", $desc = "", $values = array()){
	global $HTML;
	if(empty($value)){
		// if we do not have preset value, take it from $_SESSION
		if(isset($_SESSION['POST'][$name])){
			$value = $_SESSION['POST'][$name];
		}
	}
	if($type == "textarea"){
		$HTML[] = <<<EOF
			<tr>
				<td colspan="2"><strong>{$title}</strong><br>
				<textarea name="{$name}" rows="15" class="form-control">{$value}</textarea>
			</tr>
EOF;
	} else if($type == "select"){
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td><select name="{$name}" class="form-control">
					<option value="">{$desc}	
EOF;
		foreach($values as $k => $v){
			$HTML[] = <<<EOF
					<option value="{$k}">{$v}
EOF;
		}
		$HTML[] = <<<EOF
					</select>
			</tr>	
EOF;
	} else if($type == "file"){
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td><input type="file" name="{$name}" id="{$name}">	
			</tr>
EOF;
	} else {
		$HTML[] = <<<EOF
			<tr>
				<td><strong>{$title}</strong>
				<td><input type="{$type}" name="{$name}" value="{$value}" placeHolder="{$desc}" class="form-control">
			</tr>
EOF;
	}
}
