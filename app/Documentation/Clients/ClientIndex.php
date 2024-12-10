<?php
namespace App\Documentation\Clients;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/clients",
 *     tags={"Clients"},
 *     summary="Listar todos os clientes",
 *     description="Retorna uma lista de clientes cadastrados.",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de clientes retornada com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="clients", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array", @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="name", type="string", example="João Silva"),
 *                     @OA\Property(property="email", type="string", example="joao@example.com"),
 *                     @OA\Property(property="phone_whatsapp", type="string", example="11999999999"),
 *                     @OA\Property(property="cpf_cnpj", type="string", example="12345678900"),
 *                     @OA\Property(property="person_type", type="string", example="PF"),
 *                     @OA\Property(property="birth_date", type="string", format="date", example="1990-01-01"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *                 ))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao listar clientes.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao listar clientes.")
 *         )
 *     )
 * )
 */

 class ClientIndex {}
