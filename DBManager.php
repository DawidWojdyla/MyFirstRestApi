<?php 

require_once("MyDB.php");
require_once('constants.php');

class DBManager extends MyDB {
	
	function __construct($host, $user, $password, $dbName, $dbType= 'mysql', $charset ='utf8'){
		$this -> dbo = $this -> initDB($host, $user, $password, $dbName, $dbType= 'mysql', $charset ='utf8');
	}


	function checkCredentials($username, $password) {
		if (!$this -> dbo) {
			return SERVER_ERROR;
		}
		
		$passwordLength = strlen($password);

		if ($username != 'uzytkownik' || $passwordLength < 5 || $passwordLength > 30 ){
			return INVALID_CREDENTIALS;
		}

		$query = $this -> dbo -> prepare("SELECT `password` FROM `users` WHERE `username`=:username");
		$query -> bindValue(':username', $username, PDO::PARAM_STR);

		if (!$query -> execute()) {
			return SERVER_ERROR;
		}
		
		if (!$result = $query -> fetch(PDO::FETCH_NUM)){
			SERVER_ERROR;
		}
		
		if (!password_verify($password, $result[0])) {
			return INVALID_CREDENTIALS;
		}
		
		return ACTION_OK;
	}


	function addNewNickname($nickname) {
		
		//adding nickname to database

	}

	function getLastInsertedData(){

	//getting last inserted data
	}
}
	
?>