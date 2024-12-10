<?php
namespace App\Documentation\Transactions;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/transactions/transfer-async",
 *     tags={"Transactions"},
 *     summary="Realizar transferência (com filas)",
 *     description="Endpoint para enviar uma transferência para processamento em segundo plano.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"source_account_id", "target_account_id", "amount"},
 *             @OA\Property(property="source_account_id", type="integer", example=1),
 *             @OA\Property(property="target_account_id", type="integer", example=2),
 *             @OA\Property(property="amount", type="number", format="float", example=200.00),
 *             @OA\Property(property="description", type="string", example="Transferência para pagamento")
 *         )
 *     ),
 *     @OA\Response(
 *         response=202,
 *         description="Transferência enviada para processamento.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Transferência enviada para processamento."),
 *             @OA\Property(property="transaction_id", type="integer", example=1),
 *             @OA\Property(property="status_url", type="string", example="http://127.0.0.1:8000/api/transactions/1/status")
 *         )
 *     )
 * )
 */
class TransferAsync {}
