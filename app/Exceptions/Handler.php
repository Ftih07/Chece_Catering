<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register any exception handling callbacks for the application.
     */
    public function register(): void
    {
        //
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // Tangani error HTTP seperti 403, 404, 503
        if ($exception instanceof HttpExceptionInterface) {
            $statusCode = $exception->getStatusCode();

            if (in_array($statusCode, [403, 404, 500, 503])) {
                // Cek apakah view error tersebut ada
                if (view()->exists("errors.$statusCode")) {
                    return response()->view("errors.$statusCode", [], $statusCode);
                }
            }
        }

        // Tangani error selain itu (error tak terduga) → arahkan ke 500
        return response()->view("errors.500", [], 500);

        // Atau, jika ingin tetap pakai default Laravel fallback:
        // return parent::render($request, $exception);
    }
}
