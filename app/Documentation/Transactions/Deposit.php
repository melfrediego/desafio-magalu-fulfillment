<?php
namespace App\Documentation\Transactions;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/transactions/deposit",
 *     tags={"Transactions"},
 *     summary="Realizar depósito (sem filas)",
 *     description="Endpoint para realizar um depósito diretamente em uma conta.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"account_id", "amount"},
 *             @OA\Property(property="account_id", type="integer", example=1),
 *             @OA\Property(property="amount", type="number", format="float", example=100.00),
 *             @OA\Property(property="description", type="string", example="Depósito inicial")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Depósito realizado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Depósito realizado com sucesso."),
 *             @OA\Property(property="transaction", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="account_id", type="integer", example=1),
 *                 @OA\Property(property="type", type="string", example="deposit"),
 *                 @OA\Property(property="amount", type="number", format="float", example=100.00),
 *                 @OA\Property(property="description", type="string", example="Depósito inicial"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao realizar depósito.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Erro ao realizar depósito."),
 *             @OA\Property(property="error", type="string", example="Detalhes do erro.")
 *         )
 *     )
 * )
 */

 class Deposit {}
