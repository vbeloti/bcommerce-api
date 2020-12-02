<?php

require_once('./database/connection.php');
require_once('./utils/jwt.php');


function auth($id = null, $product = true)
{
   $headers = apache_request_headers();

   if (isset($headers['authorization'])) {

      $decoded = json_decode(JWT::decode($headers['authorization'], 'secret_server_key'));

      if ($decoded->id) {
         if ($product && isset($decoded->id) && $id !== null && $decoded->id !== $id) {
            $sqlVerifyProduct = "SELECT * from products where id = :id";
            $resultVerify = connection()->prepare($sqlVerifyProduct);
            $resultVerify->bindValue(':id', $id);
            $resultVerify->execute();
            $product = $resultVerify->fetch();

            if (!$product) {
               header('HTTP/1.1 406 Not Acceptable');
               echo json_encode(['status' => 'error', 'message' => 'Produto não existe']);
               return;
            }

            if ($product && $decoded->id !== $product['id_user']) {
               header('HTTP/1.1 401 Unauthorized');
               echo json_encode(['status' => 'error', 'message' => 'Você não tem autorização!!!']);
               return;
            }
         }

         // if (!$product && isset($decoded->id) && $decoded->id !== $id) {
         //    header('HTTP/1.1 401 Unauthorized');
         //    echo json_encode(['status' => 'error', 'message' => 'Você não tem autorização!!!']);
         //    return;
         // }

         return true;
      } else {
         header('HTTP/1.1 401 Unauthorized');
         echo json_encode(['status' => 'error', 'message' => 'Acesso Negado!!!']);
         return;
      }
   } else {
      header('HTTP/1.1 401 Unauthorized');
      echo json_encode(['status' => 'error', 'message' => 'Acesso Negado!!!']);
      return;
   }
}
