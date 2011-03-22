<?php

try {
	
	require("Controller.php");
	$controller = new Controller();
	$ing = filter_var($_GET["ingredient"], FILTER_SANITIZE_URL);
	$q = filter_var($_GET["query"], FILTER_SANITIZE_URL);
	$controller->fetch($ing, $q);
	$controller->processData();
	
	
} catch (Exception $e) {
	header("HTTP/1.0 500 Internal Server Error");
	echo $e->getMessage();
	exit();
}

?>