<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/AccountManager.php";
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["email"]) && isset($_GET["password"]))
	{
		$email = $_GET["email"];
		$password = $_GET["password"];

		$result = AccountManager::createAccount($email, $password);

		if($result != "")
		{
			$resultArr["status"] = "true";
			$resultArr["data"] = array(
				"sessionid" => $result
			);
		}
	}

	echo json_encode($resultArr);
?>
