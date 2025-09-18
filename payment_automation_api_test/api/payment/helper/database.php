<?php
	//production
	$hostname="192.168.4.250";    
	$database="dbt";
	$username="postgres";
	$password="postgres";
	$port='5432';
	
	try {
     	$db = new PDO("pgsql:host=$hostname;port=$port;dbname=$database", $username, $password);
     	//$kmut_verification_db = new PDO("pgsql:host=$hostname;port=$port;dbname=$database1", $username, $password);
	}catch(PDOException $e){
		print_r($e);
		die('db error');
	}


// 	Proxy server information
// $proxyHost = "proxy_hostname";
// $proxyPort = "proxy_port";
// $proxyDatabase = "proxy_database";

// // PostgreSQL database information
// $hostname = "actual_db_hostname";    
// $database = "actual_db_name";
// $username = "actual_db_username";
// $password = "actual_db_password";
// $port = 'actual_db_port';

// try {
//     // Configure the connection to the proxy server
//     $proxyDsn = "pgsql:host=$proxyHost;port=$proxyPort;dbname=$proxyDatabase";
//     $proxyUsername = "proxy_username";
//     $proxyPassword = "proxy_password";

//     $proxyDb = new PDO($proxyDsn, $proxyUsername, $proxyPassword);

//     // Establish the connection to the actual PostgreSQL database through the proxy
//     $actualDsn = "pgsql:host=$hostname;port=$port;dbname=$database";
//     $db = new PDO($actualDsn, $username, $password);
// } catch (PDOException $e) {
//     print_r($e);
//     die('db error');
// }
?>