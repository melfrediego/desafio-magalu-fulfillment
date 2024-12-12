<?php

namespace App\Services;

use App\Models\Account;
use App\Models\PendingTransaction;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionService
{
    /**
     * Cria um registro de transação.
     *
     * @param array $data
     * @return Transaction
     */
    public function createTransactionRecord(array $data): Transaction
    {
        $transaction = Transaction::create($data);

        $account = Account::lockForUpdate()->findOrFail($data['account_id']);
        if ($data['type'] === 'deposit') {
            $account->balance += $data['amount'];
        } elseif ($data['type'] === 'withdraw') {
            $account->balance -= $data['amount'];
        }
        $account->save();

        return $transaction;
    }

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

            $type = $pendingTransaction->type;
            $amount = $pendingTransaction->amount;
            $description = $pendingTransaction->description;

            if ($type === 'deposit') {
                $account->balance += $amount;
            } elseif ($type === 'withdraw') {
                $this->validateBalance($account, $amount);
                $account->balance -= $amount;
            } elseif ($type === 'transfer') {
                $this->processTransfer($pendingTransaction, $account);
            } else {
                throw new Exception("Tipo de transação inválido: {$type}");
            }

            $account->save();

            $transaction = $this->createTransactionRecord([
                'account_id' => $account->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
                'status' => 'success',
            ]);

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
     * Processa transferências entre contas.
     *
     * @param PendingTransaction $pendingTransaction
     * @param Account $sourceAccount
     * @throws \Exception
     */
    protected function processTransfer(PendingTransaction $pendingTransaction, Account $sourceAccount)
    {
        $this->validateBalance($sourceAccount, $pendingTransaction->amount);

        $targetAccount = Account::findOrFail($pendingTransaction->target_account_id);

        $sourceAccount->balance -= $pendingTransaction->amount;
        $targetAccount->balance += $pendingTransaction->amount;

        $sourceAccount->save();
        $targetAccount->save();

        $this->createTransactionRecord([
            'account_id' => $sourceAccount->id,
            'type' => 'transfer',
            'amount' => $pendingTransaction->amount,
            'description' => 'Transferência enviada',
            'status' => 'success',
        ]);

        $this->createTransactionRecord([
            'account_id' => $targetAccount->id,
            'type' => 'deposit',
            'amount' => $pendingTransaction->amount,
            'description' => 'Transferência recebida',
            'status' => 'success',
        ]);

        Log::info("Transferência processada com sucesso.", [
            'source_account_id' => $sourceAccount->id,
            'target_account_id' => $targetAccount->id,
            'amount' => $pendingTransaction->amount,
        ]);
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
            throw new Exception('Saldo insuficiente.');
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
            } catch (Exception $e) {
                Log::error("Erro ao reprocessar transação pendente: {$e->getMessage()}", [
                    'transaction_id' => $pendingTransaction->id,
                ]);
            }
        }
    }
}

// class TransactionService
// {

//     // /**
//     //  * Cria um registro de transação.
//     //  *
//     //  * @param array $data
//     //  * @return Transaction
//     //  */
//     // public function createTransactionRecord(array $data): Transaction
//     // {
//     //     return Transaction::create($data);
//     // }

//     /**
//      * Processa uma transação pendente.
//      *
//      * @param PendingTransaction $pendingTransaction
//      * @return Transaction
//      * @throws \Exception
//      */
//     public function processPendingTransaction(PendingTransaction $pendingTransaction): Transaction
//     {
//         return DB::transaction(function () use ($pendingTransaction) {
//             $account = Account::lockForUpdate()->findOrFail($pendingTransaction->account_id);

//             // Processa a transação com base no tipo
//             $type = $pendingTransaction->type;
//             $amount = $pendingTransaction->amount;
//             $description = $pendingTransaction->description;

//             if ($type === 'deposit') {
//                 $account->balance += $amount;
//             } elseif ($type === 'withdraw') {
//                 $this->validateBalance($account, $amount);
//                 $account->balance -= $amount;
//             } elseif ($type === 'transfer') {
//                 $this->processTransfer($pendingTransaction, $account);
//                 return $this->createTransactionRecord($pendingTransaction, $account, 'success');
//             }

