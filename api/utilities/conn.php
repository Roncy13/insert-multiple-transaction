<?php
function getConnection() {
	require_once('./utilities/response.php');

	$connection = array(
		'servername' => "localhost",
		'username' => "root",
		'password' => "",
		'database' => 'mini_ecommerce',
	);
	
	$servername = $connection["servername"];
	$username =  $connection["username"];
	$password =  $connection["password"];
	$db =  $connection["database"];
	
	try {
			$conn = new PDO("mysql:host={$servername};dbname={$db}", $username, $password);
			// set the PDO error mode to exception
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			
			return $conn;
	} catch(Exception $e) {
			serverError($e->getMessage());
	}
}

?>