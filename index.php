<?php

if ($json = json_decode(file_get_contents('php://input'), true)) {

    require_once "Request.php";
    require_once "Controller.php";

    $controller = new Controller();
    $controller->process(new Request($_GET, $json));

} else {
    echo "{'error': 'post data missing'}";
    exit;
}