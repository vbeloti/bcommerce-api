<?php

function connection()
{
    $config = json_decode(file_get_contents("./.env.json"));

    try {
        $connection = new PDO("mysql:host={$config->host};port={$config->port};dbname={$config->database}", $config->user, $config->password);
        return $connection;
    } catch (Exception $error) {
        echo "Ocorreu o seguinte erro: {$error->getMessage()}";
    }
}
