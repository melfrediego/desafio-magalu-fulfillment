<?php
namespace App\Documentation\Clients;

use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/clients/{client}",
 *     tags={"Clients"},
 *     summary="Excluir um cliente",
 *     description="Endpoint para excluir um cliente pelo ID.",
 *     @OA\Parameter(
 *         name="client",
 *         in="path",
 *         required=true,
 *         description="ID do cliente a ser excluído",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Cliente excluído com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Cliente excluído com sucesso.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao excluir cliente.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao excluir cliente.")
 *         )
 *     )
 * )
 */

 class ClientDestroy {}
