<?php
header("Content-Type: application/json");
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../models/User.php';

$database = new Database();
$db = $database-> getConnection();

$user = new User($db);
$data = json_decode(file_get_contents("php://input"));

if (!empty($data-> name) && !empty($data-> password)) {
    $user->name = $data-> name;
    $user->password = $data-> password;

    $user_id = $user-> login();
    if ($user_id) {
        http_response_code(200);
        echo json_encode(array("message" => "Login bem-sucedido.", "user_id" => $user_id));
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Nome de usuário ou senha inválidos."));
    }
} else {
    http_response_code(400);
    echo json_encode(array("message" => "Nome de usuário ou senha ausente."));
}
