<?php
header("Content-Type: application/json");       // definindo o tipo de resposta como Json
require_once 'config/Database.php';
require_once 'middleware/AuthMiddleware.php';

$database = new Database();     // instançiando o banco de dados
$db = $database-> getConnection();       // armazenando arquivo de conexão na variável 'db'

$request_uri = explode('?', $_SERVER['REQUEST_URI'], 2);

switch ($request_uri[0]) {      // utilizando 'switch' para verificar o 'endpoint' solicitado na url
    case '/users':
        require 'controllers/UserController.php';
        break;

    case '/coffee':
        AuthMiddleware::authenticate($db);
        require 'controllers/CoffeeController.php';
        break;

    case '/login':
        require 'controllers/AuthController.php';
        break;

    default:        // se nenhum endpoint for 'encotrado'
        http_response_code(404);        // é retornado o erro 404, indicando que a url não foi encontrada
        echo json_encode(array("message" => "Endpoint not found."));
        break;
}