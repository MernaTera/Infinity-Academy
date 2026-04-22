<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Throwable;

class Handler extends ExceptionHandler
{
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        // 419 — CSRF Token Mismatch = session expired
        if ($e instanceof TokenMismatchException) {
            return redirect()
                ->route('login')
                ->with('session_expired', true);
        }

        return parent::render($request, $e);
    }
}