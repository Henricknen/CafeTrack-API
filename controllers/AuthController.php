<?php
header("Content-Type: application/json"); // Define o tipo de resposta como JSON
require_once __DIR__ . '/../config/Database.php'; // Corrigido: Caminho correto para o Database.php
require_once __DIR__ . '/../models/User.php'; // Corrigido: Caminho correto para o User.php

// require_once '/.../config/Database.php'; // Importa a configuração do banco de dados
// require_once '../models/User.php'; // Importa o modelo de usuário

$database = new Database(); // Cria uma instância do banco de dados
$db = $database->getConnection(); // Obtém a conexão com o banco de dados

$user = new User($db); // Cria uma instância do modelo User
$data = json_decode(file_get_contents("php://input")); // Lê os dados da requisição em formato JSON

// Verifica se os dados necessários foram enviados
if (!empty($data->name) && !empty($data->password)) {
    $user->name = $data->name; // Atribui o nome ao objeto User
    $user->password = $data->password; // Atribui a senha ao objeto User

    // Tenta autenticar o usuário
    $user_id = $user->login();
    if ($user_id) {
        http_response_code(200); // Código 200: OK
        echo json_encode(array("message" => "Login successful.", "user_id" => $user_id));
    } else {
        http_response_code(401); // Código 401: Unauthorized
        echo json_encode(array("message" => "Invalid username or password."));
    }
} else {
    http_response_code(400); // Código 400: Bad Request
    echo json_encode(array("message" => "Missing username or password."));
}
