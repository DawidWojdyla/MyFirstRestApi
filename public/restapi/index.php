<?php

if ($post = json_decode(file_get_contents('php://input'), true)) {

    require_once "../../private/restapi/Request.php";
    require_once "../../private/restapi/Controller.php";

    $controller = new Controller();
    $controller->process(new Request($_GET, $post));

} else {
    echo "{'error': 'post data missing or wrong json format'}";
    exit;
}