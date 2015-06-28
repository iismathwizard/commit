<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php"; //includes accountmanager for us
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"])
		&& isset($_GET["emails"])
		&& isset($_GET["endRegistrationDate"])
		&& isset($_GET["endPoolDate"])
		&& isset($_GET["poolBuyin"])
		&& isset($_GET["name"])
		&& isset($_GET["description"]))
	{
		$sessionid = $_GET["sessionid"];
		$emails = explode(",", $_GET["emails"]); //split on ","

		if($_GET["emails"] == "")
		{
			$emails = array();
		}

		$endRegistrationDate = $_GET["endRegistrationDate"];
		$endPoolDate = $_GET["endPoolDate"];
		$poolBuyin = $_GET["poolBuyin"];
		$name = $_GET["name"];
		$description = $_GET["description"];
		$icon = isset($_GET["icon"]) ? $_GET["icon"] : "";

		ClassManager::createClass($sessionid, $emails, $endRegistrationDate, $endPoolDate, $poolBuyin, $name, $description, $icon);

		$resultArr["status"] = "true";
	}

	echo json_encode($resultArr);
?>