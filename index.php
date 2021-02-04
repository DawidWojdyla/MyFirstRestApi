<?php


if (isset($_GET['action']) && ($_GET['action'] == 'send_new_data' || $_GET['action'] == 'get_last_inserted_data'))  {
	
	require_once('DBManager.php');
	require_once('constants.php');
	require_once('config.php');
	
	$dBManager = new dbManager($db_host, $db_user, $db_password , $db_name);
		
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