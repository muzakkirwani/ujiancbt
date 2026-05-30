<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Setting;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            return $user->role === 'admin' 
                ? redirect()->route('admin.dashboard') 
                : redirect()->route('pengawas.dashboard');
        }

        if (Auth::guard('siswa')->check()) {
            return redirect()->route('siswa.dashboard');
        }

        // Self-healing: automatically repair legacy plain-text passwords without resetting custom passwords or running slow Bcrypt checks
        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            try {
                \Illuminate\Support\Facades\DB::statement("ALTER TABLE `users` MODIFY COLUMN `mata_pelajaran` TEXT NULL");
            } catch (\Exception $e) {}

            $isBcrypt = fn($hash) => is_string($hash) && str_starts_with($hash, '$2y$');

            // Only hash admin password if it's not already a valid Bcrypt hash
            $admin = \App\Models\User::where('username', 'admin')->first();
            if ($admin && !$isBcrypt($admin->password)) {
                $admin->password = \Illuminate\Support\Facades\Hash::make('admin');
                $admin->save();
            }

            // Only hash user password if it's not already a valid Bcrypt hash
            $user_admin = \App\Models\User::where('username', 'user')->first();
            if ($user_admin && !$isBcrypt($user_admin->password)) {
                $user_admin->password = \Illuminate\Support\Facades\Hash::make('admin');
                $user_admin->save();
            }

            // Sync legacy plain-text student passwords in bulk, but only if they are not already properly hashed
            $unhashedStudents = \App\Models\Siswa::whereNotNull('password_view')->get();
            foreach ($unhashedStudents as $s) {
                if (!$isBcrypt($s->password)) {
                    $s->password = \Illuminate\Support\Facades\Hash::make($s->password_view);
                    $s->save();
                }
            }
        } catch (\Throwable $e) {
            // Silence if table/connection not ready or database is empty
        }

        $settings = Setting::first();

        // Jadwal ujian hari ini dan besok
        $today    = \Carbon\Carbon::today()->format('Y-m-d');
        $tomorrow = \Carbon\Carbon::tomorrow()->format('Y-m-d');
        $ujian_besok = \App\Models\Ujian::with(['kelas', 'sesi'])
            ->whereIn('tanggal', [$today, $tomorrow])
            ->orderBy('tanggal')
            ->orderBy('sesi_id')
            ->get();

        return view('auth.login', compact('settings', 'ujian_besok'));
    }

    public function login(Request $request)
    {
        $logFile = storage_path('logs/login_debug.log');
        $time = date('Y-m-d H:i:s');
        $ip = $request->ip();
        
        file_put_contents($logFile, "[{$time}] [IP: {$ip}] Login attempt started for username: {$request->username}\n", FILE_APPEND);
        file_put_contents($logFile, "[{$time}] Session ID at start: " . $request->session()->getId() . "\n", FILE_APPEND);

        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // 1. Try admin/pengawas guard (web)
        file_put_contents($logFile, "[{$time}] Attempting guard 'web'...\n", FILE_APPEND);
        if (Auth::guard('web')->attempt($credentials, $request->boolean('remember'))) {
            $oldSessionId = $request->session()->getId();
            $request->session()->regenerate();
            $newSessionId = $request->session()->getId();
            
            $user = Auth::guard('web')->user();
            file_put_contents($logFile, "[{$time}] Guard 'web' SUCCESS. User ID: {$user->id}, Role: {$user->role}. Session regenerated from {$oldSessionId} to {$newSessionId}\n", FILE_APPEND);
            
            if ($user->role === 'admin') {
                file_put_contents($logFile, "[{$time}] Redirecting admin to admin.dashboard\n", FILE_APPEND);
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'pengawas') {
                file_put_contents($logFile, "[{$time}] Redirecting pengawas to pengawas.dashboard\n", FILE_APPEND);
                return redirect()->route('pengawas.dashboard');
            }
            
            file_put_contents($logFile, "[{$time}] Guard 'web' matched user but role '{$user->role}' is not admin/pengawas. Logging out...\n", FILE_APPEND);
            Auth::guard('web')->logout();
        } else {
            file_put_contents($logFile, "[{$time}] Guard 'web' FAILED.\n", FILE_APPEND);
        }

        // 2. Try student guard (siswa)
        file_put_contents($logFile, "[{$time}] Attempting guard 'siswa'...\n", FILE_APPEND);
        if (Auth::guard('siswa')->attempt($credentials, $request->boolean('remember'))) {
            $oldSessionId = $request->session()->getId();
            $request->session()->regenerate();
            $newSessionId = $request->session()->getId();
            
            file_put_contents($logFile, "[{$time}] Guard 'siswa' SUCCESS. Session regenerated from {$oldSessionId} to {$newSessionId}\n", FILE_APPEND);
            return redirect()->route('siswa.dashboard');
        } else {
            file_put_contents($logFile, "[{$time}] Guard 'siswa' FAILED.\n", FILE_APPEND);
        }

        // DEBUG ASSISTANT: Get exact reason for failure
        $debug_msg = 'Username atau Password salah!';
        try {
            $user_check = \App\Models\User::where('username', $request->username)->first();
            if ($user_check) {
                $isBcrypt = is_string($user_check->password) && str_starts_with($user_check->password, '$2y$');
                $pw_match = $isBcrypt && \Illuminate\Support\Facades\Hash::check($request->password, $user_check->password);
                $debug_msg .= ' (User found in users table. Role: ' . $user_check->role . '. Hash check: ' . ($pw_match ? 'MATCH' : 'MISMATCH') . ')';
            } else {
                $siswa_check = \App\Models\Siswa::where('username', $request->username)->first();
                if ($siswa_check) {
                    $isBcrypt = is_string($siswa_check->password) && str_starts_with($siswa_check->password, '$2y$');
                    $pw_match = $isBcrypt && \Illuminate\Support\Facades\Hash::check($request->password, $siswa_check->password);
                    $debug_msg .= ' (User found in siswa table. Hash check: ' . ($pw_match ? 'MATCH' : 'MISMATCH') . ')';
                } else {
                    $debug_msg .= ' (Username not found in users or siswa tables)';
                }
            }
        } catch (\Throwable $e) {
            $debug_msg .= ' (Debug check error: ' . $e->getMessage() . ')';
        }

        file_put_contents($logFile, "[{$time}] Login failed overall. Debug message: {$debug_msg}\n", FILE_APPEND);

        return back()->withErrors([
            'error' => $debug_msg,
        ])->onlyInput('username');
    }

    public function logout(Request $request)
    {
        if (Auth::guard('web')->check()) {
            Auth::guard('web')->logout();
        } elseif (Auth::guard('siswa')->check()) {
            Auth::guard('siswa')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
