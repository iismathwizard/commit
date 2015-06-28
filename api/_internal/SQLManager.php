<?php
	class SQLManager
	{
		public static function createSQLConnection()
		{
			return new mysqli("localhost", "root", "", "main");
		}
	}
	class SQLQueries
	{
		public static $CreateUser = "INSERT INTO accounts VALUES (?, ?, ?)";
		public static $Login = "SELECT accountid FROM accounts WHERE email = ? AND password = ?";
		public static $Logout = "DELETE FROM sessions WHERE sessionid = ?";
		public static $CreateSession = "INSERT INTO sessions VALUES (?, ?)";
		public static $GetAccountId = "SELECT accountid FROM sessions WHERE sessionid = ?"; //sessionid -> accountid
		public static $GetEmailFromAccountId = "SELECT email FROM accounts WHERE accountid = ?";
		public static $CreateClass = "INSERT INTO classes VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"; //create a class
		public static $AddEmailToRegistrationList = "INSERT INTO registrationlist VALUES (?, ?, ?)"; //add an email account to the list of allowed accounts to register
		public static $SetPaypalId = "UPDATE registrationlist SET paymentid = ? WHERE email = ? AND classid = ?"; //when we want to assign a payment
		public static $AddUserToClassRegisters = "INSERT INTO classregisters VALUES (?, ?, ?, \"true\")"; //confirm this user as a part of this class
		public static $GetClassIdFromRegistrationKey = "SELECT classid FROM classes WHERE registrationkey = ?";
		public static $GetRegistrationKeyFromClassId = "SELECT registrationkey FROM classes WHERE classid = ?";
		public static $GetClassInfoFromId = "SELECT endregistrationdate, endpooldate, buyin, name, description, icon FROM classes WHERE classid = ?"; //Get class information where the class id equals something
		public static $GetClassIds = "SELECT classid, isowner FROM classregisters WHERE userid = ? AND valid = \"true\""; //get all classes a user is apart of or owning
		public static $GetNonOwnerUserIds = "SELECT userid FROM classregisters WHERE isowner = \"false\" AND valid = ? AND classid = ?"; //get the users that are apart of the class that aren't the owner
		public static $GetUserInRegistrationList = "SELECT email FROM registrationlist WHERE email = ? AND classid = ?"; //If something is returned we are on the whitelist for this class with this email
		public static $GetAllUserRegistrations = "SELECT classid, paymentid FROM registrationlist WHERE email = ?";
		public static $RemoveUserFromClass = "DELETE FROM classregisters WHERE classid = ? AND userid = ?";
		public static $InvalidateUserFromClass = "UPDATE classregisters SET valid = \"false\" WHERE classid = ? AND userid = ?";
		public static $RemoveRegistration = "DELETE FROM registrationlist WHERE paymentid = ?";
		public static $GetChargeFromClassId = "SELECT buyin FROM classes WHERE classid = ?";
		public static $GetAccountIdFromEmail = "SELECT accountid FROM accounts WHERE email = ?";
		public static $RemoveClass = "DELETE FROM classes WHERE classid = ?";
		public static $RemoveClassListings = "DELETE FROM classregisters WHERE classid = ?";
	}
?>