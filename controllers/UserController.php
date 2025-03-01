    <?php
header("Content-Type: application/json"); // Define o tipo de resposta como JSON
require_once __DIR__ . '/../config/Database.php';       // Importa a configuração do banco de dados
require_once __DIR__ . '/../models/User.php';       // Importa o modelo de usuário

$database = new Database();     // Cria uma instância do banco de dados
$db = $database-> getConnection();       // Obtém a conexão com o banco de dados

$user = new User($db);      // Cria uma instância do modelo User    
$data = json_decode(file_get_contents("php://input"));      // Lê os dados da requisição em formato JSON

switch ($_SERVER['REQUEST_METHOD']) {       // Verifica o método da requisição
    case 'POST':
        if (!empty($data-> name) && !empty($data-> password)) {        // Verifica se os dados necessários foram enviados
            $user-> name = $data->name; // Atribui o nome ao objeto User
            $user-> password = $data-> password;        // Atribui a senha ao objeto User

            if ($user->userExists()) {      // Verifica se o usuário já existe
                http_response_code(400);
                echo json_encode(array("message" => "Usuário já existe"));
            } elseif ($user-> create()) {
                http_response_code(201);
                echo json_encode(array("message" => "Usuário criado com sucesso"));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível criar usuário"));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados imcompletos"));
        }
        break;

    case 'PUT':

        break;
        //
    case 'DELETE':
        // 
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método inválido"));
        break;
}