<?php

	/**
		Text based DATABASE that handles column names, insertions, deletions and modifications automatically.
		
		Make sure you use ID as first column always, otherwise it does not work
	*/
	
class DB {

	public $table;			// this will be database name, converted into data/$DB$.txt
	public $columns;		// list of columns, selected from first row
	public $data;			// data is stored here

    public function __construct(array $arg = array()) {
        if (!empty($arg)) {
            foreach ($arg as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
        if(isset($arg['mode']) && $arg['mode'] == "createDB"){
        	// create database
        	$this->columns = $arg['columns'];
        	$this->createTable();
        }
        $this->loadTable();
    }
    
    public function sortByDate(){
    	$tmp = $this->data;
    	$this->data = array();
    	
    	foreach($tmp as $k => $v){
    		$this->data[$v['date'] . "." . $k] = $v;
    	}
    	krsort($this->data);
    }
    
    public function createTable(){
    	$fh = fopen("data/" . $this->table . ".txt", "wb+");
    	fwrite($fh, "#" . implode("\03", $this->columns) . "\n");
    	fclose($fh);
    }
    
    public function loadTable(){
    	$f = file("data/" . $this->table . ".txt");
    	foreach($f as $r){
    		$r = trim($r);
    		if($r[0] == "#"){
    			// this is our columns row
    			$r = substr($r, 1);
    			$this->columns = explode("\03", $r);
    		} else {
    			$row = array();
    			$col = explode("\03", $r);
    			$i = 0;
    			foreach($this->columns as $k => $v){
    				if(isset($col[$i])){
    					$row[$v] = $col[$i];
    				}
    				$i++;
    			}
    			$this->data[] = $row;
    		}
    	}
    }
    
    public function updateColumns(array $arg = array()){
		$fh = file("data/" . $this->table . ".txt", "wb+");
		if(empty($arg)){
			fwrite($fh, implode("\03", $this->columns) . "\n");
		} else {
			fwrite($fh, implode("\03", $arg) . "\n");
		}
		foreach($data as $k => $v){
			fwrite($fh, implode("\03", $v) . "\n");
		}
		fclose($fh);
    }
    
    public function addEntry(array $arg = array()){
    	/** if ID define, use updateEntry */
    	if(isset($arg['id'])){
    		if($arg['id'] == "add"){
    			unset($arg['id']);
    		} else {
    			return $this->updateEntry($arg);
    		}
    	}
    	/** get id */
    	$max_id = 0;
    	if(isset($this->data)){
			foreach($this->data as $k => $v){
				if($v['id'] > $max_id){
					$max_id = (int) $v['id'];
				}
			}
    	}
    	/** get correct order */
    	$arg['id'] = ($max_id + 1);
    	$row = array();
    	foreach($this->columns as $k => $v){
    		if(isset($arg[$v])){
    			$row[] = str_replace(array("\03", "\n"), array("", "\04"), $arg[$v]);
    		} else {
    			$row[] = "";
    		}
    	}
    	// append file
    	$fh = fopen("data/" . $this->table . ".txt", "ab+");
    	fwrite($fh, implode("\03", $row) . "\n");
    	fclose($fh);
    	return $arg['id'];    	
    }
    
    public function updateEntry(array $arg = array()){
    	/** get current state of entry */
    	$current = $this->getEntry($arg);
    	/** get correct order */
    	$row = array();
    	foreach($this->columns as $k => $v){
    		if(isset($arg[$v])){
    			$row[] = str_replace(array("\03", "\n"), array("", "\04"), $arg[$v]);
    		} else {
    			/** check if the entry is there */
    			if(isset($current[$v])){
    				$row[] = $current[$v];
    			} else {
    				$row[] = "";
    			}
    		}
    	}
    	/** get position */
    	$found = false;
    	if(isset($arg['id']) && isset($this->data)){
    		foreach($this->data as $k => $v){
    			if(isset($v['id']) && $v['id'] == $arg['id']){
    				$this->data[$k] = $row;
    				$found = true;
    				break;
    			}
    		}
    	}
    	// rewrite file
    	if($found){
			$fh = fopen("data/" . $this->table . ".txt", "wb+");
			fwrite($fh, "#" . implode("\03", $this->columns) . "\n");
			foreach($this->data as $k => $v){
				fwrite($fh, implode("\03", $v) . "\n");
			}
			fclose($fh);
			return $arg['id'];
    	} else {
    		/** cant find entry, add it 
    		$arg['id'] = "add";
    		return $this->addEntry($arg);*/
    		return false;
    	}
    }
    
    public function deleteEntry(array $arg = array()){
    	$found = false;
    	if(isset($arg['id']) && isset($this->data)){
    		foreach($this->data as $k => $v){
    			if(isset($v['id']) && $v['id'] == $arg['id']){
    				unset($this->data[$k]);
    				$found = true;
    				break;
    			}
    		}
    	}
    	// rewrite file
    	if($found){
			$fh = fopen("data/" . $this->table . ".txt", "wb+");
			fwrite($fh, "#" . implode("\03", $this->columns) . "\n");
			foreach($this->data as $k => $v){
				fwrite($fh, implode("\03", $v) . "\n");
			}
			fclose($fh);
			return true;
    	} else {
    		return false;
    	}
    }
    
    public function getEntry(array $arg = array()){
    	if(isset($arg['id']) && isset($this->data)){
    		foreach($this->data as $k => $v){
    			if(isset($v['id']) && $v['id'] == $arg['id']){
    				return $v;
    			}
    		}
    	}
    	return array();
    }
    
    public function getEntries(array $arg = array()){
    	return $this->data;
    }

}

/*
Examples how to use it

$DB = new DB(array("table" => "test", "mode" => "createDB", "columns" => array("id", "title", "more", "new")));
$DB = new DB(array("table" => "test"));

// hack to add extra column
$DB->columns[] = "new_column";
$DB->updateColumns();

$DB->addEntry(
	array(
		"title" => "Dont delete entry",
		"more" => "Nice info\nCan it handle this?",
		"new" => "Very good"
		)
	);
	
$DB->updateEntry(
	array(
		"id" => "2",
		"title" => "This is updated",
		"new" => "Very good"
		)
	);
	
$DB->deleteEntry(
	array(
		"id" => "7"
		)
	);

//$DB = new DB(array("table" => "test"));

print_r($DB);*/

