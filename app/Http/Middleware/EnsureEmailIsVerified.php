<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmailIsVerified
{
    /**
     * Handle an incoming request.
     * Kiểm tra email đã xác thực chưa, hỗ trợ cả AJAX requests
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if (!$user || !$user->email_verified_at || !$user->is_active) {
            // Nếu là AJAX request, trả về JSON
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vui lòng xác thực email để sử dụng tính năng này.',
                    'redirect' => route('verification.notice')
                ], 403);
            }
            
            // Nếu không phải AJAX, redirect về trang xác thực
            return redirect()->route('verification.notice')
                ->with('status', 'Vui lòng xác thực email để tiếp tục sử dụng.');
        }

        return $next($request);
    }
}
