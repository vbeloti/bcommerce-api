<?php

require_once('./utils/encryptPass.php');
require_once('./utils/movePhoto.php');

class UserService
{
    private $connection;

    function __construct($connection) {
        $this->connection = $connection;
    }

    function store($resource, $data)
    {
        $sqlVerifyEmail = "SELECT * from users where email = :email";
        $resultVerify =  $this->connection->prepare($sqlVerifyEmail);
        $resultVerify->bindValue(':email', $data['email']);
        $resultVerify->execute();
        $emailExists = $resultVerify->fetch();

        if($emailExists) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['status' => 'error', 'message' => 'Email já cadastrado']);
        }

        $sql = "INSERT INTO {$resource} (name, email, password, avatar, type) values (:name, :email, :password, :avatar, :type)";
        $result =  $this->connection->prepare($sql);
        $result->bindValue(':name', $data['name']);
        $result->bindValue(':email', $data['email']);
        $result->bindValue(':password', encryptPass($data['password']));
        $result->bindValue(':avatar', !empty($data['avatar']) ? movePhoto() : NULL);
        $result->bindValue(':type', !empty($data['type']) ? $data['type'] : 'client');
        $result = $result->execute();

        if ($result) {
            header('HTTP/1.1 201 Created');
            return json_encode(['status' => 'success', 'message' => 'Cadastro realizado com sucesso']);
        }
    }

    function update($resource, $id, $data)
    {

        $sqlVerifyUser = "SELECT * from users where id = :id";
        $resultVerify =  $this->connection->prepare($sqlVerifyUser);
        $resultVerify->bindValue(':id', $id);
        $resultVerify->execute();
        $user = $resultVerify->fetch();

        if(!$user) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['status' => 'error', 'message' => 'Usuário não existe']);
        }

        if(!empty($data['avatar'])) {
            if (file_exists("uploads/$user[avatar]")) {
                unlink("uploads/$user[avatar]");
            }
        }

        $sql = "UPDATE $resource SET ";
        !empty($data['name']) && $sql .= "name=:name,";
        !empty($data['email']) && $sql .= "email=:email,";
        !empty($data['password']) && $sql .= "password=:password,";
        !empty($data['avatar']) && $sql .= "avatar=:avatar,";
        !empty($data['genre']) && $sql .= "genre=:genre,";
        !empty($data['type']) && $sql .= "type=:type";
        $sql .= " WHERE id=:id";
        
        $sql = str_replace(', WHERE', ' WHERE', $sql);
        
        $result =  $this->connection->prepare($sql);
        
        !empty($data['name']) && $result->bindValue(':name', $data['name']);
        !empty($data['email']) && $result->bindValue(':email', $data['email']);
        !empty($data['password']) && $result->bindValue(':password', encryptPass($data['password']));
        !empty($data['avatar']) && $result->bindValue(':avatar', $_FILES['photo']['full_name']);
        !empty($data['genre']) && $result->bindValue(':genre', $data['genre']);
        !empty($data['type']) && $result->bindValue(':type', $data['type']);

        $result->bindValue(':id', $id);
        $result = $result->execute();

        return json_encode(['status' => 'success', 'message' => 'Atualizado com sucesso']);
    }

    function delete($resource, $id)
    {
        $sqlVerifyUser = "SELECT * from users where id = :id";
        $resultVerify =  $this->connection->prepare($sqlVerifyUser);
        $resultVerify->bindValue(':id', $id);
        $resultVerify->execute();
        $user = $resultVerify->fetch();

        if(!$user) {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['status' => 'error', 'message' => 'Usuário não existe']);
        }

        $headers = apache_request_headers();
        if (isset($headers['authorization'])) {
            $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key'));

            if (isset($decoded->id) && $decoded->id !== $user['id']) {
                header('HTTP/1.1 400 Bad Request');
                return json_encode(['status' => 'error', 'message' => 'Você não tem autorização']);
            }
        }

        $sql = "DELETE FROM $resource WHERE id= :id";
        $result =  $this->connection->prepare($sql);
        $result->bindValue(':id', $id);
        $result->execute();

        if($result) return json_encode(array("status" => "success", "message" => "Excluído com sucesso"));;
    }
}
