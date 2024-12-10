<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClientRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    /**
     * Listar todos os clientes.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $clients = User::where('is_client', true)->orderBy('id', 'DESC')->paginate(10);

            return response()->json([
                'status' => true,
                'clients' => $clients,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao listar clientes.',
            ], 500);
        }
    }

    /**
     * Criar um novo cliente.
     *
     * @param ClientRequest $request
     * @return JsonResponse
     */
    public function store(ClientRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $client = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone_whatsapp' => $request->phone_whatsapp,
                'birth_date' => $request->birth_date,
                'cpf_cnpj' => $request->cpf_cnpj,
                'person_type' => $request->person_type,
                'is_client' => true,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'client' => $client,
                'message' => 'Cliente cadastrado com sucesso!',
            ], 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao criar cliente.',
            ], 400);
        }
    }

    /**
     * Exibir um cliente específico.
     *
     * @param User $client
     * @return JsonResponse
     */
    public function show(User $client): JsonResponse
    {
        try {
            if (!$client->is_client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuário não é um cliente.',
                ], 404);
            }

            return response()->json([
                'status' => true,
                'client' => $client,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao exibir cliente.',
            ], 500);
        }
    }

    /**
     * Atualizar informações de um cliente.
     *
     * @param ClientRequest $request
     * @param User $client
     * @return JsonResponse
     */
    public function update(ClientRequest $request, User $client): JsonResponse
    {
        DB::beginTransaction();
        try {
            $client->update($request->only([
                'name',
                'email',
                'phone_whatsapp',
                'birth_date',
                'cpf_cnpj',
                'person_type',
            ]));

            if ($request->filled('password')) {
                $client->update(['password' => Hash::make($request->password)]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'client' => $client,
                'message' => 'Cliente atualizado com sucesso!',
            ], 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao atualizar cliente.',
            ], 400);
        }
    }

    /**
     * Excluir um cliente.
     *
     * @param User $client
     * @return JsonResponse
     */
    public function destroy(User $client): JsonResponse
    {
        try {
            if (!$client->is_client) {
                return response()->json([
                    'status' => false,
                    'message' => 'Usuário não é um cliente.',
                ], 404);
            }

            $client->delete();

            return response()->json([
                'status' => true,
                'message' => 'Cliente excluído com sucesso.',
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao excluir cliente.',
            ], 500);
        }
    }
}
