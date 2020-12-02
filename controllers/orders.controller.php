<?php

class OrdersController
{
    private $service;

    function __construct($service)
    {
        $this->service = $service;
    }

    function index($resource, $id = null)
    {

        $results = $this->service->index($resource, $id);
        if ($results) {
            echo (json_encode(['status' => 'success', 'data' => $results]));
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Nenhum registro encontrado']));
            header('HTTP/1.1 404 Not Found');
        }
    }

    function store($resource)
    {
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data) {
            $results = $this->service->store($resource, $data);
            if ($results) {
                echo (json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso']));
            } else {
                echo (json_encode(['status' => 'error', 'data' => 'Erro ao cadastrar']));
                header('HTTP/1.1 400 Bad Request');
            }
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Falta dados para serem cadastrados']));
            header('HTTP/1.1 400 Bad Request');
        }
    }
}
