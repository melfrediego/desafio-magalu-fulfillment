<?php
namespace App\Documentation\Users;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Atualizar um usuário existente",
 *     description="Endpoint para atualizar as informações de um usuário.",
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID do usuário a ser atualizado",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="João Silva Atualizado"),
 *             @OA\Property(property="email", type="string", example="joao.silva@example.com"),
 *             @OA\Property(property="password", type="string", example="novaSenha123"),
 *             @OA\Property(property="password_confirmation", type="string", example="novaSenha123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário atualizado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="user", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva Atualizado"),
 *                 @OA\Property(property="email", type="string", example="joao.silva@example.com"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             ),
 *             @OA\Property(property="message", type="string", example="Usuário editado com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao editar usuário.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao editar usuário.")
 *         )
 *     )
 * )
 */
class UserUpdate {}
