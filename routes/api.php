<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Redireciona a rota `/` para a rota de API raiz
// Route::get('/', function () {
//     return response()->json([
//         'message' => 'Bem-vindo à API de Gestão de Contas - Magalu Fulfillment!',
//         'versao' => '1.0.0',
//         'rotas_disponiveis' => [
//             'GET /users' => 'Lista de usuários',
//             'POST /users' => 'Criar usuário',
//             'GET /banks' => 'Lista de bancos',
//             'POST /accounts' => 'Criar uma conta',
//             'POST /transactions/deposit' => 'Realizar depósito',
//             'POST /transactions/withdraw' => 'Realizar saque',
//             'POST /transactions/transfer' => 'Realizar transferência',
//             'POST /transactions/batch' => 'Processar transações em lote',
//             'POST /transactions/reprocess' => 'Reprocessar transações pendentes',
//         ],
//     ]);
// });

// CRUD de usuários
Route::apiResource('users', UserController::class);

Route::apiResource('clients', ClientController::class);

// CRUD de bancos
Route::apiResource('banks', BankController::class);

// CRUD de contas
Route::apiResource('accounts', AccountController::class);

// Transações específicas
Route::post('/transactions/deposit', [TransactionController::class, 'deposit']);
Route::post('/transactions/deposit-async', [TransactionController::class, 'depositAsync']);
Route::post('/transactions/withdraw', [TransactionController::class, 'withdraw']);
Route::post('/transactions/withdraw-async', [TransactionController::class, 'withdrawAsync']);
Route::post('/transactions/transfer', [TransactionController::class, 'transfer']);
Route::post('/transactions/transfer-async', [TransactionController::class, 'transferAsync']);
Route::post('/transactions/batch', [TransactionController::class, 'processBatchTransactions']);
Route::post('/transactions/reprocess', [TransactionController::class, 'reprocessPendingTransactions']);
Route::get('/transactions/{id}/status', [TransactionController::class, 'getTransactionStatus'])->name('transactions.status');
