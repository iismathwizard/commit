<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php"; //includes accountmanager for us
	include_once($path);

	$resultArr = array(
		"status" => "false",
		"data" => array()
	);

	if(isset($_GET["sessionid"]))
	{
		$sessionid = $_GET["sessionid"];
		$classes = ClassManager::getClasses($sessionid);

		if(count($classes) != 0)
		{
			$resultArr["status"] = "true";
			$resultArr["data"] = array
			(
				"classes" => $classes
			);
		}
	}

	echo json_encode($resultArr);
?>