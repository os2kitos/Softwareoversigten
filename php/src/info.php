<?php print("Local");


require_once 'dbConnect.php';
$mysqli_results_systems = DB::query("SELECT Name FROM `cached_data`");

?>

