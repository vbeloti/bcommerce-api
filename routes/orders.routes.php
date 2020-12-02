<?php
require_once('./controllers/orders.controller.php');


function ordersRoutes($method, $resource, $id)
{
    switch ($method) {
        case 'GET': return index($resource, $id);
        case 'POST': return store($resource);
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: POST, PUT, DELETE, GET');
    }
}