<?php
class MyDB
{
	private $dbo = null;
  
	function __construct($host, $user, $password, $dbName, $dbType = 'mysql', $charset = 'utf8') {
		$this -> dbo = $this -> initDB($host, $user, $password, $dbName, $dbType = 'mysql', $charset = 'utf8');
	}
  
	function initDB($host, $user, $password, $dbName, $dbType, $charset) {
		$dsn = "$dbType:host=$host;dbname=$dbName;charset=$charset";
		$options = [
			PDO::ATTR_EMULATE_PREPARES => false,
			PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
		];
			
		try {
			
			$dbo = new PDO($dsn, $user, $password, $options);
			
		} catch (PDOException $e) {
			
			echo "Connection Error: " . $e -> getMessage();
			
		}
		
		return $dbo;
	}
}
?>