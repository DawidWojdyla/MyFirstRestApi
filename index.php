<?php
// malformed header from script 'index.php': Bad header: Content-Type
//header("Content-Type", "application/json; utf-8");

require_once('ResponseBuilder.php');
$responseBuilder = new ResponseBuilder();

if (isset($_GET['action']) && ($_GET['action'] == 'send_new_data' || $_GET['action'] == 'get_last_inserted_data')) {

    try {

        $json = file_get_contents('php://input');

        //null is returned if the json cannot be decoded or if the encoded data is deeper
        if (is_null($data = json_decode($json))) {
            echo $responseBuilder->getErrorResponse("post data missing or wrong format");
            exit;
        }

        if (!property_exists($data, 'username') || !property_exists($data, 'password')) {
            echo $responseBuilder->getErrorResponse("credential data missing");
            exit;
        }

        require_once('constants.php');
        require_once('DBManager.php');
        $config = require_once 'config.php';

        $dBManager = new DBManager($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);

        switch ($dBManager->checkCredentials($data->username, $data->password)):
            case ACTION_OK:
                if ($_GET['action'] == 'get_last_inserted_data'):
                    echo $dBManager->getLastInsertedData();
                    exit;
                else:
                    if (!property_exists($data, 'nickname')):
                        echo $responseBuilder->getErrorResponse("post data missing");
                        exit;
                    endif;
                    switch ($dBManager->addNewNickname($data->nickname)):
                        case ACTION_OK:
                            echo $responseBuilder->getSuccessResponse();
                            exit;
                        case ONLY_WHITESPACES_OR_NULL:
                            echo $responseBuilder->getErrorResponse("nickname is empty or contains only white characters");
                            exit;
                        case INVALID_DATA_LENGTH:
                            echo $responseBuilder->getErrorResponse("invalid nickname length");
                            exit;
                        case SERVER_ERROR:
                            echo $responseBuilder->getErrorResponse("server error");
                            exit;
                        case ACTION_FAILED:
                            echo $responseBuilder->getErrorResponse("nickname could not be saved");
                            exit;
                        default:
                            echo $responseBuilder->getErrorResponse("unexpected error");
                            exit;
                    endswitch;
                endif;
            case SERVER_ERROR:
                echo $responseBuilder->getErrorResponse("server error");
                exit;
            case ACTION_FAILED:
                echo $responseBuilder->getErrorResponse("credential verification failed");
                exit;
            case INVALID_CREDENTIALS :
                echo $responseBuilder->getErrorResponse("invalid credentials");
                exit;
            default :
                echo $responseBuilder->getErrorResponse("unexpected error");
        endswitch;
        exit;
    } catch (Exception $e) {
        echo $responseBuilder->getErrorResponse("server error");
        exit;
    }
}
echo $responseBuilder->getErrorResponse("bad request");
exit;