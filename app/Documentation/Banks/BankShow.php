<?php
namespace App\Documentation\Banks;

use OpenApi\Annotations as OA;


/**
 * @OA\Get(
 *     path="/banks/{bank}",
 *     tags={"Banks"},
 *     summary="Exibir detalhes de um banco",
 *     description="Retorna os detalhes de um banco pelo ID.",
 *     @OA\Parameter(
 *         name="bank",
 *         in="path",
 *         required=true,
 *         description="ID do banco a ser exibido",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalhes do banco retornados com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="bank", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="code", type="string", example="033"),
 *                 @OA\Property(property="name", type="string", example="Santander"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Banco não encontrado.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Banco não encontrado.")
 *         )
 *     )
 * )
 */

 class BankShow {}
