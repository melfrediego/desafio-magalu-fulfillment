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

        // $account = Account::lockForUpdate()->findOrFail($data['account_id']);
        // if ($data['type'] === 'deposit') {
        //     $account->balance += $data['amount'];
        // } elseif ($data['type'] === 'withdraw') {
        //     $account->balance -= $data['amount'];
        // }
        // $account->save();

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
            $sourceAccount = Account::lockForUpdate()->findOrFail($pendingTransaction->account_id);

            if ($pendingTransaction->type === 'transfer') {
                // Para transferências, também busca a conta de destino
                $targetAccount = Account::lockForUpdate()->findOrFail($pendingTransaction->target_account_id);

                // Valida saldo suficiente na conta de origem
                $this->validateBalance($sourceAccount, $pendingTransaction->amount);

                // Atualiza saldos
                $sourceAccount->balance -= $pendingTransaction->amount;
                $targetAccount->balance += $pendingTransaction->amount;

                $sourceAccount->save();
                $targetAccount->save();

                // Cria registros de transação para ambas as contas
                $this->createTransactionRecord([
                    'account_id' => $sourceAccount->id,
                    'type' => 'withdraw',
                    'amount' => $pendingTransaction->amount,
                    'description' => $pendingTransaction->description ?: 'Transferência enviada',
                    'status' => 'success',
                ]);

                $this->createTransactionRecord([
                    'account_id' => $targetAccount->id,
                    'type' => 'deposit',
                    'amount' => $pendingTransaction->amount,
                    'description' => $pendingTransaction->description ?: 'Transferência recebida',
                    'status' => 'success',
                ]);
            } elseif ($pendingTransaction->type === 'deposit') {
                $sourceAccount->balance += $pendingTransaction->amount;
                $sourceAccount->save();
            } elseif ($pendingTransaction->type === 'withdraw') {
                $this->validateBalance($sourceAccount, $pendingTransaction->amount);
                $sourceAccount->balance -= $pendingTransaction->amount;
                $sourceAccount->save();
            } else {
                throw new Exception("Tipo de transação inválido: {$pendingTransaction->type}");
            }

            // Marca a transação pendente como processada
            $pendingTransaction->update(['processed' => true]);

            return $this->createTransactionRecord([
                'account_id' => $sourceAccount->id,
                'type' => $pendingTransaction->type,
                'amount' => $pendingTransaction->amount,
                'description' => $pendingTransaction->description,
                'status' => 'success',
            ]);
        });
    }


    // /**
    //  * Processa transferências entre contas.
    //  *
    //  * @param PendingTransaction $pendingTransaction
    //  * @param Account $sourceAccount
    //  * @throws \Exception
    //  */
    // protected function processTransfer(PendingTransaction $pendingTransaction, Account $sourceAccount)
    // {
    //     // Valida saldo suficiente na conta de origem
    //     $this->validateBalance($sourceAccount, $pendingTransaction->amount);

    //     // Obtém a conta de destino
    //     $targetAccount = Account::lockForUpdate()->findOrFail($pendingTransaction->target_account_id);

    //     // Realiza a transferência de saldo
    //     $sourceAccount->balance -= $pendingTransaction->amount;
    //     $targetAccount->balance += $pendingTransaction->amount;

    //     // Salva os novos saldos
    //     $sourceAccount->save();
    //     $targetAccount->save();

    //     // Registra transações para ambas as contas
    //     $this->createTransactionRecord([
    //         'account_id' => $sourceAccount->id,
    //         'type' => 'withdraw',
    //         'amount' => $pendingTransaction->amount,
    //         'description' => 'Transferência enviada',
    //         'status' => 'success',
    //     ]);

    //     $this->createTransactionRecord([
    //         'account_id' => $targetAccount->id,
    //         'type' => 'deposit',
    //         'amount' => $pendingTransaction->amount,
    //         'description' => 'Transferência recebida',
    //         'status' => 'success',
    //     ]);

    //     Log::info('Transfer');

    //     // Atualiza o status da transação pendente
    //     $pendingTransaction->update(['processed' => true]);

    //     Log::info("Transferência processada com sucesso.", [
    //         'source_account_id' => $sourceAccount->id,
    //         'target_account_id' => $targetAccount->id,
    //         'amount' => $pendingTransaction->amount,
    //     ]);
    // }

    protected function processTransfer(PendingTransaction $pendingTransaction, Account $sourceAccount)
    {
        // Valida saldo suficiente na conta de origem
        $this->validateBalance($sourceAccount, $pendingTransaction->amount);

        // Obtém a conta de destino
        $targetAccount = Account::lockForUpdate()->findOrFail($pendingTransaction->target_account_id);

        // Realiza a transferência de saldo
        $sourceAccount->balance -= $pendingTransaction->amount;
        $targetAccount->balance += $pendingTransaction->amount;

        // Salva os novos saldos
        $sourceAccount->save();
        $targetAccount->save();

        // Registra transações para ambas as contas
        $this->createTransactionRecord([
            'account_id' => $sourceAccount->id,
            'type' => 'withdraw',
            'amount' => $pendingTransaction->amount,
            'description' => $pendingTransaction->description . ' - Transferência enviada',
            'status' => 'success',
        ]);

        $this->createTransactionRecord([
            'account_id' => $targetAccount->id,
            'type' => 'deposit',
            'amount' => $pendingTransaction->amount,
            'description' => $pendingTransaction->description . ' - Transferência recebida',
            'status' => 'success',
        ]);

        // Atualiza o status da transação pendente
        $pendingTransaction->update(['processed' => true]);

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
