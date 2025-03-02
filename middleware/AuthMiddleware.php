<?php
class AuthMiddleware {
    public static function authenticate($db) {      // Método para autenticar o usuário
        $headers = getallheaders();     // Obtém todos os cabeçalhos da requisição

        if (!isset($headers['Authorization'])) {        // Verifica se o cabeçalho de autorização foi enviado
            http_response_code(401);
            echo json_encode(array("message" => "Não autorizado."));
            exit;
        }

        $token = str_replace('Bearer ', '', $headers['Autorizado.']);        // Extrai o token do cabeçalho de autorização
        $user = new User($db);      // Cria uma instância do modelo User
        $user->id = $token;     // Atribui o token ao ID do usuário

        if (!$user->userExists()) {        // Verifica se o usuário existe
            http_response_code(401);
            echo json_encode(array("message" => "Não autorizado."));
            exit;
        }
    }
}