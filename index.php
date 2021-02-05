<?php

header("Content-Type", "application/json; utf-8");

require_once('ResponseBuilder.php');
$responseBuilder = new ResponseBuilder();

if (isset($_GET['action']) && ($_GET['action'] == 'send_new_data' || $_GET['action'] == 'get_last_inserted_data'))  {
	
	try {
		
		$json = file_get_contents('php://input');
		
		//null is returned if the json cannot be decoded or if the encoded data is deeper
		if (is_null($data = json_decode($json))) {
			echo $responseBuilder  -> getErrorResponse("post data missing or wrong format");
			exit;
		}
		
		if (!array_key_exists('username', $data) || !array_key_exists('password', $data)) {
			echo $responseBuilder  -> getErrorResponse("post data missing");
			exit;
		}
		
		require_once('constants.php');
		require_once('DBManager.php');
		require_once('config.php');
		
		$dBManager = new dbManager($db_host, $db_user, $db_password , $db_name);

		switch($dBManager -> checkCredentials($data -> username, $data -> password)):
			case ACTION_OK:
				if ($_GET['action'] == 'get_last_inserted_data'):
					echo $dBManager -> getLastInsertedData();
					exit;
				else:		
					if (!array_key_exists('nickname', $data)):
						echo $responseBuilder  -> getErrorResponse("post data missing");
						exit;
					endif;
					switch ($dBManager -> addNewNickname($data -> nickname)):
						case ACTION_OK: 
								echo $responseBuilder  -> getSuccessResponse();
								exit;
						case SERVER_ERROR: 
								echo $responseBuilder  -> getErrorResponse("server error");
								exit;
						default: 
							echo $responseBuilder  -> getErrorResponse("unexpected error");
							exit;
					endswitch;		
				endif;
				break;
			case SERVER_ERROR: 
				echo $responseBuilder  -> getErrorResponse("server error");
				exit;
			case INVALID_CREDENTIALS :
				echo $responseBuilder  -> getErrorResponse("invalid credentials");
				exit;
			default :
				echo $responseBuilder  -> getErrorResponse("unexpected error");
				exit;
		endswitch;
		exit;
	} catch(Exception $e){
		echo $responseBuilder  -> getErrorResponse("server error");
		exit;
	}
}
echo $responseBuilder  -> getErrorResponse("bad request");
exit;
?>