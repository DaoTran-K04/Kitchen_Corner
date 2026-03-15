<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Góc Sách</title>
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
    <div class="max-w-4xl w-full bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row border border-gray-100">
        <div class="md:w-1/2 bg-brand-brown flex flex-col justify-center items-center text-white p-8 relative overflow-hidden">
            <div class="absolute inset-0 opacity-20" style="background-image: url('https://images.unsplash.com/photo-1481627834876-b7833e8f5570?auto=format&fit=crop&q=80&w=800'); background-size: cover;"></div>
            <div class="relative z-10 text-center">
                <h2 class="text-3xl font-serif font-bold mb-2">Tham Gia Cùng Chúng Tôi</h2>
                <p class="opacity-90">Chia sẻ cảm nhận, kết nối đam mê đọc sách.</p>
            </div>
        </div>

        <div class="md:w-1/2 p-8 md:p-12">
            <div class="text-center mb-6">
                <a href="{{ route('home') }}" class="text-brand-green text-2xl font-bold flex items-center justify-center gap-2 mb-2">
                    <span class="text-3xl">📚</span>GÓC SÁCH
                </a>
                <h3 class="text-xl font-bold text-gray-800 font-serif">Tạo Tài Khoản</h3>
            </div>

            <form method="POST" action="{{ route('register') }}" onsubmit="return validateFormBeforeSubmit()">
                @csrf

                @if ($errors->any())
                <div class="bg-red-50 text-red-600 text-sm p-3 rounded-lg mb-4 flex items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    Kiểm tra lại thông tin nhập bên dưới.
                </div>
                @endif

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1">Họ và Tên</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-user"></i></span>
                        <input type="text" name="name" class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" placeholder="Nguyễn Văn A" required value="{{ old('name') }}">
                    </div>
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1">Email</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-envelope"></i></span>
                        <input type="email" name="email" id="email-input" 
                            class="w-full pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" 
                            placeholder="yourname@gmail.com" required 
                            value="{{ $errors->has('email') ? '' : old('email') }}"
                            onfocus="hideEmailWarning()"
                            onblur="validateEmailOnBlur(this)">
                    </div>
                    {{-- Chỉ 1 cảnh báo duy nhất cho email --}}
                    <p id="email-warning" class="text-red-500 text-xs mt-1 {{ $errors->has('email') ? '' : 'hidden' }}">
                        <i class="fas fa-exclamation-triangle mr-1"></i><span id="email-warning-text">{{ $errors->first('email') ?: 'Chỉ chấp nhận email @gmail.com. Vui lòng sử dụng địa chỉ Gmail.' }}</span>
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-1">Mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="password" class="w-full pl-10 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" placeholder="••••••••" required>

                        <button type="button" onclick="togglePassword('password', 'eye-icon-pass')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-icon-pass"></i>
                        </button>
                    </div>
                    @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-1">Nhập lại mật khẩu</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password_confirmation" id="password_confirmation" class="w-full pl-10 pr-10 py-2 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green transition" placeholder="••••••••" required>

                        <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-confirm')" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer">
                            <i class="fas fa-eye" id="eye-icon-confirm"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="w-full bg-brand-brown text-white font-bold py-3 rounded-lg hover:bg-[#6d4c41] transition transform hover:-translate-y-0.5 shadow-md">
                    Đăng Ký
                </button>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-500">Đã có tài khoản? <a href="{{ route('login') }}" class="text-brand-green font-bold hover:underline">Đăng nhập</a></p>
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-2 mt-4 text-sm text-gray-500 hover:text-brand-green transition">
                        <i class="fas fa-arrow-left"></i> Quay lại trang chủ
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
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Ẩn cảnh báo khi focus vào ô email
        function hideEmailWarning() {
            const warning = document.getElementById('email-warning');
            const input = document.getElementById('email-input');
            warning.classList.add('hidden');
            input.classList.remove('border-red-400');
        }

        // Kiểm tra email khi rời khỏi ô input (giống Gmail/Microsoft)
        function validateEmailOnBlur(input) {
            const warning = document.getElementById('email-warning');
            const warningText = document.getElementById('email-warning-text');
            const value = input.value.trim();
            
            // Nếu ô trống thì không hiện cảnh báo (để browser tự validate required)
            if (value === '') {
                warning.classList.add('hidden');
                input.classList.remove('border-red-400');
                return;
            }
            
            // Kiểm tra xem có phải email @gmail.com không
            const isGmail = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i.test(value);
            
            if (!isGmail) {
                // Cập nhật text cảnh báo (thay thế cảnh báo cũ từ server)
                warningText.textContent = 'Chỉ chấp nhận email @gmail.com. Vui lòng sử dụng địa chỉ Gmail.';
                warning.classList.remove('hidden');
                input.classList.add('border-red-400');
                input.classList.remove('border-gray-200');
            } else {
                warning.classList.add('hidden');
                input.classList.remove('border-red-400');
                input.classList.add('border-gray-200');
            }
        }

        // Validate form trước khi submit - ngăn submit nếu email sai (giữ lại mật khẩu)
        function validateFormBeforeSubmit() {
            const emailInput = document.getElementById('email-input');
            const warning = document.getElementById('email-warning');
            const warningText = document.getElementById('email-warning-text');
            const value = emailInput.value.trim();
            
            // Kiểm tra email có phải @gmail.com không
            const isGmail = /^[a-zA-Z0-9._%+-]+@gmail\.com$/i.test(value);
            
            if (!isGmail && value !== '') {
                // Hiện cảnh báo và ngăn submit
                warningText.textContent = 'Chỉ chấp nhận email @gmail.com. Vui lòng sử dụng địa chỉ Gmail.';
                warning.classList.remove('hidden');
                emailInput.classList.add('border-red-400');
                emailInput.focus();
                return false; // Ngăn form submit
            }
            
            return true; // Cho phép submit
        }
    </script>
</body>

</html>