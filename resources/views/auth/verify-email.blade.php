<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác Thực Email - Góc Bếp</title>
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
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            background-color: #f9fafb;
            color: #1f2937;
        }
        .code-input:focus {
            border-color: #9B2226;
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(155, 34, 38, 0.1);
            transform: translateY(-2px);
        }
    </style>
</head>

<body class="font-sans min-h-screen flex items-center justify-center p-4 relative bg-gray-50">
    <!-- Beautiful Full Background Image -->
    <div class="absolute inset-0 z-0">
        <img src="{{ asset('images/auth/kitchen_1.png') }}" alt="Kitchen Background" class="w-full h-full object-cover" />
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    </div>

    <div class="max-w-md w-full bg-white/95 backdrop-blur-xl rounded-3xl shadow-2xl overflow-hidden border border-white/20 relative z-10">
        <div class="p-8 text-center text-white relative overflow-hidden">
            <!-- Header Image -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/auth/kitchen_2.png') }}" alt="Cooking" class="w-full h-full object-cover" />
                <div class="absolute inset-0 bg-brand-green/80"></div>
            </div>
            
            <div class="relative z-10">
                <div class="w-16 h-16 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center mx-auto mb-4 border border-white/30 shadow-inner">
                    <i class="fas fa-envelope-open-text text-2xl drop-shadow-sm"></i>
                </div>
                <h2 class="text-2xl font-serif font-bold tracking-wide drop-shadow-md">Xác Thực Email</h2>
                <p class="text-white/90 text-sm mt-2 font-light">Bảo vệ tài khoản Góc Bếp của bạn</p>
            </div>
        </div>

        <div class="p-8 md:p-10 bg-white">
            @if (session('status'))
                <div class="bg-green-50 text-green-700 border-l-4 border-green-500 text-sm p-4 rounded-r-lg mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fas fa-check-circle mt-0.5 text-green-500"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if ($errors->any())
                <div class="bg-red-50 text-red-600 border-l-4 border-red-500 text-sm p-4 rounded-r-lg mb-6 flex items-start gap-3 shadow-sm">
                    <i class="fas fa-exclamation-circle mt-0.5"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <div class="text-center mb-8">
                <p class="text-gray-600 text-sm bg-gray-50 py-3 px-2 rounded-xl border border-gray-100">
                    Chúng tôi đã gửi mã 6 số đến email:<br>
                    <span class="font-bold text-brand-green text-base inline-block mt-1">{{ Auth::user()->email }}</span>
                </p>
            </div>

            <form method="POST" action="{{ route('verification.verify') }}" id="verify-form" onsubmit="return validateCode()">
                @csrf
                
                {{-- Ô nhập mã 6 số --}}
                <div class="flex justify-center gap-2 sm:gap-3 mb-3">
                    <input type="text" maxlength="1" class="code-input shadow-sm" data-index="0" autofocus inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input shadow-sm" data-index="1" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input shadow-sm" data-index="2" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input shadow-sm" data-index="3" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input shadow-sm" data-index="4" inputmode="numeric">
                    <input type="text" maxlength="1" class="code-input shadow-sm" data-index="5" inputmode="numeric">
                </div>
                
                {{-- Cảnh báo client-side --}}
                <p id="code-warning" class="text-red-500 text-xs text-center mb-6 hidden font-medium">
                    <i class="fas fa-exclamation-triangle mr-1"></i>
                    <span id="code-warning-text">Vui lòng nhập đủ 6 số.</span>
                </p>
                <div class="h-6 mb-2" id="code-spacer"></div> {{-- Spacer to keep layout from jumping --}}
                
                <input type="hidden" name="code" id="full-code">

                <button type="submit" 
                    class="w-full bg-brand-green text-white font-bold text-lg py-3.5 rounded-xl hover:bg-[#7a1a1f] transition duration-300 shadow-[0_4px_14px_0_rgba(155,34,38,0.39)] hover:shadow-[0_6px_20px_rgba(155,34,38,0.23)] transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                    <i class="fas fa-check-circle"></i>
                    Xác nhận
                </button>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-gray-100">
                <p class="text-gray-500 text-sm mb-3">Không nhận được mã?</p>
                <form method="POST" action="{{ route('verification.send') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-brand-accent font-bold hover:text-brand-green transition duration-300 hover:underline inline-flex items-center gap-1.5 focus:outline-none">
                        <i class="fas fa-paper-plane"></i>
                        Gửi lại mã
                    </button>
                </form>
            </div>

            <div class="text-center mt-6">
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-400 hover:text-gray-600 text-sm transition duration-300 flex items-center justify-center gap-2 mx-auto group">
                        <i class="fas fa-sign-out-alt transform group-hover:-translate-x-1 transition duration-300"></i>
                        Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.code-input');
        const fullCodeInput = document.getElementById('full-code');
        const form = document.getElementById('verify-form');
        const warning = document.getElementById('code-warning');
        const spacer = document.getElementById('code-spacer');

        inputs.forEach((input, index) => {
            input.addEventListener('input', (e) => {
                const value = e.target.value;
                e.target.value = value.replace(/[^0-9]/g, '');
                
                if (value && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateFullCode();
            });

            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });

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

        function hideWarning() {
            warning.classList.add('hidden');
            spacer.classList.remove('hidden');
            inputs.forEach(input => {
                input.style.borderColor = '';
            });
        }

        function showWarning(message) {
            const warningText = document.getElementById('code-warning-text');
            warningText.textContent = message;
            warning.classList.remove('hidden');
            spacer.classList.add('hidden');
            inputs.forEach(input => {
                input.style.borderColor = '#ef4444'; // Tailwind red-500
            });
        }

        function validateCode() {
            const code = updateFullCode();
            
            if (code.length === 0) {
                showWarning('Vui lòng nhập mã xác thực.');
                inputs[0].focus();
                return false;
            }
            
            if (code.length < 6) {
                showWarning('Vui lòng nhập đủ 6 số.');
                for (let i = 0; i < inputs.length; i++) {
                    if (!inputs[i].value) {
                        inputs[i].focus();
                        break;
                    }
                }
                return false;
            }
            
            return true;
        }

        inputs.forEach(input => {
            input.addEventListener('focus', hideWarning);
        });
    </script>
</body>

</html>
