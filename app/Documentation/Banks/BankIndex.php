<?php
namespace App\Documentation\Banks;

use OpenApi\Annotations as OA;

/**
 * @OA\Get(
 *     path="/banks",
 *     tags={"Banks"},
 *     summary="Listar todos os bancos",
 *     description="Retorna uma lista de bancos cadastrados.",
 *     @OA\Response(
 *         response=200,
 *         description="Lista de bancos retornada com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="banks", type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="data", type="array", @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=1),
 *                     @OA\Property(property="code", type="string", example="001"),
 *                     @OA\Property(property="name", type="string", example="Banco do Brasil"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2024-12-10T10:00:00Z")
 *                 ))
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Erro ao listar bancos.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao listar bancos.")
 *         )
 *     )
 * )
 */

 class BankIndex {}
