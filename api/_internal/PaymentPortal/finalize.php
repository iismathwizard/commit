<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/ClassManager.php";
	include_once($path);

	use PayPal\Api\Payment;
	use PayPal\Api\PaymentExecution;

	if(isset($_GET["success"]) && isset($_GET["paymentId"]))
	{
		$success = $_GET["success"];
		$paymentId = $_GET["paymentId"];

		if($success)
		{
			$payment = Payment::get($paymentId, PaypalManager::$apiContext);

		    $execution = new PaymentExecution();
		    $execution->setPayerId($_GET['PayerID']);
	        $result = $payment->execute($execution, PaypalManager::$apiContext);
		}
	}
?>