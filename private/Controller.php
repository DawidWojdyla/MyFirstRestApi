<?php

declare(strict_types=1);

require_once "ResponseBuilder.php";

class Controller
{
    private ResponseBuilder $responseBuilder;

    public function __construct()
    {
        $this->responseBuilder = new ResponseBuilder();
    }

    public function process(Request $request): void
    {
        $action = $request->getAction();

        if ($action !== "get_last_inserted_data" && $action !== "send_new_data") {
            $this->getErrorResponseAndExit("bad request");
        }

        if (is_null($json = $request->getPostData())) {
            $this->getErrorResponseAndExit("post data missing");
        }

        if (!array_key_exists('username', $json) || !array_key_exists('password', $json)) {
            $this->getErrorResponseAndExit("credential data missing");
        }

        try {
            require_once "DBManager.php";
            $config = require_once 'config.php';
            $dbManager = new DBManager($config['db_host'], $config['db_user'], $config['db_password'], $config['db_name']);
            require_once "constants.php";
            if ($this->areCredentialsValid($dbManager, $json['username'], $json['password'])) {
                if ($action === "get_last_inserted_data") {
                    echo $dbManager->getLastInsertedData($this->responseBuilder);
                    exit;
                } else if (!array_key_exists('nickname', $json)) {
                    $this->getErrorResponseAndExit("post data missing");
                } else if ($json['nickname'] === null) {
                    $this->getErrorResponseAndExit("nickname can not be null");
                } else {
                    $this->saveNickname($dbManager, $json['nickname']);
                }
            }
        } catch (Exception $e) {
            $this->getErrorResponseAndExit("server error");
        }
    }

    private function areCredentialsValid(DBManager $dbManager, string $username, string $password): bool
    {
        switch ($dbManager->checkCredentials($username, $password)):
            case ACTION_OK:
                return true;
            case SERVER_ERROR:
                $this->getErrorResponseAndExit("server error");
                break;
            case ACTION_FAILED:
                $this->getErrorResponseAndExit("credential verification failed");
                break;
            case INVALID_CREDENTIALS :
                $this->getErrorResponseAndExit("invalid credentials");
                break;
            default :
                $this->getErrorResponseAndExit("unexpected error");
        endswitch;
        return false;
    }

    private function saveNickname(DBManager $dbManager, string $nickname): void
    {
        switch ($dbManager->addNewNickname($nickname)) {
            case ACTION_OK:
                echo $this->responseBuilder->getSuccessResponse();
                exit;
            case ONLY_WHITESPACES_OR_NULL:
                $this->getErrorResponseAndExit("nickname is empty or contains only white characters");
                break;
            case INVALID_DATA_LENGTH:
                $this->getErrorResponseAndExit("invalid nickname length");
                break;
            case SERVER_ERROR:
                $this->getErrorResponseAndExit("server error");
                break;
            case ACTION_FAILED:
                $this->getErrorResponseAndExit("nickname could not be saved");
                break;
            default:
                $this->getErrorResponseAndExit("unexpected error");
        }
    }

    private function getErrorResponseAndExit(string $message): void
    {
        echo $this->responseBuilder->getErrorResponse($message);
        exit;
    }
}