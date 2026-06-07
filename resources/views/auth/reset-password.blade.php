<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt Lại Mật Khẩu - Góc Bếp</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Nunito+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#9B2226',
                        'brand-cream': '#FAFAF8',
                        'brand-accent': '#E85D04'
                    },
                    fontFamily: {
                        sans: ['Nunito Sans', 'sans-serif'],
                        serif: ['Playfair Display', 'serif']
                    }
                }
            }
        }
    </script>
</head>

<body class="font-sans min-h-screen flex items-center justify-center p-4 relative bg-gray-50">
    <!-- Beautiful Full Background Image -->
    <div class="absolute inset-0 z-0">
        <img loading="lazy" src="{{ asset('images/auth/kitchen_1.png') }}" alt="Kitchen Background" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    </div>

    <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">
        <div class="p-8 text-center text-white relative overflow-hidden">
            <!-- Header Image -->
            <div class="absolute inset-0 z-0">
                <img loading="lazy" src="{{ asset('images/auth/kitchen_2.png') }}" alt="Cooking" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-brand-green/80"></div>
            </div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                    <i class="fas fa-lock-open text-2xl drop-shadow-sm"></i>
                </div>
                <h2 class="text-2xl font-serif font-bold tracking-wide drop-shadow-md">Thiết Lập Mật Khẩu</h2>
                <p class="text-white/90 text-sm mt-2 font-light">Tạo mật khẩu mới và an toàn cho tài khoản của bạn</p>
            </div>
        </div>

        <div class="p-8 md:p-10 bg-white">
            @if ($errors->any())
                <div class="bg-red-50 text-red-600 border-l-4 border-red-500 text-sm p-4 rounded-r-lg mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token ?? '' }}">

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Địa chỉ Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" 
                            class="w-full pl-11 pr-4 py-3 bg-gray-100 border border-gray-200 rounded-xl text-gray-600 cursor-not-allowed shadow-inner focus:outline-none" 
                            value="{{ $email ?? old('email') }}" required readonly>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password"
                            class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white" 
                            placeholder="Tối thiểu 8 ký tự" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-pass')" 
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-icon-pass"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Xác nhận mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white" 
                            placeholder="Nhập lại mật khẩu mới" required>
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-confirm')" 
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-icon-confirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" 
                    class="w-full bg-brand-green text-white font-bold text-lg py-3.5 rounded-xl hover:bg-[#7a1a1f] transition duration-300 shadow-[0_4px_14px_0_rgba(155,34,38,0.39)] hover:shadow-[0_6px_20px_rgba(155,34,38,0.23)] transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-save"></i>
                    Lưu Mật Khẩu
                </button>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('login') }}" class="text-gray-500 hover:text-brand-green font-medium transition duration-300 flex items-center justify-center gap-2 group">
                    <i class="fas fa-arrow-left transform group-hover:-translate-x-1 transition duration-300"></i>
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
                icon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }
    </script>
</body>

</html>
