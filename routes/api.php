<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// CRUD de usuários
Route::apiResource('users', UserController::class);

// CRUD de bancos
Route::apiResource('banks', BankController::class);

// CRUD de contas
Route::apiResource('accounts', AccountController::class);

// Transações específicas
// Route::post('/transactions/{account}/deposit', [TransactionController::class, 'deposit']);
// Route::post('/transactions/{account}/withdraw', [TransactionController::class, 'withdraw']);
// Route::post('/transactions/{account}/transfer', [TransactionController::class, 'transfer']);

// Endpoint para reprocessar transações pendentes
// Route::post('/transactions/reprocess', [TransactionController::class, 'reprocessPendingTransactions']);
