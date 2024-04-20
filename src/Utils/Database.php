<?php
class Database {
    private $host = "localhost";
    private $db_name = "prueba2024";
    private $username = "root";
    private $password = "root";
    private $conn;

    /**
     * Obtiene o establece la conexión a la base de datos.
     * 
     * @return PDO Objeto de conexión a la base de datos.
     * @throws Exception Si no se puede establecer la conexión.
     */
    public function getConnection() {
        if ($this->conn === null) {
            try {
                $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8";
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                $this->conn = new PDO($dsn, $this->username, $this->password, $options);
            } catch (PDOException $exception) {
                // Registro del error para auditoría interna o debugging.
                error_log("Error de conexión a la base de datos: " . $exception->getMessage());
                // Lanzamiento de una excepción específica para ser capturada por el consumidor de la clase.
                throw new Exception("Error al conectar con la base de datos. Por favor, intente de nuevo más tarde.");
            }
        }
        return $this->conn;
    }
}