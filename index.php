<?php

header('Content-Type: application/json'); // Asegura que todas las respuestas sean en formato JSON.

require __DIR__ . '/src/Middleware/AuthMiddleware.php'; // Incluye el middleware de autenticación

$authMiddleware = new App\Middleware\AuthMiddleware();

// Simulación básica de enrutamiento
$request = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

function sendErrorResponse($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode(["error" => $message]);
    exit; // Asegura que la ejecución del script se detenga después de enviar el error.
}

switch ($request) {
    case '/':
        require_once __DIR__ . '/../Utils/Database.php';
        break;
    case '/transfer':
        // Aplica el middleware de autenticación solo para esta ruta
        $authMiddleware->handle($request);
        
        if ($requestMethod == 'POST') {
            require __DIR__ . '/src/Controllers/TransactionController.php';
            $controller = new TransactionController();
            $data = json_decode(file_get_contents('php://input'), true);
            if (!$data) {
                sendErrorResponse("Datos inválidos o faltantes", 400);
            }
            $controller->transfer($data);
        } else {
            sendErrorResponse("Método no permitido", 405);
        }
        break;
    
    default:
        sendErrorResponse("Página no encontrada", 404);
        break;
}
