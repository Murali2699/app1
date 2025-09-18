<?php
	//production
    $hostname="xxx";    
	$database="xxx";
	$username="xxx";
 	$password="xxx";
	$port='xxx';
	try {
     	$read_static_db = new PDO("pgsql:host=$hostname;port=$port;dbname=$database", $username, $password);
	}catch(PDOException $e){
		die('db error');     
	}
?>