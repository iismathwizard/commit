<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$filepath = $path;
	$filepath .= "/api/_internal/ClassManager.php";
	include_once($filepath);

	use PayPal\Api\Payment;
	use PayPal\Api\PaymentExecution;

	if(isset($_GET["success"]) && isset($_GET["paymentId"]) && isset($_GET["accountid"]) && isset($_GET["classid"]))
	{
		$success = $_GET["success"];
		$paymentId = $_GET["paymentId"];
		$accountid = $_GET["accountid"];
		$classid = $_GET["classid"];

		if($success)
		{
			$payment = Payment::get($paymentId, PaypalManager::$apiContext);

		    $execution = new PaymentExecution();
		    $execution->setPayerId($_GET['PayerID']);
	        $result = $payment->execute($execution, PaypalManager::$apiContext);

			ClassManager::addUserToClassRegistration($accountid, $classid, false);
		}

		ClassManager::removeRegistrationListing($paymentId);
	}

	header("location: ../../../../index.html");
?>