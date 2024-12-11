<?php

namespace App\Services;

use App\Models\Account;
use App\Models\PendingTransaction;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * Processa uma transação pendente.
     *
     * @param PendingTransaction $pendingTransaction
     * @return Transaction
     * @throws \Exception
     */
    public function processPendingTransaction(PendingTransaction $pendingTransaction): Transaction
    {
        return DB::transaction(function () use ($pendingTransaction) {
            $account = Account::lockForUpdate()->findOrFail($pendingTransaction->account_id);

            // Processa a transação com base no tipo
            $type = $pendingTransaction->type;
            $amount = $pendingTransaction->amount;
            $description = $pendingTransaction->description;

            if ($type === 'deposit') {
                $account->balance += $amount;
            } elseif ($type === 'withdraw') {
                $this->validateBalance($account, $amount);
                $account->balance -= $amount;
            } elseif ($type === 'transfer') {
                $this->validateBalance($account, $amount);
                $account->balance -= $amount;
            }

            $account->save();

            // Registra a transação processada
            $transaction = $account->transactions()->create([
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'status' => 'success',
            ]);

            // Marca a transação pendente como processada
            $pendingTransaction->update(['processed' => true]);

            Log::info("Transação {$type} processada com sucesso.", [
                'transaction_id' => $transaction->id,
                'account_id' => $account->id,
                'amount' => $amount,
            ]);

            return $transaction;
        });
    }

    /**
     * Valida se a conta tem saldo suficiente.
     *
     * @param Account $account
     * @param float $amount
     * @throws \Exception
     */
    private function validateBalance(Account $account, float $amount)
    {
        if ($account->balance < $amount) {
            throw new \Exception('Saldo insuficiente.');
        }
    }

    /**
     * Reprocessa transações pendentes.
     *
     * @return void
     */
    public function reprocessPendingTransactions()
    {
        $pendingTransactions = PendingTransaction::where('processed', false)->get();

        foreach ($pendingTransactions as $pendingTransaction) {
            try {
                $this->processPendingTransaction($pendingTransaction);
            } catch (\Exception $e) {
                Log::error("Erro ao reprocessar transação pendente: {$e->getMessage()}", [
                    'transaction_id' => $pendingTransaction->transaction_id,
                ]);
            }
        }
    }
}

// class TransactionService
// {
//     /**
//      * Processa uma transação individual (depósito, saque ou transferência).
//      *
//      * @param Account $account
//      * @param string $type
//      * @param float $amount
//      * @param string|null $description
//      * @return Transaction
//      * @throws \Exception
//      */
//     public function processTransaction(Account $account, string $type, float $amount, ?string $description = null): Transaction
//     {
//         return DB::transaction(function () use ($account, $type, $amount, $description) {
//             // Bloqueia a conta para evitar inconsistências em transações simultâneas
//             $account->lockForUpdate();

//             // Calcula saldo disponível considerando o limite de crédito
//             $availableBalance = $account->balance + $account->credit_limit;

//             // Calcula taxas associadas à transação
//             $fee = $this->calculateFee($type, $amount);
//             $totalAmount = $amount + $fee;

//             // Valida saldo para saques e transferências
//             if (in_array($type, ['withdraw', 'transfer']) && $availableBalance < $totalAmount) {
//                 throw new \Exception('Saldo insuficiente, incluindo taxas.');
//             }

//             // Atualiza saldo da conta
//             if ($type === 'withdraw' || $type === 'transfer') {
//                 $account->balance -= $totalAmount;
//             } elseif ($type === 'deposit') {
//                 $account->balance += $amount;
//             }

//             $account->save();

//             // Registra a transação
//             $transaction = $account->transactions()->create([
//                 'type' => $type,
//                 'amount' => $amount,
//                 'fee' => $fee,
//                 'description' => $description,
//                 'status' => 'success',
//             ]);

//             // Auditoria
//             Log::channel('audit')->info("Transação processada com sucesso.", [
//                 'account_id' => $account->id,
//                 'type' => $type,
//                 'amount' => $amount,
//                 'fee' => $fee,
//                 'balance' => $account->balance,
//             ]);

//             return $transaction;
//         });
//     }

//     /**
//      * Calcula a taxa de uma transação com base no tipo.
//      *
//      * @param string $type
//      * @param float $amount
//      * @return float
//      */
//     private function calculateFee(string $type, float $amount): float
//     {
//         if ($type === 'withdraw') {
//             return 0.01 * $amount; // Taxa de 1% para saques
//         } elseif ($type === 'transfer') {
//             return 0.02 * $amount; // Taxa de 2% para transferências
//         }

//         return 0; // Sem taxas para depósitos
//     }

//     /**
//      * Processa uma transferência entre contas.
//      *
//      * @param Account $sourceAccount
//      * @param Account $targetAccount
//      * @param float $amount
//      * @param string|null $description
//      * @throws \Exception
//      */
//     public function transfer(Account $sourceAccount, Account $targetAccount, float $amount, ?string $description = null): void
//     {
//         DB::transaction(function () use ($sourceAccount, $targetAccount, $amount, $description) {
//             // Bloqueia as contas de origem e destino
//             $sourceAccount->lockForUpdate();
//             $targetAccount->lockForUpdate();

//             // Débito da conta de origem
//             $this->processTransaction($sourceAccount, 'transfer', $amount, $description);

//             // Crédito na conta de destino
//             $this->processTransaction($targetAccount, 'deposit', $amount, "Transferência recebida: $description");
//         });
//     }

//     /**
//      * Processa uma lista de transações em lote.
//      *
//      * @param array $transactions
//      * @throws \Exception
//      */
//     public function processBatchTransactions(array $transactions)
//     {
//         DB::transaction(function () use ($transactions) {
//             foreach ($transactions as $transactionData) {
//                 $account = Account::findOrFail($transactionData['account_id']);

//                 $this->processTransaction(
//                     $account,
//                     $transactionData['type'],
//                     $transactionData['amount'],
//                     $transactionData['description'] ?? null
//                 );
//             }
//         });
//     }

//     /**
//      * Reprocessa todas as transações pendentes.
//      *
//      * Transações pendentes são tentadas novamente.
//      * Caso bem-sucedida, o status é atualizado para 'success'.
//      * Caso falhe, o status permanece 'pending'.
//      */
//     public function reprocessPendingTransactions()
//     {
//         $pendingTransactions = Transaction::where('status', 'pending')->get();

//         foreach ($pendingTransactions as $pendingTransaction) {
//             try {
//                 DB::transaction(function () use ($pendingTransaction) {
//                     $account = Account::findOrFail($pendingTransaction->account_id);

//                     // Processa novamente
//                     $this->processTransaction(
//                         $account,
//                         $pendingTransaction->type,
//                         $pendingTransaction->amount,
//                         $pendingTransaction->description
//                     );

//                     // Atualiza status
//                     $pendingTransaction->update(['status' => 'success']);
//                 });

//                 // Log de sucesso
//                 Log::channel('audit')->info("Transação reprocessada com sucesso.", [
//                     'transaction_id' => $pendingTransaction->id,
//                 ]);
//             } catch (\Exception $e) {
//                 // Log de erro
//                 Log::error("Erro ao reprocessar transação: {$e->getMessage()}", [
//                     'transaction_id' => $pendingTransaction->id,
//                 ]);
//             }
//         }
//     }

//     /**
//      * Consulta o status de uma transação.
//      *
//      * @param int $transactionId
//      * @return Transaction|null
//      */
//     public function getTransactionStatus(int $transactionId): ?Transaction
//     {
//         return Transaction::find($transactionId);
//     }
// }
