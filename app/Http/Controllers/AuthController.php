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
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255'],
            'password' => 'required|string|min:6|confirmed',
        ], [
            'email.required'  => 'Vui lòng nhập địa chỉ email.',
            'email.email'     => 'Email không hợp lệ.',
            'email.unique'    => 'Email này đã được sử dụng.',
            'password.min'    => 'Mật khẩu phải có ít nhất 6 ký tự.',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp.',
        ]);

        // Kiểm tra email đã tồn tại chưa
        $existingUser = User::where('email', $request->email)->first();

        // Nếu email tồn tại nhưng chưa xác thực OTP (chưa activate)
        if ($existingUser && !$existingUser->email_verified_at) {
            // Cập nhật mật khẩu mới (trường hợp họ nhập sai pass làn trước)
            $existingUser->name     = $request->name;
            $existingUser->password = Hash::make($request->password);
            $existingUser->save();
            $user = $existingUser;
        } elseif ($existingUser) {
            // Email đã xác thực – không cho đăng ký lại
            return back()->withErrors(['email' => 'Email này đã được sử dụng.'])->withInput();
        } else {
            // Tạo user mới - Kích hoạt ngay lập tức giống trang web bạn vừa xem
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'password'  => Hash::make($request->password),
                'is_active' => true, // Kích hoạt ngay
                'email_verified_at' => now(), // Coi như đã xác thực để vào được web
            ]);
        }

        // Gửi email chào mừng (Gửi được thì tốt, không gửi được cũng không báo lỗi)
        try {
            $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            Mail::send([], [], function ($message) use ($request, $code, $user) {
                $message->to($request->email)
                    ->subject('🍳 Chào mừng bạn đến với Góc Bếp')
                    ->html($this->buildOtpEmail(
                        $user->name,
                        $code,
                        'Đăng Ký Thành Công',
                        'Cảm ơn bạn đã gia nhập cộng đồng <strong>Góc Bếp</strong>! Tài khoản của bạn đã sẵn sàng sử dụng.',
                        'Chúc bạn có những trải nghiệm tuyệt vời cùng chúng tôi!'
                    ));
            });
        } catch (\Exception $e) {
            \Log::error('Mail Error: ' . $e->getMessage());
        }

        // Đăng nhập và vào thẳng trang chủ
        Auth::login($user);

        return redirect()->route('home')->with('success', 'Đăng ký tài khoản thành công!');
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
        try {
            Mail::send([], [], function ($message) use ($email, $code, $user) {
                $message->to($email)
                    ->subject('🔑 Đặt lại mật khẩu - Góc Bếp')
                    ->html($this->buildOtpEmail(
                        $user->name,
                        $code,
                        'Đặt Lại Mật Khẩu',
                        'Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản <strong>Góc Bếp</strong> của mình. Vui lòng sử dụng mã bên dưới.',
                        'Nếu bạn không yêu cầu đặt lại mật khẩu, vui lòng bỏ qua email này.'
                    ));
            });
        } catch (\Exception $e) {
            \Log::error('Mail Error (Forgot Password): ' . $e->getMessage());
        }

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

        return redirect()->route('home')->with('success', 'Xác thực email thành công! Chào mừng bạn đến với Góc Bếp.');
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
        try {
            Mail::send([], [], function ($message) use ($user, $code) {
                $message->to($user->email)
                    ->subject('🍳 Gửi lại mã xác thực - Góc Bếp')
                    ->html($this->buildOtpEmail(
                        $user->name,
                        $code,
                        'Mã Xác Thực Mới',
                        'Bạn đã yêu cầu gửi lại mã xác thực cho tài khoản <strong>Góc Bếp</strong> của mình.',
                        null
                    ));
            });
        } catch (\Exception $e) {
            \Log::error('Mail Error (Resend Code): ' . $e->getMessage());
        }

        return back()
            ->with('verify_email', $user->email)
            ->with('status', 'Đã gửi lại mã xác thực mới!');
    }

    /**
     * Build a beautiful branded OTP email HTML.
     */
    private function buildOtpEmail(string $name, string $code, string $title, string $body, ?string $footer): string
    {
        $footerHtml = $footer
            ? "<p style='margin:0;color:#9ca3af;font-size:12px;'>{$footer}</p>"
            : '';

        return "
<!DOCTYPE html>
<html lang='vi'>
<head><meta charset='UTF-8'><meta name='viewport' content='width=device-width,initial-scale=1'></head>
<body style='margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;'>
  <table width='100%' cellpadding='0' cellspacing='0' style='background:#f3f4f6;padding:32px 16px;'>
    <tr><td align='center'>
      <table width='560' cellpadding='0' cellspacing='0' style='max-width:560px;width:100%;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);'>

        <!-- HEADER -->
        <tr><td style='background:linear-gradient(135deg,#3E5F4E 0%,#2d4a3a 100%);padding:36px 40px;text-align:center;'>
          <div style='font-size:48px;margin-bottom:8px;'>&#127859;</div>
          <h1 style='margin:0;color:#ffffff;font-size:26px;font-weight:700;letter-spacing:1px;'>Góc Bếp</h1>
          <p style='margin:6px 0 0;color:#a7c4b5;font-size:13px;letter-spacing:2px;text-transform:uppercase;'>Kitchen Corner</p>
        </td></tr>

        <!-- ORANGE ACCENT BAR -->
        <tr><td style='background:#E85D04;height:4px;'></td></tr>

        <!-- BODY -->
        <tr><td style='padding:40px 40px 32px;'>
          <h2 style='margin:0 0 8px;color:#1f2937;font-size:20px;font-weight:700;'>{$title}</h2>
          <p style='margin:0 0 24px;color:#6b7280;font-size:14px;line-height:1.6;'>Xin chào <strong style='color:#1f2937;'>{$name}</strong>,</p>
          <p style='margin:0 0 28px;color:#374151;font-size:15px;line-height:1.7;'>{$body}</p>

          <!-- OTP BOX -->
          <div style='background:#f0fdf4;border:2px dashed #86efac;border-radius:12px;padding:28px 24px;text-align:center;margin-bottom:28px;'>
            <p style='margin:0 0 8px;font-size:12px;color:#6b7280;text-transform:uppercase;letter-spacing:2px;font-weight:600;'>Mã xác thực OTP</p>
            <span style='font-size:44px;font-weight:800;letter-spacing:12px;color:#16a34a;font-family:monospace;'>{$code}</span>
            <p style='margin:12px 0 0;font-size:12px;color:#9ca3af;'>⏰ Mã sẽ hết hiệu lực sau <strong>10 phút</strong></p>
          </div>

          <!-- WARNING -->
          <div style='background:#fffbeb;border-left:4px solid #f59e0b;border-radius:0 8px 8px 0;padding:12px 16px;margin-bottom:24px;'>
            <p style='margin:0;color:#92400e;font-size:13px;'>⚠️ Không chia sẻ mã này với bất kỳ ai. Nhân viên Góc Bếp sẽ không bao giờ yêu cầu mã này.</p>
          </div>
        </td></tr>

        <!-- FOOTER -->
        <tr><td style='background:#f9fafb;border-top:1px solid #e5e7eb;padding:24px 40px;text-align:center;'>
          {$footerHtml}
          <p style='margin:8px 0 0;color:#9ca3af;font-size:12px;'>&copy; 2026 Góc Bếp &mdash; Nơi lưu giữ hương vị cuộc sống</p>
          <p style='margin:4px 0 0;'><span style='display:inline-block;width:8px;height:8px;background:#3E5F4E;border-radius:50%;'></span> <span style='display:inline-block;width:8px;height:8px;background:#E85D04;border-radius:50%;margin-left:2px;'></span> <span style='display:inline-block;width:8px;height:8px;background:#16a34a;border-radius:50%;margin-left:2px;'></span></p>
        </td></tr>

      </table>
    </td></tr>
  </table>
</body>
</html>";
    }
}