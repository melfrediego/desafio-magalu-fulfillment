<?php
namespace App\Documentation\Accounts;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/accounts",
 *     tags={"Accounts"},
 *     summary="List all accounts",
 *     description="Retrieve a paginated list of all accounts with their associated users and banks.",
 *     @OA\Response(
 *         response=200,
 *         description="List of accounts retrieved successfully.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="accounts", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array",
 *                     @OA\Items(
 *                         @OA\Property(property="id", type="integer", example=1),
 *                         @OA\Property(property="agency", type="string", example="1234"),
 *                         @OA\Property(property="number", type="string", example="567890"),
 *                         @OA\Property(property="balance", type="number", example=1000.50),
 *                         @OA\Property(property="credit_limit", type="number", example=500.00),
 *                         @OA\Property(property="user", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="John Doe")
 *                         ),
 *                         @OA\Property(property="bank", type="object",
 *                             @OA\Property(property="id", type="integer", example=1),
 *                             @OA\Property(property="name", type="string", example="Bank Name")
 *                         )
 *                     )
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Error retrieving accounts.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao listar contas.")
 *         )
 *     )
 * )
 */
class AccountIndex {}
