<?php
require_once('./utils/movePhoto.php');

class ProductService
{
    private $connection;

    function __construct($connection)
    {
        $this->connection = $connection;
    }

    function index($resource, $id, $type, $genre, $price)
    {

        if (!empty($id)) $sql = "SELECT * FROM {$resource} WHERE id = :id";
        else $sql = "SELECT * FROM {$resource}";

        !empty($type) && $sql .= " WHERE type=:type";
        !empty($genre) && $sql .= " WHERE genre=:genre";
        !empty($price) && $sql .= " WHERE price <= :price";

        !empty($type) && !empty($genre) && $sql = "SELECT * FROM {$resource} WHERE type=:type AND genre=:genre";
        !empty($type) && !empty($genre) && !empty($price) && $sql = "SELECT * FROM {$resource} WHERE type=:type AND genre=:genre AND price <=:price";

        $results = $this->connection->prepare($sql);

        !empty($id) && $results->bindValue(':id', $id);
        !empty($type) && $results->bindValue(':type', $type);
        !empty($genre) && $results->bindValue(':genre', $genre);
        !empty($price) && $results->bindValue(':price', $price);

        $results->execute();
        return $results->fetchAll(PDO::FETCH_OBJ);
    }

    function store($resource, $data)
    {

        $headers = apache_request_headers();
        $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key')) ?? null;


        if (isset($decoded->type) && $decoded->type === 'client') {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['status' => 'error', 'message' => 'Você não pode realizar essa ação']);
        }

        $photo = movePhoto();

        $sql = "INSERT INTO {$resource} (title, description, price, photo, genre, type, id_user, featured) values (:title, :description, :price, :photo, :genre, :type, :id_user, :featured)";
        $result =  $this->connection->prepare($sql);
        $result->bindValue(':title', $data['title']);
        $result->bindValue(':description', $data['description']);
        $result->bindValue(':price', $data['price']);
        $result->bindValue(':photo', $photo);
        $result->bindValue(':genre', $data['genre']);
        $result->bindValue(':type', $data['type']);
        $result->bindValue(':id_user', $decoded->id);
        $result->bindValue(':featured', !empty($data['featured']) ? $data['featured'] : NULL);
        $result = $result->execute();

        if ($result) return json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso']);
    }

    function update($resource, $id, $data)
    {

        $headers = apache_request_headers();
        if (isset($headers['authorization'])) {
            $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key'));

            if (isset($decoded->type) && $decoded->type === 'client') {
                header('HTTP/1.1 400 Bad Request');
                return json_encode(['status' => 'error', 'message' => 'Você não pode realizar essa ação']);
            }
        }

        $sqlVerifyProduct = "SELECT * from products where id = :id";
        $resultVerify =  $this->connection->prepare($sqlVerifyProduct);
        $resultVerify->bindValue(':id', $id);
        $resultVerify->execute();
        $product = $resultVerify->fetch();

        if (!empty($data['photo'])) {
            if (file_exists("uploads/$product[photo]")) {
                unlink("uploads/$product[photo]");
            }
        }

        $sql = "UPDATE $resource SET ";
        !empty($data['title']) && $sql .= "title=:title,";
        !empty($data['description']) && $sql .= "description=:description,";
        !empty($data['price']) && $sql .= "price=:price,";
        !empty($data['photo']) && $sql .= "photo=:photo,";
        !empty($data['genre']) && $sql .= "genre=:genre,";
        !empty($data['type']) && $sql .= "type=:type,";
        !empty($data['featured']) && $sql .= "featured=:featured";
        $sql .= " WHERE id=:id";

        $sql = str_replace(', WHERE', ' WHERE', $sql);

        $result =  $this->connection->prepare($sql);

        !empty($data['title']) && $result->bindValue(':title', $data['title']);
        !empty($data['description']) && $result->bindValue(':description', $data['description']);
        !empty($data['price']) && $result->bindValue(':price', $data['price']);
        !empty($data['photo']) && $result->bindValue(':photo', $_FILES['photo']['full_name']);
        !empty($data['genre']) && $result->bindValue(':genre', $data['genre']);
        !empty($data['featured']) && $result->bindValue(':featured', $data['featured']);
        !empty($data['type']) && $result->bindValue(':type', $data['type']);

        $result->bindValue(':id', $id);
        $result = $result->execute();

        return json_encode(['status' => 'success', 'message' => 'Atualizado com sucesso']);
    }

    function delete($resource, $id)
    {

        $headers = apache_request_headers();
        if (isset($headers['authorization'])) {
            $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key'));

            if (isset($decoded->type) && $decoded->type === 'client') {
                header('HTTP/1.1 400 Bad Request');
                return json_encode(['status' => 'error', 'message' => 'Você não pode realizar essa ação']);
            }
        }

        $sql = "DELETE FROM $resource WHERE id= :id";
        $result =  $this->connection->prepare($sql);
        $result->bindValue(':id', $id);
        $result->execute();
        return $result;
    }
}
