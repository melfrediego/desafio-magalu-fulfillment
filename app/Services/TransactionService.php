<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * Processa uma transação individual (depósito, saque ou transferência).
     *
     * @param Account $account
     * @param string $type
     * @param float $amount
     * @param string|null $description
     * @return Transaction
     * @throws \Exception
     */
    public function processTransaction(Account $account, string $type, float $amount, ?string $description = null): Transaction
    {
        return DB::transaction(function () use ($account, $type, $amount, $description) {
            // Bloqueia a conta para evitar concorrência
            $account->lockForUpdate();

            // Calcula saldo disponível
            $availableBalance = $account->balance + $account->credit_limit;

            // Calcula taxas
            $fee = $this->calculateFee($type, $amount);
            $totalAmount = $amount + $fee;

            if (in_array($type, ['withdraw', 'transfer']) && $availableBalance < $totalAmount) {
                throw new \Exception('Saldo insuficiente, incluindo taxas.');
            }

            // Atualiza saldo
            if ($type === 'withdraw' || $type === 'transfer') {
                $account->balance -= $totalAmount;
            } elseif ($type === 'deposit') {
                $account->balance += $amount;
            }

            $account->save();

            // Registra a transação
            $transaction = $account->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'fee' => $fee,
                'description' => $description,
                'status' => 'success',
            ]);

            // Auditoria
            Log::channel('audit')->info("Transação processada com sucesso.", [
                'account_id' => $account->id,
                'type' => $type,
                'amount' => $amount,
                'fee' => $fee,
                'balance' => $account->balance,
            ]);

            return $transaction;
        });
    }

    /**
     * Calcula a taxa de uma transação com base no tipo.
     *
     * @param string $type
     * @param float $amount
     * @return float
     */
    private function calculateFee(string $type, float $amount): float
    {
        if ($type === 'withdraw') {
            return 0.01 * $amount;
        } elseif ($type === 'transfer') {
            return 0.02 * $amount;
        }

        return 0;
    }

    /**
     * Processa uma transferência entre contas.
     *
     * @param Account $sourceAccount
     * @param Account $targetAccount
     * @param float $amount
     * @param string|null $description
     * @throws \Exception
     */
    public function transfer(Account $sourceAccount, Account $targetAccount, float $amount, ?string $description = null): void
    {
        DB::transaction(function () use ($sourceAccount, $targetAccount, $amount, $description) {
            // Bloqueia ambas as contas
            $sourceAccount->lockForUpdate();
            $targetAccount->lockForUpdate();

            // Débito na conta de origem
            $this->processTransaction($sourceAccount, 'transfer', $amount, $description);

            // Crédito na conta de destino
            $this->processTransaction($targetAccount, 'deposit', $amount, "Transferência recebida: $description");
        });
    }

    /**
     * Processa uma lista de transações em lote.
     *
     * @param array $transactions
     * @throws \Exception
     */
    public function processBatchTransactions(array $transactions)
    {
        DB::transaction(function () use ($transactions) {
            foreach ($transactions as $transactionData) {
                $account = Account::findOrFail($transactionData['account_id']);

                $this->processTransaction(
                    $account,
                    $transactionData['type'],
                    $transactionData['amount'],
                    $transactionData['description'] ?? null
                );
            }
        });
    }

    /**
     * Reprocessa todas as transações pendentes.
     *
     * Este método verifica as transações pendentes (status: 'pending') e tenta
     * processá-las novamente. Caso a transação seja bem-sucedida, o status é
     * atualizado para 'success'. Caso falhe novamente, o status permanece como 'pending'.
     */
    public function reprocessPendingTransactions()
    {
        $pendingTransactions = Transaction::where('status', 'pending')->get();

        foreach ($pendingTransactions as $pendingTransaction) {
            try {
                DB::transaction(function () use ($pendingTransaction) {
                    $account = Account::findOrFail($pendingTransaction->account_id);

                    // Tenta processar novamente
                    $this->processTransaction(
                        $account,
                        $pendingTransaction->type,
                        $pendingTransaction->amount,
                        $pendingTransaction->description
                    );

                    // Atualiza o status
                    $pendingTransaction->update(['status' => 'success']);
                });

                // Log de sucesso
                Log::channel('audit')->info("Transação reprocessada com sucesso.", [
                    'transaction_id' => $pendingTransaction->id,
                    'account_id' => $pendingTransaction->account_id,
                    'type' => $pendingTransaction->type,
                    'amount' => $pendingTransaction->amount,
                ]);
            } catch (\Exception $e) {
                // Log de erro
                Log::error("Erro ao reprocessar transação: {$e->getMessage()}", [
                    'transaction_id' => $pendingTransaction->id,
                    'account_id' => $pendingTransaction->account_id,
                    'type' => $pendingTransaction->type,
                    'amount' => $pendingTransaction->amount,
                ]);
            }
        }
    }
}
