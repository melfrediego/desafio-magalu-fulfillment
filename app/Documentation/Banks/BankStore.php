<?php
namespace App\Documentation\Banks;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/banks",
 *     tags={"Banks"},
 *     summary="Cadastrar um novo banco",
 *     description="Endpoint para cadastrar um novo banco.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"code", "name"},
 *             @OA\Property(property="code", type="string", example="033"),
 *             @OA\Property(property="name", type="string", example="Santander")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Banco cadastrado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="bank", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="code", type="string", example="033"),
 *                 @OA\Property(property="name", type="string", example="Santander"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             ),
 *             @OA\Property(property="message", type="string", example="Banco cadastrado com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao criar banco.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao criar banco.")
 *         )
 *     )
 * )
 */
 class BankStore {}
