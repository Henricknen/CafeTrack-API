    <?php
header("Content-Type: application/json");
require_once '../config/Database.php';
require_once '../models/CoffeeLog.php';

$database = new Database();
$db = $database-> getConnection();

$coffeeLog = new CoffeeLog($db);
$data = json_decode(file_get_contents("php://input"));

switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (!empty($data-> user_id)) {
            $coffeeLog-> user_id = $data-> user_id;

            if ($coffeeLog-> logCoffee()) {     // Registra o café
                http_response_code(201);
                echo json_encode(array("message" => "Café registrado."));
            } else {
                http_response_code(503);
                echo json_encode(array("message" => "Não foi possível registrar o café."));
            }
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "Dados incompletos."));
        }
        break;

    case 'GET':        
        if (isset($_GET['user_id'])) {      // Obtém a quantidade total de café
            $count = $coffeeLog-> getCoffeeCount($_GET['user_id']);
            echo json_encode(array("coffee_count" => $count));
        } else {
            http_response_code(400);
            echo json_encode(array("message" => "ID do usuário obrigatório."));
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(array("message" => "Método não permitido."));
        break;
}
?>
