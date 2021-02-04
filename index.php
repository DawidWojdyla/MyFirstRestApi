<?php


if (isset($_GET['action']) && ($_GET['action'] == 'send_new_data' || $_GET['action'] == 'get_last_inserted_data'))  {
	
	require_once('DBManager.php');
	require_once('constants.php');
	
	$dBManager = new dbManager("localhost",  "root", "" , "davidowsky_androidDemo");
		
	if ($dBManager -> checkCredentials($data -> username, $data -> password) == ACTION_OK) {
		echo "LOGGED";
	} else {
		echo "BAD CREDENTIALS"
	}
	exit();
	
	
	
} else {
	
	echo "bad request";
	exit();
}
?>