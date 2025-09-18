<?php
$hostname="192.168.4.250";    
$database="dbt_new";
$username="postgres";
$password="postgres";
$port='5432';
	try {
     	$read_db_two = new PDO("pgsql:host=$hostname;port=$port;dbname=$database", $username, $password);
	}catch(PDOException $e){
		die('db error');     
	}
?>