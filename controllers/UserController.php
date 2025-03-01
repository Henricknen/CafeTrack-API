<?php
header("Content-Type: application/json"); // Define o tipo de resposta como JSON
require_once __DIR__ . '/../config/Database.php'; // Corrigido: Caminho correto para o Database.php
require_once __DIR__ . '/../models/User.php'; // Corrigido: Caminho correto para o User.php
// require_once '../config/Database.php'; // Importa a configuração do banco de dados
// require_once '../models/User.php'; // Importa o modelo de usuário

$database = new Database(); // Cria uma instância do banco de dados
$db = $database->getConnection(); // Obtém a conexão com o banco de dados

$user = new User($db); // Cria uma instância do modelo User
$data = json_decode(file_get_contents("php://input")); // Lê os dados da requisição em formato JSON

// Verifica o método da requisição
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        // Verifica se os dados necessários foram enviados
        if (!empty($data->name) && !empty($data->password)) {
            $user->name = $data->name; // Atribui o nome ao objeto User
            $user->password = $data->password; // Atribui a senha ao objeto User

            // Verifica se o usuário já existe
            if ($user->userExists()) {
                http_response_code(400); // Código 400: Bad Request
                echo json_encode(array("message" => "User already exists."));
            } elseif ($user->create()) {
                // Cria o usuário e retorna uma resposta de sucesso
                http_response_code(201); // Código 201: Created
                echo json_encode(array("message" => "User created."));
            } else {
                http_response_code(503); // Código 503: Service Unavailable
                echo json_encode(array("message" => "Unable to create user."));
            }
        } else {
            http_response_code(400); // Código 400: Bad Request
            echo json_encode(array("message" => "Data is incomplete."));
        }
        break;

    case 'PUT':
        // Implementar edição de usuário
        break;

    case 'DELETE':
        // Implementar remoção de usuário
        break;

    default:
        http_response_code(405); // Código 405: Method Not Allowed
        echo json_encode(array("message" => "Method not allowed."));
        break;
}