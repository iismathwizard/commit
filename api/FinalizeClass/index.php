<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php"; //includes accountmanager for us
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"]) && isset($_GET["classid"]))
	{
		$sessionid = $_GET["sessionid"];
		$classid = $_GET["classid"];

		ClassManager::finishClass($classid);
		$resultArr["status"] = "true";
	}

	echo json_encode($resultArr);
?>