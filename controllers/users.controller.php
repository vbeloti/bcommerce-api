<?php

class UsersController
{
    private $service;

    function __construct($service)
    {
        $this->service = $service;
    }

    function index($resource, $id)
    {

        $results = $this->service->index($resource, $id);
        if ($results) {
            echo (json_encode(['status' => 'success', 'data' => $results]));
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Nenhum registro encontrado']));
            header('HTTP/1.1 404 Not Found');
        }
    }

    function delete($resource, $id)
    {
        $result = $this->service->delete($resource, $id);

        if ($result) {
            echo $result;
        } else {
            echo json_encode(array("status" => "error", "message" => "ID não encontrado"));
            header('HTTP/1.1 400 Bad Request');
        }
    }

    function store($resource)
    {
        if (!empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['password'])) {
            $data = ['name' => $_POST['name'], 'email' => $_POST['email'], 'password' => $_POST['password'], 'avatar' => $_FILES['photo'] ?? null, 'type' => $_POST['type'] ?? null];
            $results = $this->service->store($resource, $data);
            
            echo $results;
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Falta dados para serem cadastrados']));
            header('HTTP/1.1 400 Bad Request');
        }
    }

    function update($resource, $id, $data)
    {
        $data = ['name' => $data['name'] ?? null, 'email' => $data['email'] ?? null, 'password' => $data['password'] ?? null, 'avatar' => $_FILES['photo'] ?? null, 'type' => $data['type'] ?? null];
        if ($data) {
            $results = $this->service->update($resource, $id, $data);
            if ($results) {
                echo $results;
            } else {
                echo (json_encode(['status' => 'error', 'message' => 'Erro ao cadastrar']));
                header('HTTP/1.1 400 Bad Request');
            }
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Falta dados para serem cadastrados']));
            header('HTTP/1.1 400 Bad Request');
        }
    }
}
