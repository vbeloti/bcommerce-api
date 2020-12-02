<?php


require_once('./utils/movePhoto.php');

class CommentsController
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

    function store($resource, $id)
    {

        $data = json_decode(file_get_contents('php://input'), true);

        if (!empty($data['comment']) && !empty($data['product_id'])) {
            $results = $this->service->store($resource, $data);
            if ($results) {
                echo $results;
            } else {
                echo (json_encode(['status' => 'error', 'data' => 'Erro ao comentar']));
                header('HTTP/1.1 400 Bad Request');
            }
        } else {
            echo (json_encode(['status' => 'error', 'data' => 'Falta dados para serem cadastrados']));
            header('HTTP/1.1 400 Bad Request');
        }
    }
}
