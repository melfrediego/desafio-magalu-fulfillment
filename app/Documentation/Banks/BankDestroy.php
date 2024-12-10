<?php
namespace App\Documentation\Banks;

use OpenApi\Annotations as OA;

/**
 * @OA\Delete(
 *     path="/banks/{bank}",
 *     tags={"Banks"},
 *     summary="Excluir um banco",
 *     description="Endpoint para excluir um banco pelo ID.",
 *     @OA\Parameter(
 *         name="bank",
 *         in="path",
 *         required=true,
 *         description="ID do banco a ser excluído",
 *         @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Banco excluído com sucesso.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=true),
 *             @OA\Property(property="message", type="string", example="Banco excluído com sucesso.")
 *         )
 *     ),
 *     @OA\Response(
 *         response=500,
 *         description="Erro ao excluir banco.",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean", example=false),
 *             @OA\Property(property="message", type="string", example="Erro ao excluir banco.")
 *         )
 *     )
 * )
 */

 class BankDestroy {}
