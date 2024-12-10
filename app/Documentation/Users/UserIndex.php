<?php
namespace App\Documentation\Users;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/users",
 *     tags={"Users"},
 *     summary="Listar todos os usuários",
 *     description="Retorna uma lista de usuários cadastrados.",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de usuários retornada com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(
 *                 property="users",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array", @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="João Silva"),
 *                     @OA\Property(property="email", type="string", example="joao@example.com"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *                 ))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao listar usuários.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao listar usuários.")
 *         )
 *     )
 * )
 */
class UserIndex {}
