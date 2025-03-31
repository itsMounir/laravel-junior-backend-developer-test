<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\{
    QueryException,
    Eloquent\ModelNotFoundException,
    UniqueConstraintViolationException,
};
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
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

    public function render($request, Exception|Throwable $e)
    {

        if ($e instanceof ValidationException) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }


        if ($e instanceof ModelNotFoundException) {
            return response()->json([
                'message' => explode('\\', $e->getModel())[2] . ' Not Found.',
            ], 404);
        }

        if ($e instanceof AuthorizationException) {
            return response()->json([
                'message' => 'This action is unauthorized.',
            ], 403);
        }

        if ($e instanceof UniqueConstraintViolationException) {
            return response()->json([
                'message' => 'This record is already exists.',
            ], 400);
        }

        /*if ($e instanceof QueryException) {
            return response()->json([
                'message' => 'Unknown sql error.',
            ], 400);
        }*/

        if ($e instanceof RouteNotFoundException) {
            return response()->json([
                'message' => 'unauthenticated',
            ], 401);
        }

        if ($e instanceof JobTimeoutException) {
            // Log the job timeout exception
            Log::error('JobTimeoutException: ' . $e->getMessage());
            // Return an appropriate response (e.g., 504 Gateway Timeout)
            return response()->json([
                'message' => 'Something went wrong, please try again later.'
            ], 504);
        }

        return parent::render($request, $e);
    }
}
