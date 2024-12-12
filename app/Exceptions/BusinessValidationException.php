<?php

namespace App\Exceptions;

use Exception;

class BusinessValidationException extends Exception
{
    /**
     * Construtor personalizado para a exceção de validação de negócios.
     *
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, int $code = 0)
    {
        parent::__construct($message, $code);
    }

    /**
     * Renderiza a exceção para uma resposta HTTP.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function render($request)
    {
        return response()->json([
            'error' => 'Validation Error',
            'message' => $this->getMessage(),
        ], 422);
    }
}
