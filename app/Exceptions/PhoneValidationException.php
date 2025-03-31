<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Support\Facades\Log;

class PhoneValidationException extends Exception
{
    protected array $context = [];

    public function __construct(string $message, int $code = 0, array $context = [])
    {
        parent::__construct($message, $code);
        $this->context = $context;
    }

    public function report(): void
    {
        Log::error('Phone validation error', [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'context' => $this->context,
            'trace' => $this->getTraceAsString()
        ]);
    }

    public function render($request)
    {
        $statusCode = $this->getCode() ?: 422;

        return response()->json([
            'message' => 'Phone validation failed',
            'errors' => [
                'phone' => [$this->getMessage()]
            ],
            'code' => $statusCode
        ], $statusCode);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
