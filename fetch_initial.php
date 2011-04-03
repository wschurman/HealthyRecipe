<?php

try {
	
	require("Controller.php");
	$controller = new Controller();
	//$ing = filter_var($_GET["ingredient"], FILTER_SANITIZE_URL);
	$q = filter_var($_GET["query"], FILTER_SANITIZE_URL);
	$controller->fetch($q);
	$controller->processData();
	$xml = $controller->toXML();
	if($xml == "" || $xml == "User is performing too many actions: please try again later") throw new Exception("ahhh");
	$controller->insertIntoExist();
	//echo $xml;
	
	// for use later, will echo and parse atom instead of recipe xml
	echo $controller->toAtom();
		
} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
	exit();
}

?>