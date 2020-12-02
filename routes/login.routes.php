<?php
require_once('./database/connection.php');
require_once('./controllers/login.controller.php');
require_once('./services/login.service.php');


function loginRoutes($method, $resource)
{
    $loginService = new LoginService(connection());
    $loginController = new LoginController($loginService);

    switch ($method) {
        case 'POST': return $loginController->store($resource);
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: POST, PUT, DELETE, GET');
    }
}