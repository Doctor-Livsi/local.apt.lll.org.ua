<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        // Перевіряємо токен із заголовка Authorization
        $token = $request->bearerToken();

        if (!$token) {
            // Якщо токена немає, перевіряємо localStorage/sessionStorage через клієнт
            return redirect('/login');
        }

        // Перевіряємо авторизацію через Sanctum
        if (!Auth::guard('sanctum')->check()) {
            return redirect('/login');
        }

        return $next($request);
    }
}