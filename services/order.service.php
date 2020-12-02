<?php

class OrderService
{
    private $connection;

    function __construct($connection) {
        $this->connection = $connection;
    }

    function index($resource, $idUser)
    {
        $sql = "SELECT * FROM {$resource} where id_user = :id_user ";
        $results = $this->connection->prepare($sql);
        $results->bindValue(':id_user', $idUser);
        $results->execute();
        return $results->fetchAll(PDO::FETCH_OBJ);
    }

    function store($resource, $dados)
    {
        $fields = implode(",", array_keys($dados));
        $values = trim(str_repeat('?, ', count($dados)), ', ');
        $sql = "INSERT INTO {$resource} ({$fields}) values ({$values})";
        $result =  $this->connection->prepare($sql);
        $result->execute(array_values($dados));
        return $result;
    }
}
