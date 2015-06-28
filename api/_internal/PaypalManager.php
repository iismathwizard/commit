<?php
	require __DIR__  . '/PayPal-PHP-SDK/autoload.php';

	$root = $_SERVER['DOCUMENT_ROOT'];
	$sqlPath= $root . "/api/_internal/SQLManager.php";
	include_once($sqlPath);

	use PayPal\Api\Amount;
	use PayPal\Api\Details;
	use PayPal\Api\Item;
	use PayPal\Api\ItemList;
	use PayPal\Api\Payer;
	use PayPal\Api\Payment;
	use PayPal\Api\RedirectUrls;
	use PayPal\Api\Transaction;
	use PayPal\Api\ExecutePayment;
	use PayPal\Api\PaymentExecution;

	PaypalManager::init();

	class PaypalManager
	{
		private static $clientid = "ASX0wzQikhUBUBv85Aq4LE9j9eoHaIG8MbnhbSbw32Ai1e71f8IZSvloM2ae-XXTYnxq4X2TXcapTPtG";
		private static $secret = "EK0Ihbs6pKG34luC8uB7PqR7f21fqKfVrHjXZsmc_NkSVKVUfFItj06raZ1E_gFVwrwRql2Kcon1Hnnp";
		private static $url = "https://api.sandbox.paypal.com/v1";
		private static $tokenAddr = "/oauth2/token";
		public static $apiContext = NULL;

		public static function init()
		{
			if(PaypalManager::$apiContext == NULL)
			{
				PaypalManager::$apiContext = new \PayPal\Rest\ApiContext(
				    new \PayPal\Auth\OAuthTokenCredential(
				        PaypalManager::$clientid,     // ClientID
				        PaypalManager::$secret      // ClientSecret
				    )
				);
			}
		}

		public static function createPaypalPayment($charge, $accountid, $classid)
		{
			$payer = new Payer();
			$payer->setPaymentMethod("paypal");

			$amount = new Amount();
			$amount->setCurrency("USD")
			    ->setTotal($charge);

			$item1 = new Item();
			$item1->setName('Commitment')
			    ->setCurrency('USD')
			    ->setQuantity(1)
			    ->setPrice($charge);

			$itemList = new ItemList();
			$itemList->setItems(array($item1));

			$transaction = new Transaction();
			$transaction->setAmount($amount)
    			->setItemList($itemList)
			    ->setDescription("Payment description")
			    ->setInvoiceNumber(uniqid());

			$baseUrl = "http://172.28.116.110";
			$redirectUrls = new RedirectUrls();
			$redirectUrls->setReturnUrl("$baseUrl/api/_internal/PaymentPortal/?success=true&accountid={$accountid}&classid={$classid}")
			    ->setCancelUrl("$baseUrl/api/_internal/PaymentPortal/?success=false&accountid={$accountid}&classid={$classid}");

			$payment = new Payment();
			$payment->setIntent("sale")
			    ->setPayer($payer)
			    ->setRedirectUrls($redirectUrls)
			    ->setTransactions(array($transaction));

			try
			{
			    $payment->create(PaypalManager::$apiContext);
			}
			catch (Exception $ex)
			{
			}

			return $payment;
		}

		public static function payout($classid)
		{
			$worth = ClassManager::getUserCountInClass($classid, true) * ClassManager::getChargeFromClassId($classid);
			$numberOfRemainingUsers = ClassManager::getUserCountInClass($classid, false);
			$payoutAmount = $worth / ($numberOfRemainingUsers != 0 ? $numberOfRemainingUsers : 1);

			$users = ClassManager::getUsers(0, $classid);

			// Create a new instance of Payout object
			$payouts = new \PayPal\Api\Payout();

			$senderBatchHeader = new \PayPal\Api\PayoutSenderBatchHeader();
			$senderBatchHeader->setSenderBatchId(uniqid())
    			->setEmailSubject("Your Commitment Payed Off!");

    		$header = $payouts->setSenderBatchHeader($senderBatchHeader);

    		foreach($users as $user)
    		{
    			$senderItem1 = new \PayPal\Api\PayoutItem();
				$senderItem1->setRecipientType('Email')
				    ->setNote('Congrats!')
				    ->setReceiver($user["email"])
				    ->setSenderItemId("item" . uniqid())
				    ->setAmount(new \PayPal\Api\Currency("{
				                        \"value\":\"{$payoutAmount}\",
				                        \"currency\":\"USD\"
				                    }"));

				$header->addItem($senderItem1);
    		}

    		try
    		{
    			$batchpayouts = $payouts->create(array(), PaypalManager::$apiContext);
    		}
    		catch(Exception $ez)
    		{
    			//welp fuck
    			print_r($ez);
    		}
		}
	}
?>