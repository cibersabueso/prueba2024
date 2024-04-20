<?php

use PHPUnit\Framework\TestCase;
require_once 'src/Services/TransactionService.php';

class TransactionServiceTest extends TestCase
{
    private $transactionService;
    private $db;

    protected function setUp(): void
    {
        // Mock de la base de datos
        $this->db = $this->createMock(PDO::class);
        
        // Instanciar TransactionService con la base de datos mockeada
        $this->transactionService = new TransactionService($this->db);
    }

    public function testTransferSuccess()
    {
        // Configurar el mock de PDO para simular un usuario con saldo suficiente
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn((object)['id' => 1, 'balance' => 1000]);
        $this->db->method('prepare')->willReturn($stmt);

        // Simular autorización externa y notificación exitosa
        $this->transactionService->method('checkExternalAuthorization')->willReturn(true);
        $this->transactionService->method('sendNotification')->willReturn(true);

        // Ejecutar la transferencia
        $result = $this->transactionService->transfer(1, 2, 100);

        // Afirmar que la transferencia fue exitosa
        $this->assertTrue($result);
    }

    public function testTransferInsufficientFunds()
    {
        // Configurar el mock de PDO para simular un usuario con saldo insuficiente
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('fetch')->willReturn((object)['id' => 1, 'balance' => 50]);
        $this->db->method('prepare')->willReturn($stmt);

        // Esperar que se lance una excepción por saldo insuficiente
        $this->expectException(Exception::class);
        $this->expectExceptionMessage("Saldo insuficiente");

        // Intentar ejecutar la transferencia
        $this->transactionService->transfer(1, 2, 100);
    }
}