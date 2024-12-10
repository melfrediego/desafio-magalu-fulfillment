<?php
namespace App\Documentation\Transactions;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/transactions/withdraw",
 *     tags={"Transactions"},
 *     summary="Realizar saque (sem filas)",
 *     description="Endpoint para realizar um saque diretamente de uma conta.",
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
 *         response=200,
 *         description="Saque realizado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Saque realizado com sucesso."),
 *             @OA\Property(property="transaction", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="account_id", type="integer", example=1),
 *                 @OA\Property(property="type", type="string", example="withdraw"),
 *                 @OA\Property(property="amount", type="number", format="float", example=50.00),
 *                 @OA\Property(property="description", type="string", example="Saque para despesas"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao realizar saque.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Erro ao realizar saque."),
 *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
 *         )
 *     )
 * )
 */

 class Withdraw {}
