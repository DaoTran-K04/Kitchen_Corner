<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            // Chỉ kick nếu tài khoản ĐÃ xác thực email nhưng bị admin khoá.
            // Tài khoản chưa xác thực OTP (email_verified_at = null + is_active = false)
            // KHÔNG bị redirect về /banned – để họ hoàn thành xác thực.
            if (!$user->is_active && $user->email_verified_at !== null) {
                $reason = $user->ban_reason ?? 'Vi phạm tiêu chuẩn cộng đồng.';
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('banned')->with('ban_reason', $reason);
            }
        }

        return $next($request);
    }
}
