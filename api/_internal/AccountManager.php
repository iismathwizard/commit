<?php
	$path = $_SERVER['DOCUMENT_ROOT'];
	$path .= "/api/_internal/SQLManager.php";
	include_once($path);

	class AccountManager
	{
		public static function createAccount($email, $password)
		{
			$returnValue = "";

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$CreateUser))
			{
				$randId = rand();

				$command->bind_param("sis", $email, $randId, AccountManager::secHash($password));

				if($command->execute())
				{
					$returnValue = AccountManager::login($email, $password);
				}

				$command->close();
			}

			$SQL->close();

			return $returnValue;
		}

		public static function login($email, $password)
		{
			$returnSessionId = "";

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$Login))
			{
				$command->bind_param("ss", $email, AccountManager::secHash($password));
				$command->execute();
				$command->bind_result($accountid);

				if($command->fetch())
				{
					$SQLSessionMaker = SQLManager::createSQLConnection();

					if($sessionInsert = $SQLSessionMaker->prepare(SQLQueries::$CreateSession))
					{
						$session = rand();
						$sessionInsert->bind_param("ii", $accountid, $session);
						$sessionInsert->execute();
						$sessionInsert->close();

						$returnSessionId = $session;
					}

					$SQLSessionMaker->close();
				}

				$command->close();
			}

			$SQL->close();

			return $returnSessionId;
		}

		public static function logout($id)
		{
			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$Logout))
			{
				$command->bind_param("i", $id);
				$command->execute();
				$command->close();
			}

			$SQL->close();
		}

		public static function getAccountId($sessionid)
		{
			$returnSessionId = "";

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetAccountId))
			{
				$command->bind_param("s", $sessionid);
				$command->execute();
				$command->bind_result($returnSessionId);
				$command->fetch();
				$command->close();
			}

			$SQL->close();

			return $returnSessionId;
		}

		public static function getEmailFromAccountId($userid)
		{
			$returnEmail = "";

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetEmailFromAccountId))
			{
				$command->bind_param("i", $userid);
				$command->execute();
				$command->bind_result($returnEmail);
				$command->fetch();
				$command->close();
			}

			$SQL->close();

			return $returnEmail;
		}

		public static function getAccountIdFromEmail($email)
		{
			$returnId = "";

			$SQL = SQLManager::createSQLConnection();

			if($command = $SQL->prepare(SQLQueries::$GetAccountIdFromEmail))
			{
				$command->bind_param("s", $email);
				$command->execute();
				$command->bind_result($returnId);
				$command->fetch();
				$command->close();
			}

			$SQL->close();

			return $returnId;
		}

		private static function secHash($value)
		{
			return hash("sha256", $value);
		}

		public static function generateRandomKey($length, $safeUIChars)
		{
			if($safeUIChars)
			{
				$valid_chars = "ABCDEFGHJKMNPQRSTUVWXY3456789";
			}
			else
			{
				$valid_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
			}

			// start with an empty random string
		    $random_string = "";

		    // count the number of chars in the valid chars string so we know how many choices we have
		    $num_valid_chars = strlen($valid_chars);

		    // repeat the steps until we've created a string of the right length
		    for ($i = 0; $i < $length; $i++)
		    {
		        // pick a random number from 1 up to the number of valid chars
		        $random_pick = mt_rand(1, $num_valid_chars);

		        // take the random character out of the string of valid chars
		        // subtract 1 from $random_pick because strings are indexed starting at 0, and we started picking at 1
		        $random_char = $valid_chars[$random_pick-1];

		        // add the randomly-chosen char onto the end of our string so far
		        $random_string .= $random_char;
		    }

		    // return our finished random string
		    return $random_string;
		}
	}
?>