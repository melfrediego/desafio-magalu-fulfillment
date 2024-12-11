<?php

namespace App\Jobs;

use App\Models\PendingTransaction;
use App\Models\Transaction;
use App\Services\TransactionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $transactionId;

    /**
     * Cria uma nova instância do job.
     *
     * @param string $transactionId
     */
    public function __construct(string $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * Processa a transação.
     */
    public function handle(TransactionService $service)
    {
        try {
            $pendingTransaction = PendingTransaction::where('transaction_id', $this->transactionId)->firstOrFail();
            $service->processPendingTransaction($pendingTransaction);

            Log::info("Transação processada com sucesso.", ['transaction_id' => $this->transactionId]);
        } catch (Exception $e) {
            Log::error("Erro ao processar transação: {$e->getMessage()}", ['transaction_id' => $this->transactionId]);
        }
    }
}

// class ProcessTransactionJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     private $transactionId;

//     /**
//      * Cria uma nova instância do job.
//      *
//      * @param int $transactionId
//      */
//     public function __construct(int $transactionId)
//     {
//         $this->transactionId = $transactionId;
//     }

//     /**
//      * Processa a transação.
//      */
//     public function handle(TransactionService $service)
//     {
//         try {
//             $transaction = Transaction::findOrFail($this->transactionId);
//             $account = $transaction->account;

//             $service->processTransaction(
//                 $account,
//                 $transaction->type,
//                 $transaction->amount,
//                 $transaction->description
//             );

//             $transaction->update(['status' => 'success']);
//             Log::info("Transação processada com sucesso.", ['transaction_id' => $this->transactionId]);
//         } catch (Exception $e) {
//             $transaction = Transaction::find($this->transactionId);
//             $transaction?->update(['status' => 'failed']);
//             Log::error("Erro ao processar transação: {$e->getMessage()}", ['transaction_id' => $this->transactionId]);
//         }
//     }
// }
