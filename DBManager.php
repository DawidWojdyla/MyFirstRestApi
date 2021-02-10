<?php 

require_once('constants.php');
require_once('ResponseBuilder.php');
require_once("MyDB.php");

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

		if (!$this -> dbo) {
			return SERVER_ERROR;
		}
		
		$nicknameTrim = trim($nickname);
		if ($nicknameTrim == "") {
			return ONLY_WHITESPACES;
		}
		
		$nicknameLength = strlen(trim($nicknameTrim));
		
		if (nicknameLength < 1 && nicknameLength > 30) {
			return INVALID_DATA_LENGTH;
		}

		$query = $this -> dbo -> prepare ("INSERT INTO `nicknames` VALUES (NULL, :nickname, NOW())");
		$query -> bindValue (':nickname', $nicknameTrim, PDO::PARAM_STR);
		
		if (!$query -> execute()){ 
			return SERVER_ERROR;
		}
		return ACTION_OK;
	}

	function getLastInsertedData(){

		if (!$this -> dbo) {
			$responseBuilder = new ResponseBuilder();
			return  $responseBuilder -> getErrorResponse("server error");
		}
		
		$data = array();
		
		if ($query = $this -> dbo -> prepare ("SELECT `id`, `nickname`, `date` FROM `nicknames` ORDER BY `id` DESC LIMIT 1")) {
			if ($query -> execute()) { 
				$data = $query -> fetch(PDO::FETCH_ASSOC);
			}
		}
		
		return json_encode($data);
	}
}
	
?>