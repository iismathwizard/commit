<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php";
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"]) && isset($_GET["classid"]))
	{
		$sessionid = $_GET["sessionid"];
		$classid = $_GET["classid"];

		$users = ClassManager::getUsers($sessionid, $classid);

		if(count($users) > 0)
		{
			$resultArr["status"] = "true";
			$resultArr["data"] = array
			(
				"users" => $users
			);
		}
	}

	echo json_encode($resultArr);
?>
