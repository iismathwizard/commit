<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php";
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"]) && isset($_GET["emails"]) && isset($_GET["classid"]))
	{
		$sessionid = $_GET["sessionid"];
		$emails = explode(",", $_GET["emails"]); //split on ","
		$classid = $_GET["classid"];

		ClassManager::addInvites($sessionid, $classid, $emails);
		
		$resultArr["status"] = "true";
	}

	echo json_encode($resultArr);
?>
