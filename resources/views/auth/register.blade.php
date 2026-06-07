<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Góc Bếp</title>
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

<body class="bg-brand-cream font-sans min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row-reverse border border-amber-50">
        <!-- Left Side - Decorative & Branding -->
        <div class="md:w-1/2 bg-brand-green flex flex-col justify-center items-center text-white p-10 relative overflow-hidden group order-1 md:order-1">
            <!-- Kitchen Background Image -->
            <div class="absolute inset-0 z-0">
                <img loading="lazy" src="{{ asset('images/auth/kitchen_2.png') }}" alt="Cooking Preparation" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/30 transition duration-500 group-hover:bg-black/20"></div>
                <div class="absolute inset-0 bg-brand-green/20 mix-blend-multiply"></div>
            </div>

            <div class="relative z-20 text-center text-white drop-shadow-md">
                <div class="w-16 h-16 border-2 border-white/80 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-sm">
                    <i class="fas fa-heart text-2xl"></i>
                </div>
                <h2 class="text-3xl font-serif font-bold mb-3 tracking-wide drop-shadow-md">Gia Nhập Cộng Đồng</h2>
                <p class="text-white/90 font-light leading-relaxed drop-shadow-md">Nơi chia sẻ những công thức tuyệt vời, kết nối tình yêu ẩm thực của chúng ta.</p>
            </div>
        </div>

        <!-- Form Section -->
        <div class="md:w-1/2 p-10 md:p-12">
            <div class="text-center mb-8">
                <a href="{{ route('home') }}" class="text-brand-green text-2xl font-bold flex items-center justify-center gap-2 mb-2 hover:opacity-80 transition duration-300">
                    <span class="text-3xl filter drop-shadow-sm">🍳</span>
                    <span class="tracking-wider">GÓC BẾP</span>
                </a>
                <h3 class="text-xl font-semibold text-gray-800 font-serif mt-4">Tạo Tài Khoản Mới</h3>
            </div>

            <form method="POST" action="{{ route('register') }}" onsubmit="return validateFormBeforeSubmit()">
                @csrf

                @if ($errors->any())
                <div class="bg-red-50 text-red-600 border-l-4 border-red-500 text-sm p-4 rounded-r-lg mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <span>Tạo tài khoản chưa thành công, vui lòng kiểm tra lại thông tin.</span>
                </div>
                @endif

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Họ và Tên</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-user"></i>
                        </span>
                        <input type="text" name="name" class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white" placeholder="Nguyễn Văn A" required value="{{ old('name') }}">
                    </div>
                    @error('name') <p class="text-brand-green text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Địa chỉ Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-envelope"></i>
                        </span>
                        <input type="email" name="email" id="email-input" 
                            class="w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white {{ $errors->has('email') ? 'border-red-400' : '' }}" 
                            placeholder="yourname@gmail.com" required 
                            value="{{ $errors->has('email') ? '' : old('email') }}"
                            onfocus="hideEmailWarning()"
                            onblur="validateEmailOnBlur(this)">
                    </div>
                    <p id="email-warning" class="text-brand-green text-xs mt-1 {{ $errors->has('email') ? '' : 'hidden' }}">
                        <i class="fas fa-exclamation-triangle mr-1"></i><span id="email-warning-text">{{ $errors->first('email') ?: 'Hệ thống hiện tại chỉ hỗ trợ email có đuôi @gmail.com.' }}</span>
                    </p>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" name="password" id="password" class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('password', 'eye-icon-pass')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-icon-pass"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-brand-green text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Nhập lại mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-check-circle"></i>
                        </span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white" placeholder="••••••••" required>
                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-confirm')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-icon-confirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-accent text-white font-bold text-lg py-3.5 rounded-xl hover:bg-[#c95003] transition duration-300 shadow-[0_4px_14px_0_rgba(232,93,4,0.39)] hover:shadow-[0_6px_20px_rgba(232,93,4,0.23)] transform hover:-translate-y-0.5">
                    Đăng Ký
                </button>

                <div class="text-center mt-8 space-y-4 pt-6 mt-6 border-t border-gray-100">
                    <p class="text-gray-600">Đã có tài khoản? <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline transition duration-300">Đăng nhập</a></p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-green transition duration-300 group">
                        <i class="fas fa-arrow-left transform group-hover:-translate-x-1 transition duration-300"></i> Trở về Khám phá
                    </a>
                </div>
            </form>
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

        function hideEmailWarning() {
            const warning = document.getElementById('email-warning');
            const input = document.getElementById('email-input');
            warning.classList.add('hidden');
            input.classList.remove('border-red-400');
            input.classList.add('focus:border-brand-green');
        }

        function validateEmailOnBlur(input) {
            const warning = document.getElementById('email-warning');
            const warningText = document.getElementById('email-warning-text');
            const value = input.value.trim();
            
            if (value === '') {
                warning.classList.add('hidden');
                input.classList.remove('border-red-400');
                return;
            }
            
            const isGmail = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i.test(value);
            
            if (!isGmail) {
                warningText.textContent = 'Hệ thống hiện tại chỉ hỗ trợ email có đuôi @gmail.com.';
                warning.classList.remove('hidden');
                input.classList.add('border-red-400');
                input.classList.remove('border-gray-200');
            } else {
                warning.classList.add('hidden');
                input.classList.remove('border-red-400');
                input.classList.add('border-gray-200');
            }
        }

        function validateFormBeforeSubmit() {
            const emailInput = document.getElementById('email-input');
            const warning = document.getElementById('email-warning');
            const warningText = document.getElementById('email-warning-text');
            const value = emailInput.value.trim();
            
            const isGmail = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i.test(value);
            
            if (!isGmail && value !== '') {
                warningText.textContent = 'Hệ thống hiện tại chỉ hỗ trợ email có đuôi @gmail.com.';
                warning.classList.remove('hidden');
                emailInput.classList.add('border-red-400');
                emailInput.focus();
                return false; 
            }
            return true;
        }
    </script>
</body>
</html>
