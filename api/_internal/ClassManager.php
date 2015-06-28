<?php
	$root = $_SERVER['DOCUMENT_ROOT'];
	$accountPath = $root . "/api/_internal/AccountManager.php";
	$emailPath = $root . "/api/_internal/EmailManager.php";
	$paypalPath = $root . "/api/_internal/PaypalManager.php";
	include_once($accountPath);
	include_once($paypalPath);
	include_once($emailPath);

	class ClassManager
	{
		public static function createClass($sessionid, $emails, $endRegistrationDate, $endPoolDate, $poolBuyin, $name, $description, $icon)
		{
			$key = AccountManager::generateRandomKey(6, true);
			$classid = rand();
			$accountid = AccountManager::getAccountId($sessionid);

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$CreateClass))
			{
				$command->bind_param("iiississs", $accountid, $endRegistrationDate, $endPoolDate, $poolBuyin, $name, $classid, $description, $key, $icon);
				$command->execute();
				$command->close();
			}

			ClassManager::addInvites($sessionid, $classid, $emails);

			ClassManager::addUserToClassRegistration($accountid, $classid, true);

			$SQL->close();
		}

		public static function getClassIdFromRegistrationKey($key)
		{
			$classid = "";

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetClassIdFromRegistrationKey))
			{
				$command->bind_param("s", $key);
				$command->execute();
				$command->bind_result($classid);
				$command->fetch();
				$command->close();
			}

			$SQL->close();

			return $classid;
		}

		public static function getRegistrationKeyFromClassId($classid)
		{
			$key = "";
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetRegistrationKeyFromClassId))
			{
				$command->bind_param("i", $classid);
				$command->execute();
				$command->bind_result($key);
				$command->fetch();
				$command->close();
			}

			$SQL->close();

			return $key;
		}

		public static function addUserToClassRegistration($userid, $classid, $isOwner)
		{
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$AddUserToClassRegisters))
			{
				$owner = $isOwner ? "true" : "false";

				$command->bind_param("iis", $userid, $classid, $owner);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}

		/**
		* Standard entry point for regular users trying to join
		*/
		public static function attemptToJoinUserToClass($userid, $key)
		{
			$hadRight = false;

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetUserInRegistrationList))
			{
				$accountid = $userid;
				$email = AccountManager::getEmailFromAccountId($accountid);
				$classid = ClassManager::getClassIdFromRegistrationKey($key);

				$command->bind_param("si", $email, $classid);
				$command->execute();
				$command->bind_result($returnedEmail);

				if($command->fetch())
				{
					$hadRight = true;
					$charge = ClassManager::getChargeFromClassId($classid);
					$payment = PaypalManager::createPaypalPayment($charge, $accountid, $classid);
					ClassManager::updateRegistrationWithPaypal($classid, $email, $payment->getId());
					//EmailManager::emailWithPaypal($email, $payment->getApprovalLink());
					$hadRight = $payment->getApprovalLink();
				}

				$command->close();
			}

			$SQL->close();

			return $hadRight;
		}

		private static function updateRegistrationWithPaypal($classid, $email, $paymentid)
		{
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$SetPaypalId))
			{
				$command->bind_param("ssi", $paymentid, $email, $classid);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}

		public static function leaveClass($sessionid, $classid)
		{
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$RemoveUserFromClass))
			{
				$accountid = AccountManager::getAccountId($sessionid);

				$command->bind_param("ii", $classid, $accountid);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}

		public static function finishClass($classid)
		{
			PaypalManager::payout($classid);

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$RemoveClassListings))
			{
				$command->bind_param("i", $classid);
				$command->execute();
				$command->close();
			}

			if($command = $SQL->prepare(SQLQueries::$RemoveClass))
			{
				$command->bind_param("i", $classid);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}

		/**
		* gets the classes. Will check the payment status of pending registrations and update them
		*/
		public static function getClasses($sessionid)
		{
			$classes = array();

			$accountid = AccountManager::getAccountId($sessionid);

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetClassIds))
			{
				$command->bind_param("i", $accountid);
				$command->execute();
				$command->bind_result($classid, $isOwner);

				while($command->fetch())
				{
					array_push($classes, ClassManager::getClass($classid, $isOwner));
				}

				$command->close();
			}

			$SQL->close();

			return $classes;
		}

		private static function getClass($classid, $isOwner)
		{
			$arr = array();

			$InfoSQL = SQLManager::createSQLConnection();

			if($getInfoCommand = $InfoSQL->prepare(SQLQueries::$GetClassInfoFromId))
			{
				$getInfoCommand->bind_param("i", $classid);
				$getInfoCommand->execute();
				$getInfoCommand->bind_result($endRegistrationDate, $endPoolDate, $buyin, $name, $description, $icon);

				if($getInfoCommand->fetch())
				{
					$usersRemaining = ClassManager::getUserCountInClass($classid, false);
					$usersTotal = ClassManager::getUserCountInClass($classid, true);
					$totalValue = $buyin * $usersTotal;

					$arr = array(
						"name" => $name,
						"id" => $classid,
						"usersInPool" => $usersTotal,
						"usersInPoolRemaining" => $usersRemaining,
						"poolBuyin" => $buyin,
						"poolWorth" => $totalValue,
						"endRegistrationDate" => $endRegistrationDate,
						"endPoolDate" => $endPoolDate,
						"isOwner" => $isOwner,
						"icon" => $icon
					);
				}

				$getInfoCommand->close();
			}

			$InfoSQL->close();

			return $arr;
		}

		public static function getUserCountInClass($classid, $isTotal)
		{
			$count = 0;

			$SQL = SQLManager::createSQLConnection();

			if($isTotal)
			{
				if($command = $SQL->prepare(SQLQueries::$GetNonOwnerUserIds))
				{
					$valid = "false";
					$command->bind_param("si", $valid, $classid);
					$command->execute();
					$command->bind_result($userid);

					while($command->fetch())
					{
						$count ++;
					}

					$command->close();
				}
			}

			if($command = $SQL->prepare(SQLQueries::$GetNonOwnerUserIds))
			{
				$valid = "true";
				$command->bind_param("si", $valid, $classid);
				$command->execute();
				$command->bind_result($userid);

				while($command->fetch())
				{
					$count ++;
				}

				$command->close();
			}

			$SQL->close();

			return $count;
		}

		public static function getUsers($sessionid, $classid) //should check if session is owner. fuck it.
		{
			$users = array();

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetNonOwnerUserIds))
			{
				$valid = "false";
				$command->bind_param("si", $valid, $classid);
				$command->execute();
				$command->bind_result($userid);

				while($command->fetch())
				{
					$email = AccountManager::getEmailFromAccountId($userid);
					array_push($users, array
						(
							"email" => $email,
							"userid" => $userid,
							"valid" => $valid
						)
					);
				}

				$command->close();
			}

			if($command = $SQL->prepare(SQLQueries::$GetNonOwnerUserIds))
			{
				$valid = "true";
				$command->bind_param("si", $valid, $classid);
				$command->execute();
				$command->bind_result($userid);

				while($command->fetch())
				{
					$email = AccountManager::getEmailFromAccountId($userid);
					array_push($users, array
						(
							"email" => $email,
							"userid" => $userid,
							"valid" => $valid
						)
					);
				}

				$command->close();
			}

			$SQL->close();

			return $users;
		}

		public static function kickUser($sessionid, $classid, $userid)
		{
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$InvalidateUserFromClass))
			{
				$command->bind_param("ii", $classid, $userid);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}

		public static function getChargeFromClassId($classid)
		{
			$charge = 0;

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetChargeFromClassId))
			{
				$command->bind_param("i", $classid);
				$command->execute();
				$command->bind_result($charge);
				$command->fetch();
				$command->close();
			}

			$SQL->close();

			return $charge;
		}

		public static function addInvites($sessionid, $classid, $emails)
		{
			$key = ClassManager::getRegistrationKeyFromClassId($classid);

			$SQL = SQLManager::createSQLConnection();

			foreach($emails as $email)
			{
				if($command = $SQL->prepare(SQLQueries::$AddEmailToRegistrationList))
				{
					$paypal = "";
					$command->bind_param("sis", $email, $classid, $paypal);
					$command->execute();
					$command->close();

					EmailManager::emailWithKey($email, $key, AccountManager::GetAccountIdFromEmail($email));
				}
			}

			$SQL->close();
		}

		public static function removeRegistrationListing($paymentId)
		{
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$RemoveRegistration))
			{
				$command->bind_param("s", $paymentId);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}
	}
?>