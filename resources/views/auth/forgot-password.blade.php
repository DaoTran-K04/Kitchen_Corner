<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu - Góc Sách</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#3E5F4E',
                        'brand-cream': '#FDFBF7',
                        'brand-brown': '#8C6B4B'
                    },
                    fontFamily: {
                        sans: ['Segoe UI', 'Roboto', 'sans-serif'],
                        serif: ['Merriweather', 'serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-brand-cream font-sans min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-brand-green p-6 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-key text-2xl"></i>
            </div>
            <h2 class="text-2xl font-serif font-bold">Quên Mật Khẩu?</h2>
            <p class="text-white/80 text-sm mt-1">Nhập email để nhận mã xác thực</p>
        </div>

        <div class="p-8">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 flex items-center gap-2 text-sm">
                    <i class="fas fa-check-circle"></i>
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Địa chỉ Email</label>
                    @auth
                        {{-- User đã đăng nhập - hiển thị email của họ và khóa input --}}
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-envelope"></i>
                            </span>
                            <input type="email" name="email" 
                                class="w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed" 
                                value="{{ Auth::user()->email }}" readonly>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-info-circle mr-1"></i>
                            Bạn đang đặt lại mật khẩu cho tài khoản này
                        </p>
                    @else
                        {{-- User chưa đăng nhập --}}
                        @php
                            $prefillEmail = request()->query('email');
                        @endphp
                        
                        @if($prefillEmail)
                            {{-- Email được truyền từ trang login - khóa input --}}
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" 
                                    class="w-full pl-10 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-gray-600 cursor-not-allowed" 
                                    value="{{ $prefillEmail }}" readonly>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                <i class="fas fa-info-circle mr-1"></i>
                                Bạn đang đặt lại mật khẩu cho tài khoản này
                            </p>
                        @else
                            {{-- Không có email - cho phép nhập email bất kỳ --}}
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                    <i class="fas fa-envelope"></i>
                                </span>
                                <input type="email" name="email" 
                                    class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" 
                                    placeholder="yourname@gmail.com" required value="{{ old('email') }}">
                            </div>
                        @endif
                    @endauth
                </div>

                <button type="submit" 
                    class="w-full bg-brand-green text-white font-bold py-3 rounded-lg hover:bg-[#2d4a3a] transition transform hover:-translate-y-0.5 shadow-md">
                    <i class="fas fa-paper-plane mr-2"></i>
                    Gửi Mã Xác Thực
                </button>
            </form>

            <div class="text-center mt-6 pt-6 border-t border-gray-100">
                <a href="{{ route('login') }}" class="text-brand-green font-medium hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Quay lại Đăng nhập
                </a>
            </div>
        </div>
    </div>
</body>

</html>