<?php
namespace App\Documentation\Transactions;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/transactions/withdraw-async",
 *     tags={"Transactions"},
 *     summary="Realizar saque (com filas)",
 *     description="Endpoint para enviar um saque para processamento em segundo plano.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"account_id", "amount"},
 *             @OA\Property(property="account_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", format="float", example=50.00),
 *             @OA\Property(property="description", type="string", example="Saque para despesas")
 *         )
 *     ),
 *     @OA\Response(
 *         response=202,
 *         description="Saque enviado para processamento.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Saque enviado para processamento."),
 *             @OA\Property(property="transaction_id", type="integer", example=1),
 *             @OA\Property(property="status_url", type="string", example="http://127.0.0.1:8000/api/transactions/1/status")
 *         )
 *     )
 * )
 */
class WithdrawAsync {}
