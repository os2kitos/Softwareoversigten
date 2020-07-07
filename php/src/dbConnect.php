<?php
// Connect to the DB backend //

	//require_once 'MDB2.php';
    require_once 'meekrodb.2.3.class.php';
	
    
    DB::$user = getenv('DB_USER') ;
    DB::$password = getenv('DB_PASSWORD');
    
    DB::$dbName = getenv('DB_NAME');
    DB::$host = getenv('DB_HOST');
    DB::$encoding = 'utf8'; 

?>
