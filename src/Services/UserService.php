/**
 * Servicio para manejar operaciones relacionadas con los usuarios, como el registro.
 */
    /**
     * Registra un nuevo usuario en el sistema.
     * @param string $fullName Nombre completo del usuario.
     * @param string $document Documento de identidad del usuario.
     * @param string $email Correo electrónico del usuario.
     * @param string $password Contraseña del usuario.
     * @param string $userType Tipo de usuario (común o comerciante).
     */

<?php
require_once '../Utils/Database.php';
require_once '../Models/User.php';

class UserService {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function registerUser($fullName, $document, $email, $password, $userType) {
        // Verificar si el documento o el correo ya existen
        $stmt = $this->db->prepare("SELECT * FROM users WHERE document = ? OR email = ?");
        $stmt->execute([$document, $email]);
        if ($stmt->fetch()) {
            throw new Exception("El documento o el correo electrónico ya están registrados.");
        }

        // Insertar el nuevo usuario
        $stmt = $this->db->prepare("INSERT INTO users (full_name, document, email, password, user_type, balance) VALUES (?, ?, ?, ?, ?, 0.00)");
        $stmt->execute([$fullName, $document, $email, password_hash($password, PASSWORD_DEFAULT), $userType]);
    }
}