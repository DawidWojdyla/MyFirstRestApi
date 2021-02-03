<?php


if (isset($_GET['action']) && ($_GET['action'] == 'send_new_data' || $_GET['action'] == 'get_last_inserted_data'))  {
	
	echo "OK";
	exit();
	
	
} else {
	
	echo "bad request";
	exit();
}
?>