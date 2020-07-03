<?php
// Connect to the DB backend //

	//require_once 'MDB2.php';
    require_once 'meekrodb.2.3.class.php';
	
    
    DB::$user = 'kitos';
    DB::$password = 'kitos';
    
    DB::$dbName = 'kitos';
    DB::$host = 'localhost';
    DB::$encoding = 'utf8'; 

?>