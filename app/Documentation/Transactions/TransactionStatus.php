<?php
namespace App\Documentation\Transactions;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/transactions/{id}/status",
 *     tags={"Transactions"},
 *     summary="Consultar status de uma transação",
 *     description="Endpoint para consultar o status de uma transação pelo ID.",
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID da transação",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Status da transação retornado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="transaction_id", type="integer", example=1),
 *             @OA\Property(property="status", type="string", example="completed"),
 *             @OA\Property(property="type", type="string", example="deposit"),
 *             @OA\Property(property="amount", type="number", format="float", example=100.00),
 *             @OA\Property(property="description", type="string", example="Depósito inicial"),
 *             @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Transação não encontrada.",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Transação não encontrada.")
 *         )
 *     )
 * )
 */
class TransactionStatus {}
