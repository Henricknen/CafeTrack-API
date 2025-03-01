<?php
header("Content-Type: application/json");
require_once '../config/Database.php';
require_once '../models/CoffeeLog.php';

$database = new Database();
$db = $database->getConnection();

$coffeeLog = new CoffeeLog($db);
$data = json_decode(file_get_contents("php://input"));

// Verifica o método da requisição
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        if (!empty($data->user_id)) {
            $coffeeLog->user_id = $data->user_id;

            // Registra o café
            if ($coffeeLog->logCoffee()) {
                http_response_code(201); // Created
                echo json_encode(array("message" => "Coffee logged."));
            } else {
                http_response_code(503); // Service Unavailable
                echo json_encode(array("message" => "Unable to log coffee."));
            }
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "Incomplete data."));
        }
        break;

    case 'GET':
        // Obtém a quantidade total de café
        if (isset($_GET['user_id'])) {
            $count = $coffeeLog->getCoffeeCount($_GET['user_id']);
            echo json_encode(array("coffee_count" => $count));
        } else {
            http_response_code(400); // Bad Request
            echo json_encode(array("message" => "User ID required."));
        }
        break;

    default:
        http_response_code(405); // Method Not Allowed
        echo json_encode(array("message" => "Method not allowed."));
        break;
}
?>
