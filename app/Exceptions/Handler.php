<?php

namespace App\Exceptions;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'message' => 'Erro de validação.',
            'erros' => $exception->errors(),
        ], $exception->status);
    }

    public function render($request, Throwable $exception)
    {
        // Personaliza o erro para ModelNotFoundException
        if ($exception instanceof ModelNotFoundException) {
            return response()->json([
                'status' => false,
                'message' => 'Recurso não encontrado.',
                'error' => $exception->getMessage(),
            ], 404);
        }

        // Personaliza o erro para NotFoundHttpException
        if ($exception instanceof NotFoundHttpException) {
            return response()->json([
                'status' => false,
                'message' => 'URL ou endpoint não encontrado.',
            ], 404);
        }

        // Para outros casos, use a lógica padrão
        return parent::render($request, $exception);
    }
}
