<?php
namespace App\Documentation\Banks;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/banks/{bank}",
 *     tags={"Banks"},
 *     summary="Atualizar um banco existente",
 *     description="Endpoint para atualizar as informações de um banco.",
 *     @OA\Parameter(
 *         name="bank",
 *         in="path",
 *         required=true,
 *         description="ID do banco a ser atualizado",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="code", type="string", example="033"),
 *             @OA\Property(property="name", type="string", example="Santander Atualizado")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Banco atualizado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="bank", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="code", type="string", example="033"),
 *                 @OA\Property(property="name", type="string", example="Santander Atualizado"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             ),
 *             @OA\Property(property="message", type="string", example="Banco atualizado com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao atualizar banco.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao atualizar banco.")
 *         )
 *     )
 * )
 */

 class BankUpdate {}
