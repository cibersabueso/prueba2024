<?php
/**
 * Controlador para manejar las solicitudes relacionadas con los usuarios.
 */
require_once '../Services/UserService.php';

class UserController {
    private $userService;
    /**
     * Constructor que inicializa el servicio de usuarios.
     */
    public function __construct() {
          // Inicialización del servicio
        $this->userService = new UserService();
    }

    public function register($request) {
        // Implementación del registro de usuario.
        try {
            $fullName = $request->fullName;
            $document = $request->document;
            $email = $request->email;
            $password = $request->password;
            $userType = $request->userType;

            // Validar que los datos necesarios están presentes
            if (empty($fullName) || empty($document) || empty($email) || empty($password)) {
                throw new Exception("Datos incompletos para el registro.");
            }

            // Llamar al servicio para registrar el usuario
            $this->userService->registerUser($fullName, $document, $email, $password, $userType);

            // Respuesta exitosa
            echo json_encode(["message" => "Usuario registrado con éxito"]);
        } catch (Exception $e) {
            // Manejo de errores
            http_response_code(400);
            echo json_encode(["error" => $e->getMessage()]);
        }
    }
}