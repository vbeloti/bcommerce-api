<?php
require_once('./database/connection.php');
require_once('./controllers/products.controller.php');
require_once('./services/product.service.php');
require_once('./utils/parseData.php');
require_once('./middlewares/auth.php');


function productsRoutes($method, $resource, $id, $type, $genre, $price)
{
    $productService = new ProductService(connection());
    $productsController = new ProductsController($productService);

    switch ($method) {
        case 'GET': return $productsController->index($resource, $id, $type, $genre, $price);
        case 'POST': {
            if (!auth()) return;
            return $productsController->store($resource);
        };
        case 'PUT': {
            if (!auth($id)) return;
                _parseData();
            return $productsController->update($resource, $id, $GLOBALS['_DATA']);
        }
        case 'DELETE': {
            if (!auth($id)) return;
            return $productsController->delete($resource, $id);
        }
        default:
            header('HTTP/1.1 405 Method Not Allowed');
            header('Allow: POST, PUT, DELETE, GET');
    }
}