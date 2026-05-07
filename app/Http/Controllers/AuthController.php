<?php

namespace App\Http\Controllers;

use App\Models\User; // Gọi Model User để thêm dữ liệu vào DB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Thư viện mã hóa mật khẩu
use Illuminate\Support\Facades\Password; // Thư viện reset password
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered; // Sự kiện gửi mail xác thực
use Carbon\Carbon;

class AuthController extends Controller
{
    // --- 1. ĐĂNG NHẬP ---
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            // Kiểm tra tài khoản có bị vô hiệu hóa không
            if (!Auth::user()->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Tài khoản của bạn đã bị vô hiệu hóa. Vui lòng liên hệ quản trị viên.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            
            // Kiểm tra email đã xác thực chưa
            $user = Auth::user();
            if (!$user->email_verified_at) {
                // Kiểm tra xem có mã OTP còn hiệu lực không
                $existingCode = DB::table('password_reset_codes')
                    ->where('email', $user->email)
                    ->where('expires_at', '>', Carbon::now())
                    ->first();
                
                // Nếu không có mã hoặc mã đã hết hạn, tạo mã mới
                if (!$existingCode) {
                    $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
                    
                    DB::table('password_reset_codes')->where('email', $user->email)->delete();
                    DB::table('password_reset_codes')->insert([
                        'email' => $user->email,
                        'code' => $code,
                        'expires_at' => Carbon::now()->addMinutes(10),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);

                    // Gửi email mã OTP
                    Mail::send([], [], function ($message) use ($user, $code) {
                        $message->to($user->email)
                            ->subject('Xác thực tài khoản - Góc Bếp')
                            ->html("
                                <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;'>
                                    <h2 style='color: #3E5F4E; text-align: center;'>🍳 Góc Bếp</h2>
                                    <p>Xin chào <strong>{$user->name}</strong>,</p>
                                    <p>Đây là mã xác thực tài khoản của bạn:</p>
                                    <div style='background: #f5f5f5; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0;'>
                                        <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3E5F4E;'>{$code}</span>
                                    </div>
                                    <p style='color: #888; font-size: 14px;'>Mã này sẽ hết hạn sau 10 phút.</p>
                                </div>
                            ");
                    });

                    return redirect()->route('verification.notice')
                        ->with('status', 'Đã gửi mã xác thực mới vào email của bạn!');
                }
                
                // Nếu mã còn hiệu lực, chuyển đến trang xác thực
                return redirect()->route('verification.notice')
                    ->with('status', 'Vui lòng xác thực email để tiếp tục sử dụng.');
            }
            
            return redirect()->route('home'); // Đăng nhập xong về trang chủ
        }

        return back()->withErrors([
            'email' => 'Email hoặc mật khẩu không chính xác.',
        ])->onlyInput('email');
    }

    // --- 2. ĐĂNG KÝ ---
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        // Validate dữ liệu
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i'],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.regex' => 'Chỉ chấp nhận email @gmail.com. Vui lòng sử dụng địa chỉ Gmail.',
            'email.unique' => 'Email này đã được sử dụng.'
        ]);

        // Tạo user mới trong Database (chưa xác thực email, chưa kích hoạt)
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'is_active' => false, // Chưa kích hoạt cho đến khi xác thực OTP
        ]);

        // Tạo mã OTP 6 số ngẫu nhiên
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu mã OTP vào database
        DB::table('password_reset_codes')->where('email', $request->email)->delete();
        DB::table('password_reset_codes')->insert([
            'email' => $request->email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Gửi email chứa mã OTP
        Mail::send([], [], function ($message) use ($request, $code, $user) {
            $message->to($request->email)
                ->subject('Xác thực tài khoản - Góc Bếp')
                ->html("
                    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #3E5F4E; text-align: center;'>🍳 Góc Bếp</h2>
                        <p>Xin chào <strong>{$user->name}</strong>,</p>
                        <p>Cảm ơn bạn đã đăng ký tài khoản. Đây là mã xác thực của bạn:</p>
                        <div style='background: #f5f5f5; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0;'>
                            <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3E5F4E;'>{$code}</span>
                        </div>
                        <p style='color: #888; font-size: 14px;'>Mã này sẽ hết hạn sau 10 phút.</p>
                        <p style='color: #888; font-size: 14px;'>Nếu bạn không yêu cầu đăng ký, vui lòng bỏ qua email này.</p>
                    </div>
                ");
        });

        // Đăng nhập tạm cho người dùng
        Auth::login($user);

        // Chuyển hướng đến trang nhập mã OTP
        return redirect()->route('verification.notice')
            ->with('verify_email', $request->email)
            ->with('status', 'Đã gửi mã xác thực vào email của bạn!');
    }

    // --- 4. ĐĂNG XUẤT ---
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }

    // --- 5. ĐỔI MẬT KHẨU (Dành cho người đã đăng nhập) ---
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    public function changePassword(Request $request)
    {
        // Check if AJAX request
        $isAjax = $request->ajax() || $request->wantsJson();

        // 1. Validate dữ liệu đầu vào
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed|different:current_password',
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'new_password.required' => 'Vui lòng nhập mật khẩu mới.',
            'new_password.min' => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'new_password.confirmed' => 'Mật khẩu xác nhận không khớp.',
            'new_password.different' => 'Mật khẩu mới không được trùng với mật khẩu cũ.'
        ]);

        if ($validator->fails()) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        // 2. Kiểm tra mật khẩu hiện tại có đúng không
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            if ($isAjax) {
                return response()->json([
                    'success' => false,
                    'errors' => ['current_password' => ['Mật khẩu hiện tại không chính xác.']]
                ], 422);
            }
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        // 3. Cập nhật mật khẩu mới
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->password = Hash::make($request->new_password);
        $user->save();

        if ($isAjax) {
            return response()->json([
                'success' => true,
                'message' => 'Đổi mật khẩu thành công!'
            ]);
        }

        return back()->with('status', 'Đổi mật khẩu thành công!');
    }


    // --- 6. QUÊN MẬT KHẨU (QUA EMAIL) ---

    // Hiển thị form nhập email

    public function showForgotPasswordForm()
    {
        return view('auth.forgot-password');
    }

    // Gửi link reset password qua email

    // Gửi mã OTP qua email
    public function sendResetCode(Request $request)
    {
        // Nếu user đã đăng nhập, chỉ cho phép gửi mã về email của chính họ
        if (Auth::check()) {
            $user = Auth::user();
            $email = $user->email;
            
            // Kiểm tra nếu user nhập email khác với email đang đăng nhập
            if ($request->email && $request->email !== $email) {
                return back()->withErrors(['email' => 'Bạn chỉ có thể đặt lại mật khẩu cho tài khoản đang đăng nhập.']);
            }
        } else {
            // User chưa đăng nhập - validate email và tìm user
            $request->validate([
                'email' => ['required', 'email', 'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i']
            ], [
                'email.required' => 'Vui lòng nhập email.',
                'email.email' => 'Email không hợp lệ.',
                'email.regex' => 'Chỉ hỗ trợ email @gmail.com.'
            ]);

            $email = $request->email;
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                return back()->withErrors(['email' => 'Không tìm thấy email này trong hệ thống.']);
            }
        }

        // Tạo mã 6 số ngẫu nhiên
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Xóa mã cũ (nếu có) và lưu mã mới
        DB::table('password_reset_codes')->where('email', $email)->delete();
        DB::table('password_reset_codes')->insert([
            'email' => $email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Gửi email chứa mã OTP
        Mail::send([], [], function ($message) use ($email, $code, $user) {
            $message->to($email)
                ->subject('Mã xác thực đặt lại mật khẩu - Góc Bếp')
                ->html("
                    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #3E5F4E; text-align: center;'>🍳 Góc Bếp</h2>
                        <p>Xin chào <strong>{$user->name}</strong>,</p>
                        <p>Bạn đã yêu cầu đặt lại mật khẩu. Đây là mã xác thực của bạn:</p>
                        <div style='background: #f5f5f5; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0;'>
                            <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3E5F4E;'>{$code}</span>
                        </div>
                        <p style='color: #888; font-size: 14px;'>Mã này sẽ hết hạn sau 10 phút.</p>
                        <p style='color: #888; font-size: 14px;'>Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.</p>
                    </div>
                ");
        });

        // Lưu email vào session và chuyển đến trang nhập mã
        return redirect()->route('password.verify.form')
            ->with('reset_email', $email)
            ->with('status', 'Đã gửi mã xác thực vào email của bạn!');
    }

    // Hiển thị form nhập mã OTP
    public function showVerifyCodeForm(Request $request)
    {
        $email = session('reset_email') ?? $request->email;
        if (!$email) {
            return redirect()->route('password.request');
        }
        return view('auth.verify-code', ['email' => $email]);
    }

    // Xác thực mã OTP
    public function verifyCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'code' => 'required|string|size:6',
        ]);

        $record = DB::table('password_reset_codes')
            ->where('email', $request->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return back()
                ->with('reset_email', $request->email)
                ->withErrors(['code' => 'Mã xác thực không đúng hoặc đã hết hạn.']);
        }

        // Mã đúng - lưu vào session và chuyển đến form đổi mật khẩu
        session(['verified_email' => $request->email, 'verified_code' => $request->code]);
        return redirect()->route('password.reset.form');
    }

    // Gửi lại mã OTP
    public function resendCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        // Gọi lại hàm sendResetCode
        return $this->sendResetCode($request);
    }

    // Hiển thị form đặt lại mật khẩu mới (sau khi xác thực mã)
    public function showResetPasswordForm(Request $request)
    {
        $email = session('verified_email');
        if (!$email) {
            return redirect()->route('password.request');
        }
        return view('auth.reset-password', ['email' => $email]);
    }

    // Xử lý đặt lại mật khẩu mới
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6|confirmed',
        ], [
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.'
        ]);

        // Kiểm tra email đã được verify chưa
        $verifiedEmail = session('verified_email');
        if ($verifiedEmail !== $request->email) {
            return back()->withErrors(['email' => 'Phiên xác thực không hợp lệ. Vui lòng thử lại.']);
        }

        // Cập nhật mật khẩu
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return back()->withErrors(['email' => 'Không tìm thấy tài khoản.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Xóa mã đã sử dụng và session
        DB::table('password_reset_codes')->where('email', $request->email)->delete();
        session()->forget(['verified_email', 'verified_code', 'reset_email']);

        return redirect()->route('login')->with('status', 'Đổi mật khẩu thành công! Vui lòng đăng nhập lại.');
    }

    // --- 7. XÁC THỰC EMAIL KHI ĐĂNG KÝ (OTP) ---

    // Xác thực mã OTP khi đăng ký
    public function verifyRegistrationCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ], [
            'code.required' => 'Vui lòng nhập mã xác thực.',
            'code.size' => 'Mã xác thực phải có 6 số.'
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'Vui lòng đăng nhập lại.']);
        }

        $record = DB::table('password_reset_codes')
            ->where('email', $user->email)
            ->where('code', $request->code)
            ->where('expires_at', '>', Carbon::now())
            ->first();

        if (!$record) {
            return back()
                ->with('verify_email', $user->email)
                ->withErrors(['code' => 'Mã xác thực không đúng hoặc đã hết hạn.']);
        }

        // Mã đúng - xác thực email và kích hoạt tài khoản
        $user->email_verified_at = Carbon::now();
        $user->is_active = true; // Kích hoạt tài khoản
        $user->save();

        // Xóa mã đã sử dụng
        DB::table('password_reset_codes')->where('email', $user->email)->delete();

        return redirect()->route('home')->with('success', 'Xác thực email thành công! Chào mừng bạn đến với Góc Sách.');
    }

    // Gửi lại mã OTP khi đăng ký
    public function resendRegistrationCode(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Tạo mã mới
        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Lưu mã mới
        DB::table('password_reset_codes')->where('email', $user->email)->delete();
        DB::table('password_reset_codes')->insert([
            'email' => $user->email,
            'code' => $code,
            'expires_at' => Carbon::now()->addMinutes(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        // Gửi email
        Mail::send([], [], function ($message) use ($user, $code) {
            $message->to($user->email)
                ->subject('Mã xác thực tài khoản - Góc Bếp')
                ->html("
                    <div style='font-family: Arial, sans-serif; max-width: 500px; margin: 0 auto; padding: 20px;'>
                        <h2 style='color: #3E5F4E; text-align: center;'>🍳 Góc Bếp</h2>
                        <p>Xin chào <strong>{$user->name}</strong>,</p>
                        <p>Đây là mã xác thực mới của bạn:</p>
                        <div style='background: #f5f5f5; padding: 20px; text-align: center; border-radius: 10px; margin: 20px 0;'>
                            <span style='font-size: 32px; font-weight: bold; letter-spacing: 8px; color: #3E5F4E;'>{$code}</span>
                        </div>
                        <p style='color: #888; font-size: 14px;'>Mã này sẽ hết hạn sau 10 phút.</p>
                    </div>
                ");
        });

        return back()
            ->with('verify_email', $user->email)
            ->with('status', 'Đã gửi lại mã xác thực mới!');
    }
}