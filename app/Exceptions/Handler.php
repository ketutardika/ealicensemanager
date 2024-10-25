<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var string[]
     */
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Customize response for API routes
        if ($request->expectsJson()) {
            return response()->json([
                'validation' => 'invalid',
                'message' => 'You need to be authenticated to access this resource.'
            ], 401);
        }

        // Default behavior for web routes (redirect)
        return redirect()->guest(route('login'));
    }
    
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var string[]
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }
}
