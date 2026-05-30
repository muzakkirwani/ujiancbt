<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $logFile = storage_path('logs/login_debug.log');
        $time = date('Y-m-d H:i:s');
        $path = $request->fullUrl();
        $sessId = $request->session()->getId();
        
        file_put_contents($logFile, "[{$time}] CheckRole middleware triggered for path: {$path}. Session ID: {$sessId}. Required roles: " . implode(', ', $roles) . "\n", FILE_APPEND);

        // 1. Check if user is logged in under default web guard (Admin or Pengawas)
        $webChecked = Auth::guard('web')->check();
        file_put_contents($logFile, "[{$time}] Auth::guard('web')->check(): " . ($webChecked ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
        if ($webChecked) {
            $user = Auth::guard('web')->user();
            file_put_contents($logFile, "[{$time}] Logged in user ID: {$user->id}, Role: {$user->role}\n", FILE_APPEND);
            if (in_array($user->role, $roles)) {
                file_put_contents($logFile, "[{$time}] Role match! Access granted.\n", FILE_APPEND);
                return $next($request);
            } else {
                file_put_contents($logFile, "[{$time}] Role mismatch. User role: {$user->role}, required roles: " . implode(', ', $roles) . "\n", FILE_APPEND);
            }
        }

        // 2. Check if student is logged in under siswa guard
        $siswaChecked = Auth::guard('siswa')->check();
        file_put_contents($logFile, "[{$time}] Auth::guard('siswa')->check(): " . ($siswaChecked ? 'TRUE' : 'FALSE') . "\n", FILE_APPEND);
        if ($siswaChecked) {
            file_put_contents($logFile, "[{$time}] Logged in student ID: " . Auth::guard('siswa')->id() . "\n", FILE_APPEND);
            if (in_array('siswa', $roles)) {
                file_put_contents($logFile, "[{$time}] Student role match! Access granted.\n", FILE_APPEND);
                return $next($request);
            }
        }

        // Not authorized: redirect to login page with error
        file_put_contents($logFile, "[{$time}] Unauthorised access. Redirecting back to login...\n", FILE_APPEND);
        return redirect()->route('login')->withErrors([
            'error' => 'Anda tidak memiliki akses ke halaman tersebut!',
        ]);
    }
}
