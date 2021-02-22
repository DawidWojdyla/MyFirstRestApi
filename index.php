<?php

declare(strict_types=1);

require_once('ResponseBuilder.php');

if (isset($_GET['action']) && ($_GET['action'] == 'send_new_data' || $_GET['action'] == 'get_last_inserted_data')) {

    try {

        $json = file_get_contents('php://input');

        if (is_null($data = json_decode($json))) {
            echo ResponseBuilder::getErrorResponse("post data missing or wrong format");
            exit;
        }

        if (!property_exists($data, 'username') || !property_exists($data, 'password')) {
            echo ResponseBuilder::getErrorResponse("credential data missing");
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
                        echo ResponseBuilder::getErrorResponse("post data missing");
                        exit;
                    endif;

                    if ($data->nickname === null):
                        echo ResponseBuilder::getErrorResponse("nickname can not be null");
                        exit;
                    endif;

                    switch ($dBManager->addNewNickname($data->nickname)):
                        case ACTION_OK:
                            echo ResponseBuilder::getSuccessResponse();
                            exit;
                        case ONLY_WHITESPACES_OR_NULL:
                            echo ResponseBuilder::getErrorResponse("nickname is empty or contains only white characters");
                            exit;
                        case INVALID_DATA_LENGTH:
                            echo ResponseBuilder::getErrorResponse("invalid nickname length");
                            exit;
                        case SERVER_ERROR:
                            echo ResponseBuilder::getErrorResponse("server error");
                            exit;
                        case ACTION_FAILED:
                            echo ResponseBuilder::getErrorResponse("nickname could not be saved");
                            exit;
                        default:
                            echo ResponseBuilder::getErrorResponse("unexpected error");
                            exit;
                    endswitch;
                endif;
            case SERVER_ERROR:
                echo ResponseBuilder::getErrorResponse("server error");
                exit;
            case ACTION_FAILED:
                echo ResponseBuilder::getErrorResponse("credential verification failed");
                exit;
            case INVALID_CREDENTIALS :
                echo ResponseBuilder::getErrorResponse("invalid credentials");
                exit;
            default :
                echo ResponseBuilder::getErrorResponse("unexpected error");
        endswitch;
        exit;
    } catch (Exception $e) {
        echo ResponseBuilder::getErrorResponse("server error");
        exit;
    }
}
echo ResponseBuilder::getErrorResponse("bad request");
exit;