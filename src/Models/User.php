/**
 * Modelo representando un usuario del sistema.
 */

<?php
class User {
    public $id;
    public $fullName;
    public $document;
    public $email;
    public $password;
    public $userType;
    public $balance;

    // Constructor con valores por defecto
    public function __construct($fullName = "", $document = "", $email = "", $password = "", $userType = "common", $balance = 0.00) {
        $this->fullName = $fullName;
        $this->document = $document;
        $this->email = $email;
        $this->password = $password;
        $this->userType = $userType;
        $this->balance = $balance;
    }
}