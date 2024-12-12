<?php
namespace App\Documentation\Accounts;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/accounts/{account}",
 *     tags={"Accounts"},
 *     summary="Get account details",
 *     description="Retrieve details of a specific account by ID.",
 *     @OA\Parameter(
 *         name="account",
 *         in="path",
 *         required=true,
 *         description="ID of the account to retrieve",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Account details retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="account", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="agency", type="string", example="1234"),
 *                 @OA\Property(property="number", type="string", example="567890"),
 *                 @OA\Property(property="balance", type="number", example=1000.50),
 *                 @OA\Property(property="credit_limit", type="number", example=500.00)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Account not found.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Conta não encontrada.")
 *         )
 *     )
 * )
 */
class AccountShow {}
