<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Listar todos os usuários.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() : JsonResponse
    {
        try {
            $users = User::orderBy('id', 'DESC')->paginate(10);
            return response()->json([
                'status' => true,
                'users' => $users
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Erro ao listar usuários.'
            ], 404);
        }
    }

    /**
     * Criar um novo usuário.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UserRequest $request) : JsonResponse
    {
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário cadastrado com sucesso!'
            ], 201);

            return response()->json($user, 201);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao criar usuário.'
            ], 400);
        }
    }

    /**
     * Exibir um usuário específico.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(User $user) : JsonResponse
    {
        try {
            return response()->json([
                'status' => true,
                'user' => $user
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Usuário não encontrado.'
            ], 404);
        }
    }

    /**
     * Atualizar informações de um usuário.
     *
     * @param Request $request
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UserRequest $request, User $user) : JsonResponse
    {
        DB::beginTransaction();

        try {

            $data = $request->only(['name', 'email']);
            if ($request->has('password')) {
                $data['password'] = Hash::make($request->password);
            }

            $user->update($data);

            DB::commit();

            return response()->json([
                'status' => true,
                'user' => $user,
                'message' => 'Usuário editado com sucesso!'
            ], 200);

            return response()->json($user, 200);
        } catch (Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Erro ao editar usuário.'
            ], 400);
        }
    }

    /**
     * Excluir um usuário.
     *
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json([
                'status' => true,
                'message' => 'Usuário excluído com sucesso.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => 'Erro ao excluir usuário.'
            ], 500);
        }
    }
}
