<?php
require_once '../Utils/Database.php';
require_once '../Models/User.php';

use Exception;
use PDOException;

class TransactionService {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function transfer($payerId, $payeeId, $amount) {
        try {
            $this->db->beginTransaction();

            if ($amount <= 0) {
                throw new Exception("El monto de la transferencia debe ser positivo.");
            }

            $payer = $this->getUser($payerId);
            $payee = $this->getUser($payeeId);

            if ($payer->userType === 'merchant') {
                throw new Exception("Los comerciantes no pueden enviar dinero.");
            }

            if ($payer->balance < $amount) {
                throw new Exception("Saldo insuficiente.");
            }

            if (!$this->checkExternalAuthorization()) {
                throw new Exception("Autorización externa fallida.");
            }

            $this->updateBalance($payerId, -$amount);
            $this->updateBalance($payeeId, $amount);

            if (!$this->sendNotification($payeeId)) {
                throw new Exception("Fallo al enviar notificación.");
            }

            $this->db->commit();
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error en operación de base de datos: " . $e->getMessage());
            throw new Exception("Error al procesar la transacción en la base de datos.");
        } catch (Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    private function getUser($userId) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch(PDO::FETCH_OBJ);
            if (!$user) {
                throw new Exception("Usuario no encontrado.");
            }
            return $user;
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            throw new Exception("Error al obtener información del usuario.");
        }
    }

    private function updateBalance($userId, $amount) {
        try {
            $stmt = $this->db->prepare("UPDATE users SET balance = balance + ? WHERE id = ?");
            $stmt->execute([$amount, $userId]);
        } catch (PDOException $e) {
            error_log("Error al actualizar saldo: " . $e->getMessage());
            throw new Exception("Error al actualizar el saldo del usuario.");
        }
    }

    private function checkExternalAuthorization() {
        try {
            $url = "https://run.mocky.io/v3/1f94933c-353c-4ad1-a6a5-a1a5ce2a7abe";
            $response = file_get_contents($url);
            $data = json_decode($response);
            if ($data->message !== "Autorizado") {
                throw new Exception("Autorización externa no concedida.");
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al verificar autorización externa: " . $e->getMessage());
            throw new Exception("Fallo al consultar el servicio de autorización externa.");
        }
    }

    private function sendNotification($userId) {
        try {
            $url = "https://run.mocky.io/v3/6839223e-cd6c-4615-817a-60e06d2b9c82";
            $response = file_get_contents($url);
            $data = json_decode($response);
            if ($data->message !== "Notificación enviada") {
                throw new Exception("Fallo al enviar la notificación al usuario.");
            }
            return true;
        } catch (Exception $e) {
            error_log("Error al enviar notificación: " . $e->getMessage());
            throw new Exception("Fallo al enviar la notificación al usuario.");
        }
    }
}