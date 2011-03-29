<?php

try {
	
	require("Controller.php");
	$controller = new Controller();
	//$ing = filter_var($_GET["ingredient"], FILTER_SANITIZE_URL);
	$q = filter_var($_GET["query"], FILTER_SANITIZE_URL);
	$controller->fetch($q);
	//$controller->processRecipes();
	$controller->processData();
	$xml = $controller->toXML();
	if($xml == "" || $xml == "User is performing too many actions: please try again later") throw new Exception("ahhh");
	echo $xml;
		
} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
	exit();
}

?>