<?php

require_once('ResponseBuilder.php');
require_once("MyDB.php");

class DBManager
{
    private $dbo;

    function __construct($host, $user, $password, $dbName, $dbType = 'mysql', $charset = 'utf8')
    {
        $this->dbo = MyDB::initDB($host, $user, $password, $dbName, $dbType, $charset);
    }

    function checkCredentials($username, $password)
    {
        if (!$this->dbo) {
            return SERVER_ERROR;
        }

        $passwordLength = mb_strlen($password, 'UTF-8');

        if ($username != 'uzytkownik' || $passwordLength < 5 || $passwordLength > 30) {
            return INVALID_CREDENTIALS;
        }

        $query = $this->dbo->prepare("SELECT `password` FROM `users` WHERE `username`=:username");
        $query->bindValue(':username', $username);

        if (!$query->execute()) {
            return ACTION_FAILED;
        }

        if (!$result = $query->fetch(PDO::FETCH_NUM)) {
            return ACTION_FAILED;
        }

        if (!password_verify($password, $result[0])) {
            return INVALID_CREDENTIALS;
        }

        return ACTION_OK;
    }

    function addNewNickname($nickname)
    {
        if (!$this->dbo) {
            return SERVER_ERROR;
        }

        $nicknameTrim = trim($nickname);
        if ($nicknameTrim == "") {
            return ONLY_WHITESPACES_OR_NULL;
        }

        $nicknameLength = mb_strlen($nicknameTrim, 'UTF-8');

        if ($nicknameLength < 1 || $nicknameLength > 30) {
            return INVALID_DATA_LENGTH;
        }

        $query = $this->dbo->prepare("INSERT INTO `nicknames` VALUES (NULL, :nickname, NOW())");
        $query->bindValue(':nickname', $nicknameTrim);

        if (!$query->execute()) {
            return ACTION_FAILED;
        }
        return ACTION_OK;
    }

    function getLastInsertedData()
    {
        if (!$this->dbo) {
            $responseBuilder = new ResponseBuilder();
            return $responseBuilder->getErrorResponse("server error");
        }

        $data = array();

        if ($query = $this->dbo->prepare("SELECT `id`, `nickname`, `date` FROM `nicknames` ORDER BY `id` DESC LIMIT 1")) {
            if ($query->execute()) {
                $data = $query->fetch(PDO::FETCH_ASSOC);
            }
        }

        return json_encode($data);
    }
}
