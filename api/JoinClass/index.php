<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php";
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	$result = false;

	if((isset($_GET["sessionid"]) || isset($_GET["userid"])) && isset($_GET["invitekey"]))
	{
		$userid = 0;
		
		if(isset($_GET["sessionid"]))
		{
			$userid = AccountManager::getAccountId($_GET["sessionid"]);
		}
		else
		{
			$userid = $_GET["userid"];
		}

		$invitekey = $_GET["invitekey"];

		$result = ClassManager::attemptToJoinUserToClass($userid, $invitekey);

		if($result != false)
		{
			$resultArr["status"] = "true";
		}
	}

	if(isset($_GET["sessionid"]))
	{
		echo json_encode($resultArr);
	}
	else
	{
		header("location: " . $result);
	}
?>