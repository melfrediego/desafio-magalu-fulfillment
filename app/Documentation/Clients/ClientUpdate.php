<?php
namespace App\Documentation\Clients;

use OpenApi\Annotations as OA;

/**
 * @OA\Put(
 *     path="/clients/{client}",
 *     tags={"Clients"},
 *     summary="Atualizar um cliente existente",
 *     description="Endpoint para atualizar as informações de um cliente.",
 *     @OA\Parameter(
 *         name="client",
 *         in="path",
 *         required=true,
 *         description="ID do cliente a ser atualizado",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="João Silva Atualizado"),
 *             @OA\Property(property="email", type="string", example="joao.silva@example.com"),
 *             @OA\Property(property="phone_whatsapp", type="string", example="11999999999"),
 *             @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
 *             @OA\Property(property="person_type", type="string", example="PF"),
 *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="password", type="string", example="novaSenha123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cliente atualizado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="client", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva Atualizado"),
 *                 @OA\Property(property="email", type="string", example="joao.silva@example.com")
 *             ),
 *             @OA\Property(property="message", type="string", example="Cliente atualizado com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao atualizar cliente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao atualizar cliente.")
 *         )
 *     )
 * )
 */


 class ClientUpdate {}
