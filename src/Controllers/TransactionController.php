<?php
require_once '../Services/TransactionService.php';
require_once '../Utils/Sanitizer.php';
require_once '../Utils/CsrfProtector.php'; 
use App\Utils\Sanitizer;
use App\Utils\CsrfProtector;

class TransactionController {
    private $transactionService;

    /**
     * Controlador para manejar las solicitudes de transacciones.
     */
    public function __construct() {
        // Inicializa el servicio de transacciones.
        $this->transactionService = new TransactionService();
    }

    /**
     * Maneja la solicitud entrante y delega a la acción correspondiente basada en el método HTTP.
     */
    public function handleRequest() {
        // Implementación del manejo de solicitudes.
        $method = $_SERVER['REQUEST_METHOD'];
        switch ($method) {
            case 'POST':
                $data = json_decode(file_get_contents('php://input'), true);
                if ($data) {
                    // Verificar el token CSRF antes de procesar los datos
                    CsrfProtector::verifyToken($data['csrf_token']);
                    
                    $data = $this->sanitizeData($data);
                    $this->transfer($data);
                } else {
                    http_response_code(400); // Solicitud incorrecta
                    echo json_encode(["error" => "Datos inválidos o faltantes"]);
                }
                break;
            default:
                http_response_code(405); // Método no permitido
                echo json_encode(["error" => "Método no permitido"]);
                break;
        }
    }

    /**
     * Método público para manejar la transferencia de fondos.
     * Asumiendo que $data es un array que contiene los datos necesarios.
     */
    public function transfer($data) {
        try {
            $payerId = $data['payer'];
            $payeeId = $data['payee'];
            $amount = $data['value'];

            // Validar que los datos necesarios están presentes
            if (empty($payerId) || empty($payeeId) || empty($amount)) {
                throw new Exception("Datos incompletos para la transferencia.");
            }

            // Llamar al servicio de transferencia
            $this->transactionService->transfer($payerId, $payeeId, $amount);

            // Respuesta exitosa
            http_response_code(200); // OK
            echo json_encode(["message" => "Transferencia realizada con éxito"]);
        } catch (Exception $e) {
            // Manejo de errores
            http_response_code(400); // Solicitud incorrecta
            echo json_encode(["error" => $e->getMessage()]);
        }
    }

    /**
     * Sanitiza los datos de entrada utilizando la clase Sanitizer.
     */
    private function sanitizeData($data) {
        foreach ($data as $key => $value) {
            $data[$key] = Sanitizer::sanitizeInput($value);
        }
        return $data;
    }
}

$controller = new TransactionController();
$controller->handleRequest();