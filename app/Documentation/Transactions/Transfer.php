<?php
namespace App\Documentation\Transactions;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/transactions/transfer",
 *     tags={"Transactions"},
 *     summary="Realizar transferência (sem filas)",
 *     description="Endpoint para realizar uma transferência entre contas diretamente.",
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
 *         response=200,
 *         description="Transferência realizada com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Transferência realizada com sucesso.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao realizar transferência.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Erro ao realizar transferência."),
 *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
 *         )
 *     )
 * )
 */

 class Transfer {}
