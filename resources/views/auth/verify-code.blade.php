<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Mã - Góc Sách</title>
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
    <style>
        .code-input {
            width: 50px;
            height: 60px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            outline: none;
            transition: all 0.2s;
        }
        .code-input:focus {
            border-color: #3E5F4E;
            box-shadow: 0 0 0 3px rgba(62, 95, 78, 0.1);
        }
    </style>
</head>

<body class="bg-brand-cream font-sans min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <div class="bg-brand-green p-6 text-center text-white">
            <div class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3">
                <i class="fas fa-shield-alt text-2xl"></i>
            </div>
            <h2 class="text-2xl font-serif font-bold">Nhập Mã Xác Thực</h2>
            <p class="text-white/80 text-sm mt-1">Chúng tôi đã gửi mã 6 số vào email của bạn</p>
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

            <div class="text-center mb-6">
                <p class="text-gray-600 text-sm">
                    Mã đã được gửi đến <span class="font-bold text-brand-green">{{ session('reset_email') ?? $email ?? '' }}</span>
                </p>
            </div>

            <form method="POST" action="{{ route('password.verify') }}" id="verify-form" onsubmit="return validateCode()">
                @csrf
                <input type="hidden" name="email" value="{{ session('reset_email') ?? $email ?? '' }}">
                
                {{-- Ô nhập mã 6 số --}}
                <div class="flex justify-center gap-2 mb-2">
                    <input type="text" maxlength="1" class="code-input" data-index="0" autofocus inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input" data-index="1" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input" data-index="2" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input" data-index="3" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input" data-index="4" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input" data-index="5" inputmode="numeric">
                </div>
                
                {{-- Cảnh báo client-side --}}
                <p id="code-warning" class="text-red-500 text-xs text-center mb-4 hidden">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="code-warning-text">Vui lòng nhập đủ 6 số.</span>
                </p>
                
                <input type="hidden" name="code" id="full-code">

                <button type="submit" 
                    class="w-full bg-brand-green text-white font-bold py-3 rounded-lg hover:bg-[#2d4a3a] transition transform hover:-translate-y-0.5 shadow-md">
                    <i class="fas fa-check-circle mr-2"></i>
                    Xác Thực
                </button>
            </form>

            <div class="text-center mt-6 pt-6 border-t border-gray-100">
                <p class="text-gray-500 text-sm mb-3">Không nhận được mã?</p>
                <form method="POST" action="{{ route('password.resend') }}" class="inline">
                    @csrf
                    <input type="hidden" name="email" value="{{ session('reset_email') ?? $email ?? '' }}">
                    <button type="submit" class="text-brand-green font-medium hover:underline">
                        <i class="fas fa-redo mr-1"></i>
                        Gửi lại mã
                    </button>
                </form>
            </div>

            <div class="text-center mt-4">
                <a href="{{ route('password.request') }}" class="text-gray-500 text-sm hover:underline">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Quay lại
                </a>
            </div>
        </div>
    </div>

    <script>
        // Auto-focus next input and combine code
        const inputs = document.querySelectorAll('.code-input');
        const fullCodeInput = document.getElementById('full-code');
        const form = document.getElementById('verify-form');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                
                // Chỉ cho phép số
                e.target.value = value.replace(/[^0-9]/g, '');
                
                // Auto-focus next input
                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                
                // Combine all inputs vào hidden field
                updateFullCode();
            });

            input.addEventListener('keydown', (e) => {
                // Backspace: focus previous input
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Paste handler
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').slice(0, 6);
                
                pastedData.split('').forEach((char, i) => {
                    if (inputs[i]) inputs[i].value = char;
                });
                
                updateFullCode();
                if (pastedData.length === 6) {
                    inputs[5].focus();
                }
            });
        });

        function updateFullCode() {
            let code = '';
            inputs.forEach(input => code += input.value);
            fullCodeInput.value = code;
            return code;
        }

        // Ẩn cảnh báo khi người dùng bắt đầu nhập
        function hideWarning() {
            const warning = document.getElementById('code-warning');
            warning.classList.add('hidden');
            inputs.forEach(input => {
                input.classList.remove('border-red-400');
            });
        }

        // Hiện cảnh báo
        function showWarning(message) {
            const warning = document.getElementById('code-warning');
            const warningText = document.getElementById('code-warning-text');
            warningText.textContent = message;
            warning.classList.remove('hidden');
            inputs.forEach(input => {
                input.classList.add('border-red-400');
            });
        }

        // Validate trước khi submit
        function validateCode() {
            const code = updateFullCode();
            
            if (code.length === 0) {
                showWarning('Vui lòng nhập mã xác thực.');
                inputs[0].focus();
                return false;
            }
            
            if (code.length < 6) {
                showWarning('Vui lòng nhập đủ 6 số.');
                // Focus vào ô tiếp theo cần nhập
                for (let i = 0; i < inputs.length; i++) {
                    if (!inputs[i].value) {
                        inputs[i].focus();
                        break;
                    }
                }
                return false;
            }
            
            // Mã đủ 6 số - cho phép submit
            return true;
        }

        // Ẩn warning khi focus vào input
        inputs.forEach(input => {
            input.addEventListener('focus', hideWarning);
        });
    </script>
</body>

</html>
