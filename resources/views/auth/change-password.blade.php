<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Đổi Mật Khẩu - Góc Bếp</title>
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
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        @keyframes slideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
        .animate-slide-out { animation: slideOut 0.3s ease-in forwards; }
    </style>
</head>

<body class="bg-brand-cream font-sans min-h-screen flex items-center justify-center p-4">
    <div class="max-w-4xl w-full bg-white rounded-3xl shadow-2xl overflow-hidden flex flex-col md:flex-row border border-amber-50">
        <!-- Left Side - Decorative -->
        <div class="md:w-1/2 bg-brand-green flex flex-col justify-center items-center text-white p-10 relative overflow-hidden group">
            <!-- Kitchen Background Image -->
            <div class="absolute inset-0 z-0">
                <img src="{{ asset('images/auth/kitchen_3.png') }}" alt="Kitchen Spices" class="w-full h-full object-cover transition duration-700 group-hover:scale-105" />
                <div class="absolute inset-0 bg-black/30 transition duration-500 group-hover:bg-black/20"></div>
                <div class="absolute inset-0 bg-brand-green/20 mix-blend-multiply"></div>
            </div>

            <div class="relative z-20 w-full max-w-md text-center transform transition duration-500 group-hover:scale-105">
                <div class="w-20 h-20 border-2 border-white/80 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-md bg-white/10 shadow-inner">
                    <i class="fas fa-shield-alt text-4xl drop-shadow-sm"></i>
                </div>
                <h2 class="text-3xl font-serif font-bold mb-3 tracking-wide drop-shadow-md">Bảo Mật Góc Bếp</h2>
                <p class="text-white/90 font-light leading-relaxed drop-shadow-md">Giữ an toàn cho góc nhỏ của bạn. Đổi mật khẩu định kỳ là một thói quen tốt.</p>
                
                <div class="mt-8 space-y-3 text-sm font-light opacity-90 text-left w-max mx-auto bg-black/20 p-5 rounded-2xl backdrop-blur-sm border border-white/10">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check text-brand-accent"></i>
                        <span>Mật khẩu tối thiểu 8 ký tự</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check text-brand-accent"></i>
                        <span>Bao gồm cả chữ và số</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check text-brand-accent"></i>
                        <span>Tránh những thông tin dễ đoán</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side - Form -->
        <div class="md:w-1/2 p-10 md:p-12">
            <div class="text-center mb-10">
                <h3 class="text-2xl font-bold text-gray-800 font-serif">Đổi Mật Khẩu</h3>
                <div class="w-16 h-1 bg-brand-green mx-auto my-4 rounded-full"></div>
                <p class="text-gray-500 text-sm">Xin chào, <strong class="text-brand-green">{{ Auth::user()->name ?? 'Người dùng' }}</strong></p>
            </div>

            <form id="change-password-form">
                @csrf

                <!-- Message Container -->
                <div id="message-container"></div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Mật khẩu hiện tại</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-unlock-alt"></i>
                        </span>
                        <input type="password" name="current_password" id="current_password"
                            class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white"
                            placeholder="Nhập mật khẩu hiện tại" required>
                        <button type="button" onclick="togglePassword('current_password', 'eye-1')"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-1"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-key"></i>
                        </span>
                        <input type="password" name="new_password" id="new_password"
                            class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white"
                            placeholder="Tối thiểu 8 ký tự" required>
                        <button type="button" onclick="togglePassword('new_password', 'eye-2')"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-2"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-8">
                    <label class="block text-gray-700 text-sm font-semibold mb-2">Xác nhận mật khẩu mới</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                            <i class="fas fa-check-double"></i>
                        </span>
                        <input type="password" name="new_password_confirmation" id="new_password_confirmation"
                            class="w-full pl-11 pr-12 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-800 transition duration-300 focus:outline-none focus:border-brand-green focus:ring-1 focus:ring-brand-green focus:bg-white"
                            placeholder="Nhập lại mật khẩu mới" required>
                        <button type="button" onclick="togglePassword('new_password_confirmation', 'eye-3')"
                            class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-brand-green focus:outline-none cursor-pointer transition duration-300">
                            <i class="fas fa-eye" id="eye-3"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-3">
                    <button type="submit" id="submit-btn"
                        class="flex-[2] bg-brand-green text-white font-bold py-3.5 rounded-xl hover:bg-[#7a1a1f] transition duration-300 shadow-[0_4px_14px_0_rgba(155,34,38,0.39)] hover:shadow-[0_6px_20px_rgba(155,34,38,0.23)] transform hover:-translate-y-0.5 flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Lưu Thay Đổi
                    </button>
                    <a href="{{ route('profile') }}"
                        class="flex-1 text-center bg-white text-gray-700 font-bold py-3.5 rounded-xl hover:bg-gray-50 transition border border-gray-200 shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </form>

            <div class="text-center mt-8 pt-6 border-t border-gray-100 flex justify-between items-center text-sm">
                <a href="{{ route('password.request') }}" class="text-gray-500 hover:text-brand-accent transition duration-300">
                    <i class="fas fa-question-circle mr-1"></i> Quên mật khẩu?
                </a>
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-brand-green transition duration-300 group">
                    Về Trang Chủ <i class="fas fa-arrow-right ml-1 transform group-hover:translate-x-1 transition duration-300"></i>
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

        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('change-password-form');
            const messageContainer = document.getElementById('message-container');
            const submitBtn = document.getElementById('submit-btn');

            function showToast(message, isError = true) {
                const existingToast = document.getElementById('toast-notification');
                if (existingToast) existingToast.remove();

                const bgColor = isError ? 'bg-red-500' : 'bg-green-500';
                const icon = isError ? 'fa-exclamation-circle' : 'fa-check-circle';

                const toast = document.createElement('div');
                toast.id = 'toast-notification';
                toast.className = `fixed top-4 right-4 ${bgColor} text-white px-5 py-4 rounded-xl shadow-2xl z-50 flex items-center gap-3 text-sm max-w-sm animate-slide-in font-medium`;
                toast.innerHTML = `
                    <i class="fas ${icon} text-xl"></i>
                    <span>${message}</span>
                    <button onclick="this.parentElement.remove()" class="ml-auto hover:text-white/80 transition focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                document.body.appendChild(toast);

                setTimeout(() => {
                    if (toast.parentElement) {
                        toast.classList.add('animate-slide-out');
                        setTimeout(() => toast.remove(), 300);
                    }
                }, 5000);
            }

            form.addEventListener('submit', async function (e) {
                e.preventDefault();

                // Validation check client-side
                const newPass = document.getElementById('new_password').value;
                const newPassConfirm = document.getElementById('new_password_confirmation').value;
                
                if (newPass !== newPassConfirm) {
                    showToast('Đã xảy ra lỗi: Mật khẩu xác nhận không khớp.', true);
                    return;
                }

                submitBtn.disabled = true;
                const originalBtnContent = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Đang xử lý...';
                messageContainer.innerHTML = '';

                const formData = new FormData(form);

                try {
                    const response = await fetch('{{ route("change.password.post") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    const data = await response.json();

                    if (response.ok && data.success) {
                        messageContainer.innerHTML = `
                            <div class="bg-green-50 text-green-700 p-4 rounded-xl mb-6 border-l-4 border-green-500 shadow-sm text-sm">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-check-circle text-green-500 text-lg"></i>
                                        <span class="font-medium">${data.message || 'Đổi mật khẩu thành công!'}</span>
                                    </div>
                                    <span class="sm:ml-auto text-xs bg-white px-3 py-1.5 rounded-lg border border-green-100 shadow-sm whitespace-nowrap">
                                        Về trang chủ sau <strong id="countdown" class="text-green-600">3</strong>s
                                    </span>
                                </div>
                            </div>
                        `;

                        form.reset();

                        let seconds = 3;
                        const countdownEl = document.getElementById('countdown');
                        const countdownInterval = setInterval(function () {
                            seconds--;
                            countdownEl.textContent = seconds;
                            if (seconds <= 0) {
                                clearInterval(countdownInterval);
                                window.location.href = '{{ route("home") }}';
                            }
                        }, 1000);

                    } else {
                        let errorMessage = 'Có lỗi xảy ra, vui lòng thử lại.';
                        if (data.errors) {
                            for (const field in data.errors) {
                                errorMessage = data.errors[field][0];
                                break;
                            }
                        } else if (data.message) {
                            errorMessage = data.message;
                        }
                        showToast(errorMessage, true);
                    }
                } catch (error) {
                    showToast('Không thể kết nối đến máy chủ. Vui lòng thử lại.', true);
                }

                if (!document.getElementById('countdown')) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnContent;
                }
            });
        });
    </script>
</body>

</html>
