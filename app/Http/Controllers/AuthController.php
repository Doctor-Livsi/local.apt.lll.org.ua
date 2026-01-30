<?php

namespace App\Http\Controllers;

use App\Models\Users;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\PersonalAccessToken;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $method = 'web';
//        $method = $request->is('api/*') ? 'api' : 'web';
        $email = $request->input('email');

        Carbon::setLocale('uk');

        $currentTime = now();
        $block = DB::table('block_logs')
            ->where('blocked_until', '>', $currentTime)
            ->where(function ($query) use ($ipAddress, $email) {
                $query->where('type', 'ip')->where('value', $ipAddress)
                    ->orWhere('type', 'email')->where('value', $email);
            })
            ->first();

        if ($block) {
            $blockedUntilFormatted = Carbon::parse($block->blocked_until)
                ->translatedFormat('H:i:s d F Y');
            return response()->json([
                'success' => false,
                'message' => "Доступ заблоковано до {$blockedUntilFormatted}",
            ], 200);
        }

        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
                'redirect' => 'nullable|string',
            ]);
        } catch (ValidationException $e) {
            DB::table('login_attempts')->insert([
                'email' => $email,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'method' => $method,
                'success' => false,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Помилка валідації',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = Users::where('email', $request->email)->first();

        $success = $user && Hash::check($request->password, $user->password);

        DB::table('login_attempts')->insert([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'method' => $method,
            'success' => $success,
            'created_at' => now(),
        ]);

        if (!$success) {
            // Знаходимо останню успішну спробу для цього IP
            $lastSuccess = DB::table('login_attempts')
                ->where('ip_address', $ipAddress)
                ->where('success', true)
                ->orderBy('created_at', 'desc')
                ->first();

            // Підраховуємо невдалі спроби після останньої успішної спроби
            $failedAttemptsQuery = DB::table('login_attempts')
                ->where('ip_address', $ipAddress)
                ->where('success', false)
                ->where('created_at', '>=', now()->subMinutes(30));

            if ($lastSuccess) {
                $failedAttemptsQuery->where('created_at', '>', $lastSuccess->created_at);
            }

            $failedAttempts = $failedAttemptsQuery->count();

            if ($failedAttempts >= 3) {
                DB::table('block_logs')->insert([
                    'type' => 'ip',
                    'value' => $ipAddress,
                    'blocked_until' => now()->addMinutes(30),
                    'created_at' => now(),
                ]);
                $blockedUntilFormatted = now()->addMinutes(30)->translatedFormat('H:i:s d F Y');
                return response()->json([
                    'success' => false,
                    'message' => "IP заблоковано на 30 хвилин до {$blockedUntilFormatted}",
                ], 200);
            }

            if ($failedAttempts == 2) {
                return response()->json([
                    'success' => false,
                    'message' => 'Залишилася остання спроба перед блокуванням!',
                ], 200);
            }

            $recentAttempts = DB::table('login_attempts')
                ->where('ip_address', $ipAddress)
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();

            if ($recentAttempts >= 10) {
                DB::table('block_logs')->insert([
                    'type' => 'ip',
                    'value' => $ipAddress,
                    'blocked_until' => now()->addMinutes(60),
                    'created_at' => now(),
                ]);
                $blockedUntilFormatted = now()->addMinutes(60)->translatedFormat('H:i:s d F Y');
                return response()->json([
                    'success' => false,
                    'message' => "IP заблоковано на 60 хвилин до {$blockedUntilFormatted}",
                ], 200);
            }

            $distinctIps = DB::table('login_attempts')
                ->where('email', $email)
                ->where('created_at', '>=', now()->subMinutes(30))
                ->distinct()
                ->pluck('ip_address')
                ->count();

            if ($distinctIps > 1) {
                DB::table('block_logs')->insert([
                    'type' => 'email',
                    'value' => $email,
                    'blocked_until' => now()->addMinutes(30),
                    'created_at' => now(),
                ]);
                $blockedUntilFormatted = now()->addMinutes(30)->translatedFormat('H:i:s d F Y');
                return response()->json([
                    'success' => false,
                    'message' => "Обліковий запис заблоковано до {$blockedUntilFormatted}",
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'Невірний email або пароль.',
            ], 200);
        }

        Auth::shouldUse('web');
        Auth::login($user);

        $request->session()->regenerate();
        $request->session()->save();
        $token = $user->createToken('auth-token')->plainTextToken;

        Log::info('Користувач авторизований', [
            'user_id' => $user->id,
            'auth_check' => Auth::guard('web')->check(),
            'user' => Auth::guard('web')->user(),
            'token' => $token,
        ]);

        $redirect = $request->input('redirect', '/');
        if ($redirect === '/login') {
            $redirect = '/';
        }

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
            'redirect' => $redirect,
        ], 200);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('web')->user();
        if ($user) {
            $user->tokens()->delete();
        }

        $sessionId = $request->session()->getId();

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($sessionId) {
            \Illuminate\Support\Facades\DB::table('sessions')
                ->where('id', $sessionId)
                ->delete();
        } else {
            Log::warning('Не вдалося отримати sessionId для видалення сесії');
        }

        Log::info('Користувач вийшов', [
            'user_id' => Auth::guard('web')->id(),
            'auth_check' => Auth::guard('web')->check(),
            'session_id' => $sessionId ?? 'немає sessionId',
        ]);
        return redirect('/login');
//        return response()->json(['success' => true]);
    }
    public function loginWeb(Request $request)
    {
        // ВАЖНО: это web endpoint, но возвращаем тот же JSON, который у тебя уже работает
        return $this->login($request);
    }

    public function loginApi(Request $request)
    {
        $ipAddress = $request->ip();
        $userAgent = $request->userAgent();
        $email = $request->input('email');

        Carbon::setLocale('uk');

        // Блокировки (то же, что в login())
        $currentTime = now();
        $block = DB::table('block_logs')
            ->where('blocked_until', '>', $currentTime)
            ->where(function ($query) use ($ipAddress, $email) {
                $query->where('type', 'ip')->where('value', $ipAddress)
                    ->orWhere('type', 'email')->where('value', $email);
            })
            ->first();

        if ($block) {
            $blockedUntilFormatted = Carbon::parse($block->blocked_until)->translatedFormat('H:i:s d F Y');
            return response()->json([
                'success' => false,
                'message' => "Доступ заблоковано до {$blockedUntilFormatted}",
            ], 200);
        }

        // Валидация
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);
        } catch (ValidationException $e) {
            DB::table('login_attempts')->insert([
                'email' => $email,
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
                'method' => 'api',
                'success' => false,
                'created_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Помилка валідації',
                'errors' => $e->errors(),
            ], 422);
        }

        // Проверка пользователя/пароля
        $user = Users::where('email', $request->email)->first();
        $success = $user && Hash::check($request->password, $user->password);

        DB::table('login_attempts')->insert([
            'email' => $email,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'method' => 'api',
            'success' => $success,
            'created_at' => now(),
        ]);

        if (!$success) {
            return response()->json([
                'success' => false,
                'message' => 'Невірний email або пароль.',
            ], 200);
        }

        // Только токен. Сессию не трогаем.
        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

}
