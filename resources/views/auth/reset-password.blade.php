<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu - Góc Sách</title>
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
        <div class="bg-brand-brown p-6 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-lock-open text-2xl"></i>
            </div>
            <h2 class="text-2xl font-serif font-bold">Đặt Lại Mật Khẩu</h2>
            <p class="text-white/80 text-sm mt-1">Nhập mật khẩu mới cho tài khoản của bạn</p>
        </div>

        <div class="p-8">
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" 
                            class="w-full pl-10 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" 
                            value="{{ $email ?? old('email') }}" required readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" 
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-pass')" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-icon-pass"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Xác nhận mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full pl-10 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" 
                            placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-confirm')" 
                            class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-icon-confirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-brand-brown text-white font-bold py-3 rounded-lg hover:bg-[#6d4c41] transition transform hover:-translate-y-0.5 shadow-md">
                    <i class="fas fa-save mr-2"></i>
                    Đặt Lại Mật Khẩu
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

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>