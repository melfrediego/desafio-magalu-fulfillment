<?php
namespace App\Documentation\Accounts;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/accounts",
 *     tags={"Accounts"},
 *     summary="Create a new account",
 *     description="Create a new account for a client user.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="agency", type="string", example="1234"),
 *             @OA\Property(property="number", type="string", example="567890"),
 *             @OA\Property(property="balance", type="number", example=1000.50),
 *             @OA\Property(property="credit_limit", type="number", example=500.00),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="bank_id", type="integer", example=1)
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Account created successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="account", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="agency", type="string", example="1234"),
 *                 @OA\Property(property="number", type="string", example="567890"),
 *                 @OA\Property(property="balance", type="number", example=1000.50),
 *                 @OA\Property(property="credit_limit", type="number", example=500.00)
 *             ),
 *             @OA\Property(property="message", type="string", example="Conta cadastrada com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=403,
 *         description="Error when user is not a client.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="A conta só pode ser cadastrada para um usuário que seja cliente.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Error creating account.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao criar conta.")
 *         )
 *     )
 * )
 */
class AccountStore {}
