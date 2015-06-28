<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php";
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"]) && isset($_GET["userid"]) && isset($_GET["classid"]))
	{
		$sessionid = $_GET["sessionid"];
		$userid = $_GET["userid"];
		$classid = $_GET["classid"];

		ClassManager::kickUser($sessionid, $classid, $userid);
		$resultArr["status"] = "true";
	}

	echo json_encode($resultArr);
?>
