<?php

require_once('./utils/encryptPass.php');
require_once('./utils/jwt.php');


class LoginService
{
    private $connection;

    function __construct($connection)
    {
        $this->connection = $connection;
    }

    function store($resource, $data)
    {
        $sqlVerifyEmail = "SELECT * from users where email = :email";
        $resultVerify =  $this->connection->prepare($sqlVerifyEmail);
        $resultVerify->bindValue(':email', $data['email']);
        $resultVerify->execute();
        $user = $resultVerify->fetch();

        if (!$user) {
            header('HTTP/1.1 401 Unauthorized');
            return json_encode(['status' => 'error', 'message' => 'Email não existe']);
        }

        if (password_verify($data['password'], $user['password'])) {
            $userEncode = json_encode([ 'id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'] , 'avatar' => $user['avatar'], 'type' => $user['type']]);
            $token = JWT::encode($userEncode, 'secret_server_key');

            return json_encode(['status' => 'success', 'id' => $user['id'] ?? null, 'name' => $user['name'] ?? null, 'email' => $user['email'] ?? null, 'avatar' => $user['avatar'] ?? null, 'type' => $user['type'] ?? null, 'token' => $token]);
        } else {
            header('HTTP/1.1 400 Bad Request');
            return json_encode(['status' => 'error', 'message' => 'Email ou Senha Inválidos']);
        }
    }
}
