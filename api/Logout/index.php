<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/AccountManager.php";
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"]))
	{
		$sessionid = $_GET["sessionid"];

		AccountManager::logout($sessionid);

		$resultArr["status"] = "true";
	}

	echo json_encode($resultArr);
?>
