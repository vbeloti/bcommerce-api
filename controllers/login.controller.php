<?php

class LoginController
{
    private $service;

    function __construct($service)
    {
        $this->service = $service;
    }

    function store($resource)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if (!empty($data['email']) && !empty($data['password'])) {
            $results = $this->service->store($resource, $data);
            echo $results;
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Falta dados para serem autenticados']));
            header('HTTP/1.1 400 Bad Request');
        }
    }
}
