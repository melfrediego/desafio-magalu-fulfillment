<?php
namespace App\Documentation\Accounts;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/accounts/{account}",
 *     tags={"Accounts"},
 *     summary="Update account details",
 *     description="Update details of a specific account by ID.",
 *     @OA\Parameter(
 *         name="account",
 *         in="path",
 *         required=true,
 *         description="ID of the account to update",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="agency", type="string", example="1234"),
 *             @OA\Property(property="number", type="string", example="567890"),
 *             @OA\Property(property="balance", type="number", example=1200.00),
 *             @OA\Property(property="credit_limit", type="number", example=600.00),
 *             @OA\Property(property="user_id", type="integer", example=2),
 *             @OA\Property(property="bank_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account updated successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="account", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="agency", type="string", example="1234"),
 *                 @OA\Property(property="number", type="string", example="567890"),
 *                 @OA\Property(property="balance", type="number", example=1200.00),
 *                 @OA\Property(property="credit_limit", type="number", example=600.00)
 *             ),
 *             @OA\Property(property="message", type="string", example="Conta atualizada com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Error when user is not a client.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="A conta só pode ser associada a um usuário que seja cliente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error updating account.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao atualizar conta.")
 *         )
 *     )
 * )
 */
class AccountUpdate {}
