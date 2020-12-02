<?php
require_once('./database/connection.php');
require_once('./controllers/users.controller.php');
require_once('./services/user.service.php');
require_once('./utils/parseData.php');



function usersRoutes($method, $resource, $id)
{
    $userService = new UserService(connection());
    $usersController = new UsersController($userService);

    switch ($method) {
        case 'GET': return $usersController->index($resource, $id);
        case 'POST': return $usersController->store($resource);
        case 'PUT': {
            if (!auth($id, false)) return;
                _parseData();
            return $usersController->update($resource, $id, $GLOBALS['_DATA']);
        }
        case 'DELETE': return $usersController->delete($resource, $id);
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: POST, PUT, DELETE, GET');
    }
}