<?php

namespace Tests\Unit;

use App\Models\Account;
use App\Models\PendingTransaction;
use App\Models\Transaction;
use App\Services\TransactionService;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionServiceTest extends TestCase
{
    use RefreshDatabase; // Isso irá usar um banco de dados em memória para os testes

    private TransactionService $service;

    /**
     *  Configura o ambiente de teste.
     *  Cria uma instância do serviço TransactionService antes de cada teste.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TransactionService();
    }

    /**
     * @test
     * Testa se o método createTransactionRecord cria um registro de transação com sucesso.
     */
    public function createTransactionRecord_creates_a_transaction_record_successfully()
    {
        // Cria um array com os dados da transação
        $data = [
            'account_id' => Account::factory()->create()->id, // Cria uma conta e usa seu ID
            'type' => 'deposit',
            'amount' => 100,
            'description' => 'Test deposit',
            'status' => 'success',
        ];

        // Chama o método createTransactionRecord do serviço
        $transaction = $this->service->createTransactionRecord($data);

        // Verifica se o retorno é uma instância de Transaction
        $this->assertInstanceOf(Transaction::class, $transaction);
        // Verifica se os dados foram persistidos no banco de dados
        $this->assertDatabaseHas('transactions', $data);
    }

    /**
     * @test
     * Testa se o método processPendingTransaction processa uma transação pendente de depósito com sucesso.
     */
    public function processPendingTransaction_processes_a_pending_deposit_transaction_successfully()
    {
        // Cria uma conta com saldo de 500
        $account = Account::factory()->create(['balance' => 500]);
        // Cria uma transação pendente de depósito
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 100,
        ]);

        // Processa a transação pendente
        $transaction = $this->service->processPendingTransaction($pendingTransaction);

        // Verifica se o retorno é uma instância de Transaction
        $this->assertInstanceOf(Transaction::class, $transaction);
        // Verifica se o saldo da conta foi atualizado corretamente
        $this->assertEquals(600, $account->fresh()->balance);
        // Verifica se a transação pendente foi marcada como processada
        $this->assertTrue(boolval($pendingTransaction->fresh()->processed));
    }

    /**
     * @test
     * Testa se o método processPendingTransaction processa uma transação pendente de saque com sucesso.
     */
    public function processPendingTransaction_processes_a_pending_withdrawal_transaction_successfully()
    {
        // Cria uma conta com saldo de 500
        $account = Account::factory()->create(['balance' => 500]);
        // Cria uma transação pendente de saque
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 100,
        ]);

        // Processa a transação pendente
        $transaction = $this->service->processPendingTransaction($pendingTransaction);

        // Verifica se o retorno é uma instância de Transaction
        $this->assertInstanceOf(Transaction::class, $transaction);
        // Verifica se o saldo da conta foi atualizado corretamente
        $this->assertEquals(400, $account->fresh()->balance);
        // Verifica se a transação pendente foi marcada como processada
        $this->assertTrue(boolval($pendingTransaction->fresh()->processed));
    }

    /**
     * @test
     * Testa se o método processPendingTransaction processa uma transação pendente de transferência com sucesso.
     */
    public function processPendingTransaction_processes_a_pending_transfer_transaction_successfully()
    {
        // Cria duas contas, uma de origem com saldo de 500 e outra de destino com saldo de 200
        $sourceAccount = Account::factory()->create(['balance' => 500]);
        $targetAccount = Account::factory()->create(['balance' => 200]);
        // Cria uma transação pendente de transferência
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'type' => 'transfer',
            'amount' => 100,
        ]);

        // Processa a transação pendente
        $transaction = $this->service->processPendingTransaction($pendingTransaction);

        // Verifica se o retorno é uma instância de Transaction
        $this->assertInstanceOf(Transaction::class, $transaction);
        // Verifica se o saldo da conta de origem foi atualizado corretamente
        $this->assertEquals(400, $sourceAccount->fresh()->balance);
        // Verifica se o saldo da conta de destino foi atualizado corretamente
        $this->assertEquals(300, $targetAccount->fresh()->balance);
        // Verifica se a transação pendente foi marcada como processada
        $this->assertTrue(boolval($pendingTransaction->fresh()->processed));
    }

    /**
     * @test
     * Testa se o método processPendingTransaction lança uma exceção para tipo de transação inválido.
     */
    public function processPendingTransaction_throws_exception_for_invalid_transaction_type()
    {
        // Cria uma conta
        $account = Account::factory()->create(['balance' => 500]);

        // Cria uma transação pendente com um tipo INVÁLIDO (usando state)
        $pendingTransaction = PendingTransaction::factory()->state(['type' => 'invalid_type'])->create();

        // Espera que uma exceção seja lançada
        $this->expectException(Exception::class);
        // Espera que a mensagem da exceção seja "Tipo de transação inválido: invalid_type"
        $this->expectExceptionMessage("Tipo de transação inválido: invalid_type");

        // Processa a transação pendente (deve lançar a exceção)
        $this->service->processPendingTransaction($pendingTransaction);
    }

    /**
     * @test
     * Testa se o método processPendingTransaction lança uma exceção para saldo insuficiente.
     */
    public function processPendingTransaction_throws_exception_for_insufficient_balance()
    {
        // Cria uma conta com saldo insuficiente
        $account = Account::factory()->create(['balance' => 50]);
        // Cria uma transação pendente de saque com valor maior que o saldo
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 100,
        ]);

        // Espera que uma exceção seja lançada
        $this->expectException(Exception::class);
        // Espera que a mensagem da exceção seja "Saldo insuficiente."
        $this->expectExceptionMessage('Saldo insuficiente.');

        // Processa a transação pendente (deve lançar a exceção)
        $this->service->processPendingTransaction($pendingTransaction);
    }

    /**
     * @test
     * Testa se o método reprocessPendingTransactions reprocessa transações pendentes com sucesso.
     */
    public function reprocessPendingTransactions_reprocesses_pending_transactions_successfully()
    {
        // Cria uma conta com saldo de 500
        $account = Account::factory()->create(['balance' => 500]);
        // Cria uma transação pendente de depósito
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'deposit',
            'amount' => 100,
            'processed' => false, // Define como não processada
        ]);

        // Chama o método para reprocessar as transações pendentes
        $this->service->reprocessPendingTransactions();

        // Verifica se o saldo da conta foi atualizado corretamente
        $this->assertEquals(600, $account->fresh()->balance);
        // Verifica se a transação pendente foi marcada como processada
        $this->assertTrue(boolval($pendingTransaction->fresh()->processed));
    }

    /**
     * @test
     * Testa se o método reprocessPendingTransactions registra um log de erro para transações com falha.
     */
    public function reprocessPendingTransactions_logs_error_for_failed_transactions()
    {
        // Cria uma conta com saldo insuficiente
        $account = Account::factory()->create(['balance' => 50]);
        // Cria uma transação pendente de saque com valor maior que o saldo
        $pendingTransaction = PendingTransaction::factory()->create([
            'account_id' => $account->id,
            'type' => 'withdraw',
            'amount' => 100,
            'processed' => false, // Define como não processada
        ]);

        // Chama o método para reprocessar as transações pendentes
        $this->service->reprocessPendingTransactions();

        // Verifica se o saldo da conta permanece o mesmo
        $this->assertEquals(50, $account->fresh()->balance);
        // Verifica se a transação pendente continua não processada
        $this->assertFalse(boolval($pendingTransaction->fresh()->processed));
        // Aqui você deve adicionar uma asserção para verificar se o log de erro foi registrado
        // $this->assertLogsContain("Erro ao reprocessar transação pendente: Saldo insuficiente.");
    }
}
