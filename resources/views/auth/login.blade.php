<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Góc Sách</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { 'brand-green': '#3E5F4E', 'brand-cream': '#FDFBF7', 'brand-brown': '#8C6B4B' },
                    fontFamily: { sans: ['Segoe UI', 'Roboto', 'sans-serif'], serif: ['Merriweather', 'serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-brand-cream font-sans min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
        <div class="md:w-1/2 bg-brand-green flex flex-col justify-center items-center text-white p-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20" style="background-image: url('https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&q=80&w=800'); background-size: cover;"></div>
            <div class="relative z-10 text-center">
                <h2 class="text-3xl font-serif font-bold mb-2">Chào Mừng Trở Lại</h2>
                <p class="opacity-90">Tiếp tục hành trình khám phá tri thức cùng cộng đồng Góc Sách.</p>
            </div>
        </div>

        <div class="md:w-1/2 p-8 md:p-12">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="text-brand-green text-2xl font-bold flex items-center justify-center gap-2 mb-2">
                    <span class="text-3xl">📚</span>GÓC SÁCH
                </a>
                <h3 class="text-xl font-bold text-gray-800 font-serif">Đăng Nhập</h3>
            </div>

            <form method="POST" action="{{ route('login.post') }}">
                @csrf
                
                @if ($errors->any())
                    <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4 flex items-center gap-2">
                        <i class="fas fa-exclamation-circle"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" placeholder="yourname@gmail.com" required value="{{ old('email') }}">
                    </div>
                </div>

                <div class="mb-6">
                    <div class="flex justify-between items-center mb-2">
                        <label class="text-gray-700 text-sm font-bold">Mật khẩu</label>
                        <a href="#" onclick="goToForgotPassword(event)" class="text-xs text-brand-brown hover:underline" tabindex="-1">Quên mật khẩu?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-lock"></i></span>
                        
                        <input type="password" name="password" id="password" class="w-full pl-10 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" placeholder="••••••••" required>
                        
                        <button type="button" onclick="togglePassword()" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-icon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-green text-white font-bold py-3 rounded-lg hover:bg-[#2C3E36] transition transform hover:-translate-y-0.5 shadow-md">
                    Đăng Nhập
                </button>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-500">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-brand-brown font-bold hover:underline">Đăng ký ngay</a></p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 text-sm text-gray-500 hover:text-brand-green transition">
                        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eye-icon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash'); // Đổi icon thành mắt gạch chéo
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye'); // Đổi lại icon mắt thường
            }
        }

        // Chuyển đến trang quên mật khẩu với email đã nhập
        function goToForgotPassword(event) {
            event.preventDefault();
            const emailInput = document.querySelector('input[name="email"]');
            const email = emailInput.value.trim();
            
            if (email) {
                // Nếu có email, truyền qua query parameter
                window.location.href = "{{ route('password.request') }}?email=" + encodeURIComponent(email);
            } else {
                // Nếu chưa nhập email, vẫn chuyển đến trang forgot password
                window.location.href = "{{ route('password.request') }}";
            }
        }
    </script>
</body>
</html>