<?php
namespace App\Documentation\Users;

use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/users/{user}",
 *     tags={"Users"},
 *     summary="Excluir um usuário",
 *     description="Endpoint para excluir um usuário pelo ID.",
 *     @OA\Parameter(
 *         name="user",
 *         in="path",
 *         required=true,
 *         description="ID do usuário a ser excluído",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Usuário excluído com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Usuário excluído com sucesso.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao excluir usuário.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="error", type="string", example="Erro ao excluir usuário.")
 *         )
 *     )
 * )
 */
class UserDestroy {}
