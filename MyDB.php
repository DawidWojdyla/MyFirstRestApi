<?php

class MyDB
{
    public static function initDB($host, $user, $password, $dbName, $dbType, $charset)
    {
        $dsn = "$dbType:host=$host;dbname=$dbName;charset=$charset";
        $options = [
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];

        try {

            $dbo = new PDO($dsn, $user, $password, $options);

        } catch (PDOException $e) {
            return null;
        }
        return $dbo;
    }
}