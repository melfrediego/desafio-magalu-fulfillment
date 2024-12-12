<?php
namespace App\Documentation\Accounts;

use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/accounts/{account}",
 *     tags={"Accounts"},
 *     summary="Delete an account",
 *     description="Delete a specific account by ID.",
 *     @OA\Parameter(
 *         name="account",
 *         in="path",
 *         required=true,
 *         description="ID of the account to delete",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account deleted successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Conta excluída com sucesso.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Error deleting account.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao excluir conta.")
 *         )
 *     )
 * )
 */
class AccountDestroy {}