//             $account->save();

//             // Registra a transação processada
//             $transaction = $this->createTransactionRecord($pendingTransaction, $account, 'success');

//             // Marca a transação pendente como processada
//             $pendingTransaction->update(['processed' => true]);

//             Log::info("Transação {$type} processada com sucesso.", [
//                 'transaction_id' => $transaction->id,
//                 'account_id' => $account->id,
//                 'amount' => $amount,
//             ]);

//             return $transaction;
//         });
//     }


//      //FUNCIONANDO CORRETAMENTE
//     // public function processPendingTransaction(PendingTransaction $pendingTransaction): Transaction
//     // {
//     //     return DB::transaction(function () use ($pendingTransaction) {
//     //         $account = Account::lockForUpdate()->findOrFail($pendingTransaction->account_id);

//     //         // Processa a transação com base no tipo
//     //         $type = $pendingTransaction->type;
//     //         $amount = $pendingTransaction->amount;
//     //         $description = $pendingTransaction->description;

//     //         if ($type === 'deposit') {
//     //             $account->balance += $amount;
//     //         } elseif ($type === 'withdraw') {
//     //             $this->validateBalance($account, $amount);
//     //             $account->balance -= $amount;
//     //         } elseif ($type === 'transfer') {
//     //             $this->validateBalance($account, $amount);
//     //             $account->balance -= $amount;
//     //         }

//     //         $account->save();

//     //         // Registra a transação processada
//     //         $transaction = $account->transactions()->create([
//     //             'type' => $type,
//     //             'amount' => $amount,
//     //             'description' => $description,
//     //             'status' => 'success',
//     //         ]);

//     //         // Marca a transação pendente como processada
//     //         $pendingTransaction->update(['processed' => true]);

//     //         Log::info("Transação {$type} processada com sucesso.", [
//     //             'transaction_id' => $transaction->id,
//     //             'account_id' => $account->id,
//     //             'amount' => $amount,
//     //         ]);

//     //         return $transaction;
//     //     });
//     // }

//     /**
//      * Processa transferências entre contas.
//      *
//      * @param PendingTransaction $pendingTransaction
//      * @param Account $sourceAccount
//      * @throws \Exception
//      */
//     private function processTransfer(PendingTransaction $pendingTransaction, Account $sourceAccount)
//     {
//         $this->validateBalance($sourceAccount, $pendingTransaction->amount);

//         $targetAccount = Account::findOrFail($pendingTransaction->target_account_id);

//         $sourceAccount->balance -= $pendingTransaction->amount;
//         $targetAccount->balance += $pendingTransaction->amount;

//         $sourceAccount->save();
//         $targetAccount->save();

//         Log::info("Transferência processada com sucesso.", [
//             'source_account_id' => $sourceAccount->id,
//             'target_account_id' => $targetAccount->id,
//             'amount' => $pendingTransaction->amount,
//             'transaction_id' => $pendingTransaction->transaction_id,
//         ]);
//     }

//     /**
//      * Valida se a conta tem saldo suficiente.
//      *
//      * @param Account $account
//      * @param float $amount
//      * @throws \Exception
//      */
//     private function validateBalance(Account $account, float $amount)
//     {
//         if ($account->balance < $amount) {
//             throw new Exception('Saldo insuficiente.');
//         }
//     }

//     /**
//      * Reprocessa transações pendentes.
//      *
//      * @return void
//      */
//     public function reprocessPendingTransactions()
//     {
//         $pendingTransactions = PendingTransaction::where('processed', false)->get();

//         foreach ($pendingTransactions as $pendingTransaction) {
//             try {
//                 $this->processPendingTransaction($pendingTransaction);
//             } catch (Exception $e) {
//                 Log::error("Erro ao reprocessar transação pendente: {$e->getMessage()}", [
//                     'transaction_id' => $pendingTransaction->transaction_id,
//                 ]);
//             }
//         }
//     }
// }
