<?php
namespace App\Documentation\Clients;

use OpenApi\Annotations as OA;

/**
 * @OA\Post(
 *     path="/clients",
 *     tags={"Clients"},
 *     summary="Criar um novo cliente",
 *     description="Endpoint para cadastrar um novo cliente.",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "cpf_cnpj", "person_type"},
 *             @OA\Property(property="name", type="string", example="João Silva"),
 *             @OA\Property(property="email", type="string", example="joao@example.com"),
 *             @OA\Property(property="password", type="string", example="senha123"),
 *             @OA\Property(property="password_confirmation", type="string", example="senha123"),
 *             @OA\Property(property="phone_whatsapp", type="string", example="11999999999"),
 *             @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *             @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
 *             @OA\Property(property="person_type", type="string", example="PF")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Cliente cadastrado com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="client", type="object",
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="name", type="string", example="João Silva"),
 *                 @OA\Property(property="email", type="string", example="joao@example.com"),
 *                 @OA\Property(property="phone_whatsapp", type="string", example="11999999999"),
 *                 @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
 *                 @OA\Property(property="person_type", type="string", example="PF"),
 *                 @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *             ),
 *             @OA\Property(property="message", type="string", example="Cliente cadastrado com sucesso!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Erro ao criar cliente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao criar cliente.")
 *         )
 *     )
 * )
 */

 class ClientStore {}
