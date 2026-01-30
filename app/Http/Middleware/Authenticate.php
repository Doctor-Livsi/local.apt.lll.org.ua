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
                'auth_check_web' => Auth::guard('web')->check(),
                'user_web' => Auth::guard('web')->user(),
                'session_id' => $request->session()->getId(),
                'laravel_session' => $request->cookie('laravel_session'),
            ]);

            return route('login');
        }
    }
}
