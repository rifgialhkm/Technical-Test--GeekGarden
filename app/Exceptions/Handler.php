<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

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
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (NotFoundHttpException $e, $request) {
            return response()->json([
                'success' => false,
                'code' => 404,
                'message' => 'Pertanyaan tidak ditemukan.'
            ], Response::HTTP_NOT_FOUND);
        });

        $this->renderable(function (AuthenticationException $e, $request) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Invalid token. Token tidak ditemukan di database.'
            ], Response::HTTP_UNAUTHORIZED);
        });
    }
}
