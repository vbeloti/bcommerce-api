<?php

include_once('./routes/products.routes.php');
include_once('./routes/users.routes.php');
include_once('./routes/login.routes.php');
include_once('./routes/comments.routes.php');

 if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, DELETE, PUT, PATCH, OPTIONS');
        header('Access-Control-Allow-Headers: authorization, Content-Type');
        header('Access-Control-Max-Age: 1728000');
        header('Content-Length: 0');
        header('Content-Type: text/plain');
        die();
    }

    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');


// header('Content-Type: application/x-www-form-urlencoded');
// header('Content-Type: multipart/form-data');

$method = $_SERVER['REQUEST_METHOD'];
$url = !empty($_GET['url']) ? explode('/', trim($_GET['url'])) : '/';
$resource = $url[0];
$id = $url[1] ?? null;

if ($resource === '/') echo (json_encode(['status' => 'success', 'message' => 'API Is Working']));

if ($_GET) {
    switch ($resource) {
        case 'products':
            $type = isset($_GET['type']) ? $_GET['type'] : null;
            $genre = isset($_GET['genre']) ? $_GET['genre'] : null;
            $price = isset($_GET['price']) ? $_GET['price'] : null;
            return productsRoutes($method, $resource, $id, $type, $genre, $price);
        case 'users':
            return usersRoutes($method, $resource, $id);
        case 'login':
            return loginRoutes($method, $resource);
        case "comments":
            return commentsRoutes($method, $resource, $id);
        // case 'orders':
        //     return ordersRoutes($method, $resource, $id);
        default:
            return header('HTTP/1.1 404 NOT FOUND');
    }
}
