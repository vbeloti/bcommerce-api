<?php


require_once('./utils/movePhoto.php');

class ProductsController
{
    private $service;

    function __construct($service)
    {
        $this->service = $service;
    }

    function index($resource, $id, $type, $genre, $price)
    {

        $results = $this->service->index($resource, $id, $type, $genre, $price);
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
            echo json_encode(array("status" => "success", "message" => "Excluído com sucesso"));
        } else {
            echo json_encode(array("status" => "error", "message" => "ID não encontrado"));
            header('HTTP/1.1 400 Bad Request');
        }
    }

    function store($resource)
    {

        if (!empty($_POST['title']) && !empty($_POST['description']) && !empty($_POST['price'])  && !empty($_FILES['photo'])  && !empty($_POST['genre'])  && !empty($_POST['type'])) {
            $data = ['title' => $_POST['title'], 'description' => $_POST['description'], 'price' => $_POST['price'], 'photo' => $_FILES['photo'], 'genre' => $_POST['genre'], 'type' => $_POST['type'], 'featured' => $_POST['featured'] ?? null];
            $results = $this->service->store($resource, $data);
            if ($results) {
                echo $results;
            } else {
                echo (json_encode(['status' => 'error', 'data' => 'Erro ao cadastrar']));
                header('HTTP/1.1 400 Bad Request');
            }
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Falta dados para serem cadastrados']));
            header('HTTP/1.1 400 Bad Request');
        }
    }



    function update($resource, $id, $data)
    {

        $data = ['title' => $data['title'] ?? null, 'description' => $data['description'] ?? null, 'price' => $data['price'] ?? null, 'photo' => $_FILES['photo'] ?? null, 'genre' => $data['genre'] ?? null, 'type' => $data['type'] ?? null, 'featured' => $data['featured'] ?? null];
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
