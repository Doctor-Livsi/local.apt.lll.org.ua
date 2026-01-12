<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Authenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            Log::info('Перевірка авторизації', [
                'auth_check' => Auth::check(),
                'user' => Auth::user(),
                'session_id' => $request->session()->getId(),
                'laravel_session' => $request->cookie('laravel_session'),
            ]);
            return route('login');
        }
    }
}