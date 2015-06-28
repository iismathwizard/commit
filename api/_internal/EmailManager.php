<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/SendGrid/sendgrid-php.php";
	include_once($path);

	class EmailManager
	{
		private static $api_name = "commit-app";
		private static $api_key = "0ursuperspookykey";

		public static function emailWithKey($emailaddr, $key, $userid)
		{
			$sendgrid = new SendGrid(EmailManager::$api_name, EmailManager::$api_key);
			$email = new SendGrid\Email();
			$email
			    ->addTo($emailaddr)
			    ->setFrom('noreply@commitapp.com')
			    ->setSubject('You\'re Invited to a Pool!')
			    ->setHtml("<a href=\"http://172.28.116.110/api/JoinClass/?userid={$userid}&invitekey={$key}\">Confirm Registration</a>"); //TODO; change to registration portal

			$sendgrid->send($email);
		}

		public static function emailWithPaypal($emailaddr, $link)
		{
			$sendgrid = new SendGrid(EmailManager::$api_name, EmailManager::$api_key);
			$email = new SendGrid\Email();
			$email
			    ->addTo($emailaddr)
			    ->setFrom('noreply@commitapp.com')
			    ->setSubject('Your Commitment')
			    ->setHtml("<a href=\"{$link}\">Link to paypal!</a>");

			$sendgrid->send($email);
		}

		public static function emailFinalize($emailaddr, $link)
		{
			$sendgrid = new SendGrid(EmailManager::$api_name, EmailManager::$api_key);
			$email = new SendGrid\Email();
			$email
			    ->addTo($emailaddr)
			    ->setFrom('noreply@commitapp.com')
			    ->setSubject('Your Commitment has paid off!')
			    ->setHtml("<a href=\"{$link}\">Collect reward!</a>");

			$sendgrid->send($email);
		}
	}
?>