<?php
namespace App\Documentation\Clients;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/clients/{client}",
 *     tags={"Clients"},
 *     summary="Exibir detalhes de um cliente",
 *     description="Retorna os detalhes de um cliente pelo ID.",
 *     @OA\Parameter(
 *         name="client",
 *         in="path",
 *         required=true,
 *         description="ID do cliente a ser exibido",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Detalhes do cliente retornados com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="client", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", example="joao@example.com"),
 *                 @OA\Property(property="phone_whatsapp", type="string", example="11999999999"),
 *                 @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
 *                 @OA\Property(property="person_type", type="string", example="PF"),
 *                 @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Usuário não é um cliente ou não encontrado.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Usuário não é um cliente.")
 *         )
 *     )
 * )
 */

 class ClientShow {}
