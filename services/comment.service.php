<?php

class CommentService
{
    private $connection;

    function __construct($connection) {
        $this->connection = $connection;
    }

    function index($resource, $id)
    {
        $sql = "SELECT A.*, A.id, B.avatar, B.name FROM comments A LEFT JOIN users B ON A.user_id = B.id WHERE product_id = :id ORDER BY created DESC";
        $results = $this->connection->prepare($sql);
        $results->bindValue(':id', $id);
        $results->execute();
        return $results->fetchAll(PDO::FETCH_OBJ);
    }

    function store($resource, $data)
    {
        $sqlVerifyProduct = "SELECT * from products where id = :id";
        $resultVerify = connection()->prepare($sqlVerifyProduct);
        $resultVerify->bindValue(':id', $data['product_id']);
        $resultVerify->execute();
        $product = $resultVerify->fetch();
  
        if(!$product) {
           header('HTTP/1.1 406 Not Acceptable');
           return json_encode(['status' => 'error', 'message' => 'Produto não existe']);
        }

        $headers = apache_request_headers();
        if (isset($headers['authorization'])) {
            $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key'));
        }

        $sql = "INSERT INTO {$resource} (comment, user_id, product_id) VALUES (:comment, :user_id, :product_id)";
        $result =  $this->connection->prepare($sql);
        $result->bindValue(':comment', $data['comment']);
        $result->bindValue(':user_id', $decoded->id ?? null);
        $result->bindValue(':product_id', $data['product_id']);
        $result = $result->execute();

        if ($result) return json_encode(['status' => 'success', 'message' => 'Comentário cadastrado com sucesso']);
    }

    function delete($resource, $id)
    {
        $sqlVerifyComment = "SELECT * from comments where id = :id";
        $resultVerify = connection()->prepare($sqlVerifyComment);
        $resultVerify->bindValue(':id', $id);
        $resultVerify->execute();
        $comment = $resultVerify->fetch();
  
        if(!$comment) {
           header('HTTP/1.1 404 Not Found');
           return json_encode(['status' => 'error', 'message' => 'Comentário não existe']);
        }

        $headers = apache_request_headers();
        if (isset($headers['authorization'])) {
            $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key'));

            if (isset($decoded->id) && $decoded->id !== $comment['user_id']) {
                header('HTTP/1.1 401 Unauthorized');
                return json_encode(['status' => 'error', 'message' => 'Você não tem autorização!!!']);
            }
        }

        $sql = "DELETE FROM $resource WHERE id = :id";
        $result =  $this->connection->prepare($sql);
        $result->bindValue(':id', $id);
        $result->execute();

        if ($result) return json_encode(['status' => 'success', 'message' => 'Comentário removido com sucesso']);
    }
}
