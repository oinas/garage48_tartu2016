<?php

if ($db = new SQLite3('data/data.db')) {
// first let the engine check table, and create it eventualy
	@$db->exec('CREATE TABLE IF NOT EXISTS users 
	(id id, 
	user VARCHAR(255), 
	password VARCHAR(255), 
	first VARCHAR(255), 
	last VARCHAR(255),  
	email VARCHAR(255),  
	facebookid VARCHAR(255),  
	googleid VARCHAR(255),  
	date VARCHAR(255),  
	update VARCHAR(255),  
	status VARCHAR(255),  
	description VARCHAR(255),  
	banstatus VARCHAR(255), 
	PRIMARY KEY (id))');

	$db->exec("INSERT INTO users 
		(bar) 
		VALUES 
		('This is a test')");
}
