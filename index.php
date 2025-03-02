<?php
header("Content-Type: application/json");       // Define o tipo de resposta como Json
require_once 'config/Database.php';
require_once 'middleware/AuthMiddleware.php';

$database = new Database();     // Instançiando o banco de dados
$db = $database-> getConnection();       // Armazenando arquivo de conexão na variável 'db'

$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

switch ($request_uri[0]) {      // Utilizando 'switch' para verificar o 'endpoint' solicitado na url
    case '/api/users':
        require 'controllers/UserController.php';
        break;

    case '/api/coffee':
        AuthMiddleware::authenticate($db);
        require 'controllers/CoffeeController.php';
        break;

    case '/api/login':
        require 'controllers/AuthController.php';
        break;

    default:        // Se nenhum endpoint for 'encotrado'
        http_response_code(404);        // Será retornado o erro 404, indicando que a url não foi encontrada
        echo json_encode(array("message" => "Nenhum 'Endpoint' foi encotrado"));
        break;
}